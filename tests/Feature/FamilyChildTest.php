<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FamilyChildTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_opens_children_modal_for_a_family(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create(['head_of_family_name' => 'حسن علي']);
        FamilyChild::factory()->create([
            'family_id' => $family->id,
            'name' => 'أحمد حسن',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['family_children' => $family->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('childrenModal.family_id', $family->id)
                ->where('childrenModal.family_name', 'حسن علي')
                ->has('childrenModal.children', 1)
                ->where('childrenModal.children.0.name', 'أحمد حسن'));
    }

    public function test_authenticated_user_can_create_a_child(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create();

        $this->from(route('dashboard', ['family_children' => $family->id]))
            ->actingAs($user)
            ->post(route('families.children.store', $family), [
                'name' => 'محمد كاظم',
                'profession' => 'طالب',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('family_children', [
            'family_id' => $family->id,
            'name' => 'محمد كاظم',
            'profession' => 'طالب',
        ]);
    }

    public function test_authenticated_user_can_update_a_child(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create();
        $child = FamilyChild::factory()->create([
            'family_id' => $family->id,
            'name' => 'علي',
        ]);

        $this->from(route('dashboard', ['family_children' => $family->id]))
            ->actingAs($user)
            ->put(route('families.children.update', [$family, $child]), [
                'name' => 'علي حسن',
                'profession' => 'موظف',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('family_children', [
            'id' => $child->id,
            'name' => 'علي حسن',
            'profession' => 'موظف',
        ]);
    }

    public function test_authenticated_user_can_delete_a_child(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create();
        $child = FamilyChild::factory()->create(['family_id' => $family->id]);

        $this->from(route('dashboard', ['family_children' => $family->id]))
            ->actingAs($user)
            ->delete(route('families.children.destroy', [$family, $child]))
            ->assertRedirect();

        $this->assertDatabaseMissing('family_children', ['id' => $child->id]);
    }
}
