<?php

namespace Tests\Feature\Auth;

use App\Support\MukhtarPin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MukhtarEnterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_enter_with_correct_pin(): void
    {
        $this->post(route('enter'), [
            'pin' => MukhtarPin::default(),
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_guest_cannot_enter_with_wrong_pin(): void
    {
        $this->post(route('enter'), [
            'pin' => '000000',
        ])
            ->assertSessionHasErrors('pin');

        $this->assertGuest();
    }

    public function test_pin_must_match_configured_length(): void
    {
        $this->post(route('enter'), [
            'pin' => '12',
        ])
            ->assertSessionHasErrors('pin');

        $this->assertGuest();
    }
}
