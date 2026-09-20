<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Support\MukhtarPin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_settings_are_on_the_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('security.edit'))
            ->assertRedirect(route('profile.edit'));

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/profile')
                ->where('pinLength', MukhtarPin::length()),
            );
    }

    public function test_pin_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => MukhtarPin::default(),
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('user-pin.update'), [
                'current_pin' => MukhtarPin::default(),
                'pin' => '654321',
                'pin_confirmation' => '654321',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('654321', $user->refresh()->password));
    }

    public function test_current_pin_must_be_correct_to_update(): void
    {
        $user = User::factory()->create([
            'password' => MukhtarPin::default(),
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('user-pin.update'), [
                'current_pin' => '000000',
                'pin' => '654321',
                'pin_confirmation' => '654321',
            ])
            ->assertSessionHasErrors('current_pin')
            ->assertRedirect(route('profile.edit'));
    }
}
