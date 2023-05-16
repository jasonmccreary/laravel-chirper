<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Chirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ChirpController
 */
class ChirpControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_count_for_no_chirps(): void
    {
        $response = $this->getJson(route('api.chirps.index'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'count' => 0,
            ],
        ]);
    }

    #[Test]
    public function index_returns_chirp_count(): void
    {
        Chirp::factory()->times(3)->create();

        $response = $this->getJson(route('api.chirps.index'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'count' => 3,
            ],
        ]);
    }

    #[Test]
    public function show_returns_chirp_as_json()
    {
        $chirp = Chirp::factory()->create();

        $response = $this->get(route('api.chirps.show', $chirp->id));

        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                'id' => $chirp->id,
                'user_id' => $chirp->user_id,
                'message' => $chirp->message,
                'created_at' => $chirp->created_at,
                'updated_at' => $chirp->updated_at,
            ],
        ]);
    }

    #[Test]
    public function show_throws_a_404()
    {
        $response = $this->get(route('api.chirps.show', 123));

        $response->assertNotFound();
    }
}
