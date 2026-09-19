<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FamilyChild;
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
        FamilyChild::factory()->create(['family_id' => $family->id]);

        $this->actingAs($user)
            ->delete(route('families.destroy', $family))
            ->assertRedirect();

        $this->assertDatabaseMissing('families', ['id' => $family->id]);
        $this->assertDatabaseMissing('family_children', ['family_id' => $family->id]);
    }

    public function test_authenticated_user_can_bulk_delete_families(): void
    {
        $user = User::factory()->create();
        $first = Family::factory()->create();
        $second = Family::factory()->create();
        $kept = Family::factory()->create();
        FamilyChild::factory()->create(['family_id' => $first->id]);

        $this->actingAs($user)
            ->delete(route('families.bulk-destroy'), [
                'ids' => [$first->id, $second->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('families', ['id' => $first->id]);
        $this->assertDatabaseMissing('families', ['id' => $second->id]);
        $this->assertDatabaseHas('families', ['id' => $kept->id]);
        $this->assertDatabaseMissing('family_children', ['family_id' => $first->id]);
    }

    public function test_bulk_delete_requires_family_ids(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('families.deletion'))
            ->delete(route('families.bulk-destroy'), ['ids' => []])
            ->assertRedirect(route('families.deletion'))
            ->assertSessionHasErrors('ids');
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
            'spouse_name' => 'زينب حسن علي',
            'spouse_profession' => 'معلمة',
        ]);

        $this->actingAs($user)
            ->get(route('families.record', $family))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('families/record')
                ->where('record.head.full_name', 'حسن علي كاظم')
                ->where('record.head.mother_name', 'فاطمة محمد')
                ->where('record.head.spouse_name', 'زينب حسن علي')
                ->where('record.head.spouse_profession', 'معلمة')
                ->where('record.title', 'سجل عوائل حي الإسكان العسكري'));
    }
}
