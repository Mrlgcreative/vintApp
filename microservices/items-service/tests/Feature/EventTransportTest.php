<?php

namespace Tests\Feature;

use App\Contracts\EventPublisher;
use App\Models\Item;
use App\Models\OutboxMessage;
use App\Services\ItemService;
use App\Services\NullEventPublisher;
use App\Services\OutboxRelay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class EventTransportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // L'outbox est la source de vérité : les tests portent sur son contenu,
        // pas sur la présence d'un Redis.
        $this->app->instance(EventPublisher::class, $this->app->make(NullEventPublisher::class));
    }

    public function test_creating_item_writes_outbox_message_in_same_transaction(): void
    {
        $category = $this->makeCategory();

        $item = $this->app->make(ItemService::class)->create([
            'name' => 'Montre',
            'description' => 'Belle montre',
            'price' => 100,
            'category_id' => $category->id,
        ], 42);

        $message = OutboxMessage::query()->sole();

        $this->assertSame('item.created', $message->type);
        $this->assertSame('vintapp.catalog', $message->stream);
        $this->assertSame($item->public_id, $message->payload['item_id']);
        $this->assertSame(42, $message->payload['seller_id']);
        $this->assertEquals(100, $message->payload['price']);
        $this->assertNull($message->published_at);
        $this->assertSame(0, $message->attempts);
        $this->assertTrue(Str::isUuid($message->event_id));
    }

    public function test_rolled_back_creation_does_not_leave_item_nor_event(): void
    {
        $category = $this->makeCategory();

        /*
         * Échec métier survenant après la création : le rollback doit emporter
         * l'article ET son événement.
         */
        try {
            DB::transaction(function () use ($category) {
                $this->app->make(ItemService::class)->create([
                    'name' => 'Montre',
                    'description' => 'Belle montre',
                    'price' => 100,
                    'category_id' => $category->id,
                ], 42);

                throw new RuntimeException('échec métier après création');
            });

            $this->fail('La transaction aurait dû être annulée.');
        } catch (RuntimeException $e) {
            $this->assertSame('échec métier après création', $e->getMessage());
        }

        $this->assertSame(0, Item::query()->count());
        $this->assertSame(0, OutboxMessage::query()->count());
    }

    public function test_update_emits_changed_keys(): void
    {
        $item = $this->makeItem();

        $this->app->make(ItemService::class)->update($item, ['price' => 555]);

        $message = OutboxMessage::query()->where('type', 'item.updated')->sole();

        $this->assertContains('price', $message->payload['changed']);
        $this->assertEquals(555, $message->payload['price']);
        $this->assertSame($item->public_id, $message->payload['item_id']);
    }

    public function test_delete_emits_event_after_removal(): void
    {
        $item = $this->makeItem();

        $this->app->make(ItemService::class)->delete($item);

        $message = OutboxMessage::query()->where('type', 'item.deleted')->sole();

        $this->assertSame($item->public_id, $message->payload['item_id']);
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_relay_publishes_pending_messages_and_marks_them(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'item.created',
            'stream' => 'vintapp.catalog',
            'payload' => ['item_id' => 'X'],
            'available_at' => now(),
        ]);

        $published = [];

        $this->app->instance(EventPublisher::class, new class($published) implements EventPublisher
        {
            public function __construct(private array &$published) {}

            public function publish(OutboxMessage $message): void
            {
                $this->published[] = $message->event_id;
            }

            public function name(): string
            {
                return 'spy';
            }
        });

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(1, $result['published']);
        $this->assertSame(0, $result['failed']);
        $this->assertSame([$message->event_id], $published);
        $this->assertNotNull($message->fresh()->published_at);
    }

    public function test_relay_keeps_message_when_publisher_throws(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'item.created',
            'stream' => 'vintapp.catalog',
            'payload' => ['item_id' => 'X'],
            'available_at' => now(),
        ]);

        $this->app->instance(EventPublisher::class, new class implements EventPublisher
        {
            public function publish(OutboxMessage $message): void
            {
                throw new RuntimeException('redis indisponible');
            }

            public function name(): string
            {
                return 'broken';
            }
        });

        config()->set('events.outbox.backoff_seconds', 60);

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(0, $result['published']);
        $this->assertSame(1, $result['failed']);

        $fresh = $message->fresh();

        $this->assertNull($fresh->published_at);
        $this->assertSame(1, $fresh->attempts);
        $this->assertStringContainsString('redis indisponible', $fresh->last_error);
        $this->assertTrue($fresh->available_at->isFuture());
    }

    public function test_relay_skips_backed_off_message(): void
    {
        $message = OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => 'item.created',
            'stream' => 'vintapp.catalog',
            'payload' => ['item_id' => 'X'],
            'available_at' => now()->addMinutes(10),
        ]);

        $this->app->instance(EventPublisher::class, new class implements EventPublisher
        {
            public function publish(OutboxMessage $message): void
            {
                throw new RuntimeException('ne devrait pas être appelé');
            }

            public function name(): string
            {
                return 'never';
            }
        });

        $result = $this->app->make(OutboxRelay::class)->flush();

        $this->assertSame(0, $result['published']);
        $this->assertSame(0, $result['failed']);
        $this->assertSame(0, $message->fresh()->attempts);
    }

    public function test_unknown_publisher_driver_fails_loudly(): void
    {
        config()->set('events.publisher', 'kafka-typo');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('EVENT_PUBLISHER inconnu');

        $this->app->forgetInstance(EventPublisher::class);
        $this->app->make(EventPublisher::class);
    }
}
