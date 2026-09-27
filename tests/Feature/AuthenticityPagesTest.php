<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use App\Models\Category;
use App\Models\Item;
use App\Models\ProductAuthenticityCheck;
use App\Models\User;
use App\Models\Wallet;
use App\Models\VerificationImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthenticityPagesTest extends TestCase
{
    use RefreshDatabase;

    private function ownerWithItem(array $attributes = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create([
            'name' => 'Montres',
            'slug' => 'montres',
            'is_active' => true,
        ]);

        $item = Item::create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Rolex Datejust',
            'description' => 'Montre en bon etat.',
            'price' => 250,
            'status' => 'active',
        ], $attributes));

        return [$user, $item];
    }

    private function mainWallet(User $user, float $balance = 50.0): Wallet
    {
        return Wallet::create([
            'user_id' => $user->id,
            'type' => Wallet::TYPE_MAIN,
            'currency' => 'USD',
            'balance' => $balance,
        ]);
    }

    public function test_dashboard_renders_with_stats_and_filter_tabs(): void
    {
        [$user] = $this->ownerWithItem();

        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.dashboard'))
            ->assertOk()
            ->assertSee('Mes certifications')
            ->assertSee('Demandes envoyées')
            ->assertSee(route('authenticity.dashboard', ['status' => 'in_progress']), false)
            ->assertSee('Aucune certification demandée');
    }

    public function test_dashboard_filter_narrows_the_list_without_changing_totals(): void
    {
        [$user, $item] = $this->ownerWithItem();

        ProductAuthenticityCheck::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => ProductAuthenticityCheck::STATUS_PENDING,
            'verification_fee' => 5,
        ]);

        $approved = ProductAuthenticityCheck::create([
            'item_id' => Item::create([
                'user_id' => $user->id,
                'category_id' => $item->category_id,
                'name' => 'Omega Speedmaster',
                'description' => 'Montrelegendaire.',
                'price' => 300,
                'status' => 'active',
            ])->id,
            'user_id' => $user->id,
            'status' => ProductAuthenticityCheck::STATUS_EXPERT_APPROVED,
            'verification_fee' => 10,
        ]);

        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.dashboard', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Omega Speedmaster')
            ->assertSee('Approuvées')
            // Le total des demandes reste global, non filtré
            ->assertSee('2', false);
    }

    public function test_request_page_exposes_the_fee_of_the_category(): void
    {
        [$user, $item] = $this->ownerWithItem();

        // Catégorie "montres" => multiplicateur 2 sur une base de 5 USD
        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.request', $item))
            ->assertOk()
            ->assertSee('10.00')
            ->assertSee(route('authenticity.submit', $item), false)
            ->assertSee('name="product_images[]"', false)
            ->assertSee('name="terms_accepted"', false)
            ->assertSee('data-step-panel="4"', false);
    }

    public function test_request_form_creates_a_check_and_saves_images(): void
    {
        Storage::fake('public');

        [$user, $item] = $this->ownerWithItem();

        $response = $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->post(route('authenticity.submit', $item), [
                'product_images' => [
                    UploadedFile::fake()->image('face.jpg'),
                    UploadedFile::fake()->image('back.jpg'),
                    UploadedFile::fake()->image('side.jpg'),
                ],
                'certificate' => UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf'),
                'serial_number' => 'ABC123456',
                'purchase_date' => '2026-01-15',
                'purchase_location' => 'Boutique officielle',
                'additional_notes' => 'Emballage d’origine.',
                'terms_accepted' => 1,
            ]);

        $response->assertSessionHasNoErrors();

        $check = ProductAuthenticityCheck::where('item_id', $item->id)->firstOrFail();

        $response->assertRedirect(route('authenticity.payment', $check));

        $this->assertSame($user->id, $check->user_id);
        $this->assertSame(ProductAuthenticityCheck::STATUS_PENDING, $check->status);
        $this->assertFalse((bool) $check->payment_completed);
        $this->assertEquals(10.0, (float) $check->verification_fee);

        $this->assertSame(4, VerificationImage::where('authenticity_check_id', $check->id)->count());
    }

    public function test_request_form_rejects_an_item_that_is_not_owned(): void
    {
        [, $item] = $this->ownerWithItem();
        $stranger = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($stranger)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.request', $item))
            ->assertForbidden();
    }

    public function test_payment_page_shows_wallet_balance_and_blocks_when_insufficient(): void
    {
        [$user, $item] = $this->ownerWithItem();

        $check = ProductAuthenticityCheck::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => ProductAuthenticityCheck::STATUS_PENDING,
            'verification_fee' => 10,
        ]);

        $this->mainWallet($user, 2.0);

        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.payment', $check))
            ->assertOk()
            ->assertSee('Solde insuffisant')
            ->assertSee(route('wallet.index'), false)
            ->assertSee('2.00');
    }

    public function test_status_page_shows_the_timeline_and_the_expert_notes(): void
    {
        [$user, $item] = $this->ownerWithItem();

        $check = ProductAuthenticityCheck::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => ProductAuthenticityCheck::STATUS_EXPERT_REVIEW,
            'verification_fee' => 10,
            'payment_completed' => true,
            'expert_notes' => 'Les gravures sont cohérentes.',
        ]);

        VerificationImage::create([
            'authenticity_check_id' => $check->id,
            'image_path' => 'verification/front.jpg',
            'image_type' => VerificationImage::TYPE_PRODUCT_FRONT,
            'image_quality_score' => 88,
        ]);

        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.status', $item))
            ->assertOk()
            ->assertSee('Vérification en cours')
            ->assertSee('Analyse par IA')
            ->assertSee('Décision finale')
            ->assertSee('Les gravures sont cohérentes.')
            ->assertSee('Vue de face')
            ->assertSee('88');
    }
}
