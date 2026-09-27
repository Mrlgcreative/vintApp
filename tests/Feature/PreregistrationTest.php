<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\UserWaiting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreregistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::updateOrCreate(['key' => 'preregistration_enabled'], ['value' => '1', 'type' => 'boolean', 'category' => 'preregistration', 'label' => 'x', 'is_public' => true]);
    }

    public function test_default_value_of_setting(): void
    {
        $this->assertNotNull(Setting::where('key', 'preregistration_enabled')->first());
    }

    public function test_enabled_shows_form(): void
    {
        $r = $this->get('/preregistration');
        $r->assertOk();
        $r->assertSee('preregistrationForm', false);
    }

    public function test_disabled_shows_closed_page(): void
    {
        Setting::where('key', 'preregistration_enabled')->update(['value' => '0']);
        $r = $this->get('/preregistration');
        $r->assertOk();
        $r->assertDontSee('preregistrationForm', false);
    }

    public function test_limit_reached(): void
    {
        Setting::updateOrCreate(['key' => 'preregistration_limit'], ['value' => '1', 'type' => 'integer', 'category' => 'preregistration', 'label' => 'x', 'is_public' => true]);
        UserWaiting::create(['name' => 'a', 'email' => 'a@b.c', 'country' => 'CD', 'status' => 'pending', 'confirmation_token' => 'tok_a']);
        $r = $this->get('/preregistration');
        $r->assertOk();
        $r->assertDontSee('preregistrationForm', false);
    }

    public function test_store_creates_record(): void
    {
        $r = $this->postJson('/preregistration', [
            'name' => 'Test',
            'email' => 'test@ex.com',
            'phone' => '0812345678',
            'country' => 'CD',
            'reasons' => ['Acheter des produits vintage de qualité'],
            'firebase_uid' => 'abc123',
        ]);
        $r->assertStatus(201);
        $this->assertDatabaseHas('users_waiting', ['email' => 'test@ex.com']);
        $this->assertNotNull(UserWaiting::where('email', 'test@ex.com')->first()->email_confirmed_at);
    }

    public function test_store_refused_when_disabled(): void
    {
        Setting::where('key', 'preregistration_enabled')->update(['value' => '0']);
        $r = $this->postJson('/preregistration', [
            'name' => 'Test', 'email' => 'x@ex.com', 'country' => 'CD', 'firebase_uid' => 'u1',
        ]);
        $r->assertStatus(403);
    }
}
