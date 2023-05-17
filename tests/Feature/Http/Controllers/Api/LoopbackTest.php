<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Loopback
 */
class LoopbackTest extends TestCase
{
    use WithFaker;

    #[Test]
    public function it_sends_back_the_post_data(): void
    {
        $input = $this->faker->sentence();

        $response = $this->postJson(route('api.loopback'), [
            'message' => $input,
        ]);

        $response->assertOk();
        $response->assertExactJson([
            'message' => $input,
        ]);
    }
}
