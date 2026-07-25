<?php

namespace Tests\Feature;

use Database\Seeders\OTPTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OTPTestSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_procuradores_and_users_are_created_without_duplicates_when_seeding_twice(): void
    {
        $this->seed(OTPTestSeeder::class);
        $this->seed(OTPTestSeeder::class);

        $this->assertDatabaseCount('procuradores', 2);
        $this->assertDatabaseCount('usuarios', 4);
        $this->assertDatabaseHas('procuradores', ['procurador_email' => '1240245@usap.edu']);
        $this->assertDatabaseHas('usuarios', ['email' => '1240245@usap.edu']);
        $this->assertDatabaseHas('usuarios', ['email' => '3180215@usap.edu']);
        $this->assertDatabaseHas('usuarios', ['email' => 'test@usap.edu']);
        $this->assertDatabaseHas('usuarios', ['email' => 'test2@usap.edu']);
    }
}
