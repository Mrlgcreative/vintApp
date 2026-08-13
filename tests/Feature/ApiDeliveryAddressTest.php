<?php

namespace Tests\Feature;

use App\Models\DeliveryAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiDeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'John Doe',
            'phone' => '+243812345678',
            'email' => 'john@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => 'Av. de la Paix 12',
            'latitude' => -4.325,
            'longitude' => 15.308,
            'is_default' => true,
        ], $overrides);
    }

    /** @test */
    public function user_can_create_and_list_delivery_addresses()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/delivery-addresses', $this->payload())
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/delivery-addresses')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function first_address_becomes_default()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/delivery-addresses', $this->payload(['is_default' => false]))
            ->assertStatus(201);

        $address = $user->deliveryAddresses()->first();
        $this->assertTrue($address->is_default);
    }

    /** @test */
    public function user_cannot_see_others_address()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $address = $other->deliveryAddresses()->create($this->payload());

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/delivery-addresses/' . $address->id)
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function user_can_update_address()
    {
        $user = User::factory()->create();
        $address = $user->deliveryAddresses()->create($this->payload());
        Sanctum::actingAs($user);

        $this->putJson('/api/v1/delivery-addresses/' . $address->id, $this->payload([
            'full_name' => 'Jane Doe',
        ]))
            ->assertOk()
            ->assertJsonPath('data.full_name', 'Jane Doe');
    }

    /** @test */
    public function user_can_set_default_and_delete_address()
    {
        $user = User::factory()->create();
        $first = $user->deliveryAddresses()->create($this->payload(['is_default' => true]));
        $second = $user->deliveryAddresses()->create($this->payload(['is_default' => false]));
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/delivery-addresses/' . $second->id . '/default')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue($second->fresh()->is_default);
        $this->assertFalse($first->fresh()->is_default);

        $this->deleteJson('/api/v1/delivery-addresses/' . $second->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('delivery_addresses', ['id' => $second->id]);
        $this->assertTrue($first->fresh()->is_default);
    }

    /** @test */
    public function validation_fails_without_required_fields()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/delivery-addresses', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function guest_cannot_access_delivery_addresses()
    {
        $this->getJson('/api/v1/delivery-addresses')->assertStatus(401);
        $this->postJson('/api/v1/delivery-addresses', $this->payload())->assertStatus(401);
    }
}
