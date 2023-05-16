<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\ChirpCreated;
use App\Http\Requests\SaveChirp;
use App\Models\Chirp;
use App\Models\User;
use App\Notifications\NewChirp;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\Wormhole;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ChirpController
 */
class ChirpControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker, AdditionalAssertions;

    #[Test]
    public function store_sends_notification_for_new_chirp_to_other_users(): void
    {
        $other = User::factory()->createQuietly();
        $user = User::factory()->createQuietly();
        $message = $this->faker->sentence();

        Notification::fake();

        $response = $this->actingAs($user)
            ->post(route('chirps.store'), [
                'message' => $message,
            ]);

        $response->assertRedirectToRoute('chirps.index');
        $this->assertDatabaseCount('chirps', 1);

        Notification::assertSentTimes(NewChirp::class, 1);
        Notification::assertSentTo($other, NewChirp::class);
        Notification::assertNothingSentTo($user);
    }


    #[Test]
    public function index_displays_view_with_recent_chirps(): void
    {
        $user = User::factory()->create();
        $chirps = Chirp::factory()->createManyQuietly([
            ['created_at' => now()->subDay()],
            ['user_id' => $user->id],
        ]);

        $response = $this->actingAs($user)
            ->get(route('chirps.index'));

        $response->assertOk();
        $response->assertViewIs('chirps.index');
        $response->assertViewHas('chirps', $chirps->reverse()->values());
    }

    #[Test]
    public function store_saves_a_new_chirp(): void
    {
        $user = User::factory()->create();
        $message = $this->faker->sentence();

        $response = $this->actingAs($user)
            ->post(route('chirps.store'), [
                'message' => $message,
            ]);

        $response->assertRedirectToRoute('chirps.index');

        $this->assertDatabaseHas(Chirp::class, [
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    #[Test]
    public function store_validates_with_form_request()
    {
        $this->assertRouteUsesFormRequest(
            'chirps.store',
            SaveChirp::class
        );
    }

    #[Test]
    public function store_throws_validation_exception(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('chirps.index'))
            ->post(route('chirps.store'));

        $response->assertRedirectToRoute('chirps.index');
        $response->assertSessionHasErrors(['message']);

        $this->assertDatabaseEmpty(Chirp::class);
    }

    #[Test]
    public function edit_displays_view_for_chirp(): void
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('chirps.edit', $chirp->id));

        $response->assertOk();
        $response->assertViewIs('chirps.edit');
        $response->assertViewHas('chirp', $chirp);
    }

    #[Test]
    public function edit_returns_403_for_someone_elses_chirp(): void
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('chirps.edit', $chirp->id));

        $response->assertForbidden();
    }


    #[Test]
    public function update_saves_a_new_chirp(): void
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id' => $user->id
        ]);

        $message = $this->faker->sentence();

        $response = $this->actingAs($user)
            ->put(route('chirps.update', $chirp->id), [
                'message' => $message,
            ]);

        $response->assertRedirectToRoute('chirps.index');

        $this->assertDatabaseHas(Chirp::class, [
            'id' => $chirp->id,
            'user_id' => $chirp->user_id,
            'message' => $message,
        ]);
    }

    #[Test]
    public function update_validates_with_form_request()
    {
        $this->assertRouteUsesFormRequest(
            'chirps.update',
            SaveChirp::class
        );
    }

    #[Test]
    public function delete_removes_chirp(): void
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->delete(route('chirps.destroy', $chirp->id));

        $response->assertRedirectToRoute('chirps.index');

        $this->assertModelMissing($chirp);
    }
}
