<?php

namespace Tests\Feature\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HomeController
 */
class HomeControllerTest extends TestCase
{
    #[Test]
    public function home_page_renders_laravel_text(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('welcome');
        $response->assertSeeText('Laravel v10.10');
    }
}
