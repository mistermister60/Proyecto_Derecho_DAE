<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function testRootRedirectsToLogin(): void
    {
        $this->get('/')->assertStatus(302);
    }

    public function testLoginPageLoads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function testHealthEndpoint(): void
    {
        $this->get('/api/health')->assertStatus(204);
    }
}
