<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FamilyTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_family_register(): void
    {
        $user = User::factory()->create();
        Family::factory()->create(['head_of_family_name' => 'محمد علي']);

        $this->actingAs($user)
            ->get(route('families.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('families/index')
                ->where('families.total', 1));
    }

    public function test_authenticated_user_can_create_a_family(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('families.store'), [
                'head_of_family_name' => 'حسن كاظم',
                'profession' => 'موظف',
            ])
            ->assertRedirect(route('families.index'));

        $this->assertDatabaseHas('families', [
            'head_of_family_name' => 'حسن كاظم',
            'profession' => 'موظف',
        ]);
    }

    public function test_authenticated_user_can_delete_a_family(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create();

        $this->actingAs($user)
            ->delete(route('families.destroy', $family))
            ->assertRedirect();

        $this->assertDatabaseMissing('families', ['id' => $family->id]);
    }

    public function test_authenticated_user_can_update_a_family(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create([
            'head_of_family_name' => 'علي حسن',
        ]);

        $this->actingAs($user)
            ->put(route('families.update', $family), [
                'head_of_family_name' => 'علي حسين',
                'profession' => 'معلم',
            ])
            ->assertRedirect(route('families.index'));

        $this->assertDatabaseHas('families', [
            'id' => $family->id,
            'head_of_family_name' => 'علي حسين',
            'profession' => 'معلم',
        ]);
    }

    public function test_authenticated_user_can_view_family_record(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create([
            'head_of_family_name' => 'حسن علي كاظم',
            'mother_name' => 'فاطمة محمد',
        ]);

        $this->actingAs($user)
            ->get(route('families.record', $family))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('families/record')
                ->where('record.head.full_name', 'حسن علي كاظم')
                ->where('record.head.mother_name', 'فاطمة محمد')
                ->where('record.title', 'سجل عوائل حي الإسكان العسكري'));
    }
}
