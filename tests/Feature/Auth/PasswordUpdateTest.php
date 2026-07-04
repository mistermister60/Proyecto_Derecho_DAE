<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $this->markTestSkipped('Password update route (PUT /password) not implemented in this custom auth system.');
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $this->markTestSkipped('Password update route (PUT /password) not implemented in this custom auth system.');
    }
}
