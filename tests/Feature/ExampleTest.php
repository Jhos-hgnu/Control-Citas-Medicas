<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_returns_a_successful_response(): void
    {
        $response = $this->withoutVite()->get('/');

        $response->assertStatus(200);
    }

    public function test_the_health_endpoint_returns_an_ok_status(): void
    {
        $response = $this->getJson('/api/health');

        $response
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }
}
