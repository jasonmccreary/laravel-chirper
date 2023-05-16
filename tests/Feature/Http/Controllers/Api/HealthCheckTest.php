<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\HealthCheck
 */
class HealthCheckTest extends TestCase
{
    #[Test]
    public function it_returns_status_json(): void
    {
        $response = $this->getJson('/api/status');

        $response->assertOk();
        $response->assertExactJson([
            'status' => 'ok',
            'message' => 'Chirp chirp!'
        ]);
    }
}
