<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_loads(): void
    {
        $this->seed();

        $this->get('/')->assertStatus(200)->assertSee('Explore 9 places');
    }
}
