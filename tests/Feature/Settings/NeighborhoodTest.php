<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NeighborhoodTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_update_the_neighborhood(): void
    {
        $this->patch(route('settings.neighborhood.update'), [
            'neighborhood' => 'حي الاختبار',
        ])->assertRedirect(route('home'));
    }

    public function test_authenticated_user_can_update_the_neighborhood(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('settings.neighborhood.update'), [
                'neighborhood' => 'حي العباس',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<title>حي العباس</title>', false)
            ->assertInertia(fn (Assert $page) => $page
                ->where('neighborhood', 'حي العباس'));
    }

    public function test_neighborhood_update_requires_a_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('settings.neighborhood.update'), [
                'neighborhood' => '',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('neighborhood');
    }
}
