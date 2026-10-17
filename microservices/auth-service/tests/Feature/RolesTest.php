<?php

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_seeder_creates_all_roles(): void
    {
        foreach (['admin', 'user', 'vendeur', 'expert'] as $slug) {
            $this->assertDatabaseHas('roles', ['slug' => $slug]);
        }
    }
}
