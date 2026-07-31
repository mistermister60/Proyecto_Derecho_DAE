<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertStatus(302);
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_health_endpoint(): void
    {
        $this->get('/api/health')->assertStatus(204);
    }
}
