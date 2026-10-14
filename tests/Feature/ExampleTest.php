<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $productResponse = $this->get('/products');
        $productResponse->assertStatus(200);
    }
}
