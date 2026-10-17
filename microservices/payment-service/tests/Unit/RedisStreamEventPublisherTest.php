<?php

namespace Tests\Unit;

use App\Models\OutboxMessage;
use App\Services\RedisStreamEventPublisher;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * Le publisher Redis est le seul morceau qui parle au bus. Il est testé avec
 * un double plutôt qu'avec un Redis réel, pour verrouiller deux points :
 * l'appel `xadd` et le fait qu'une panne remonte au relay (qui reprogramme
 * le message) au lieu d'être avalée.
 */
class RedisStreamEventPublisherTest extends TestCase
{
    private function message(string $type = 'payment.completed'): OutboxMessage
    {
        return new OutboxMessage([
            'event_id' => (string) Str::uuid(),
            'type' => $type,
            'stream' => 'vintapp.payment',
            'payload' => ['payment_id' => 42, 'amount' => 5000],
        ]);
    }

    public function test_publishes_to_the_message_stream(): void
    {
        $message = $this->message();

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('xadd')
            ->once()
            ->withArgs(function ($stream, $id, $fields, $maxLength, $approximate) use ($message) {
                $this->assertSame('vintapp.payment', $stream);
                $this->assertSame('*', $id);
                $this->assertSame(10000, $maxLength);
                $this->assertSame('approximate', $approximate);

                $decoded = json_decode($fields['payload'], true);

                $this->assertSame($message->event_id, $decoded['event_id']);
                $this->assertSame('payment.completed', $decoded['type']);
                $this->assertArrayHasKey('occurred_at', $decoded);
                $this->assertSame(5000, $decoded['data']['amount']);

                return true;
            })
            ->andReturn('1-1');

        Redis::shouldReceive('connection')->with('default')->andReturn($connection);

        $publisher = new RedisStreamEventPublisher('default', 10000);

        $publisher->publish($message);
    }

    public function test_truncates_stream_to_configured_length(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('xadd')
            ->once()
            ->with('vintapp.payment', '*', Mockery::any(), 250, 'approximate')
            ->andReturn('1-1');

        Redis::shouldReceive('connection')->with('bus')->andReturn($connection);

        (new RedisStreamEventPublisher('bus', 250))->publish($this->message());
    }

    public function test_connection_failure_bubbles_up_so_the_message_stays_pending(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('xadd')
            ->once()
            ->andThrow(new RuntimeException('Connection refused'));

        Redis::shouldReceive('connection')->andReturn($connection);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Publication Redis échouée');

        (new RedisStreamEventPublisher('default', 10000))->publish($this->message());
    }

    public function test_publisher_reports_its_name(): void
    {
        $this->assertSame('redis-stream', (new RedisStreamEventPublisher('default', 100))->name());
    }
}
