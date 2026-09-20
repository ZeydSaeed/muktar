<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('home'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('neighborhood', 'حي الاسكان العسكري')
            ->has('families.data')
            ->where('families.total', 0));
    }

    public function test_dashboard_paginates_families()
    {
        $user = User::factory()->create();
        Family::factory()->count(25)->create();

        $this->actingAs($user);

        $perPage = (int) config('mukhtar.pagination.families_per_page');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->has('families.data', $perPage)
                ->where('families.total', 25)
                ->where('families.current_page', 1)
                ->where('families.last_page', 2));

        $this->get(route('dashboard', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.current_page', 2)
                ->has('families.data', 5));
    }

    public function test_dashboard_lists_families_alphabetically_by_head_name(): void
    {
        $user = User::factory()->create();
        Family::factory()->create(['head_of_family_name' => 'يوسف كاظم']);
        Family::factory()->create(['head_of_family_name' => 'أحمد علي']);
        Family::factory()->create(['head_of_family_name' => 'حسن محمد']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.data.0.head_of_family_name', 'أحمد علي')
                ->where('families.data.1.head_of_family_name', 'حسن محمد')
                ->where('families.data.2.head_of_family_name', 'يوسف كاظم'));
    }

    public function test_dashboard_searches_head_of_family_name(): void
    {
        $user = User::factory()->create();
        Family::factory()->create(['head_of_family_name' => 'حسن علي كاظم']);
        Family::factory()->create(['head_of_family_name' => 'محمد جاسم']);

        $this->actingAs($user)
            ->get(route('dashboard', ['search' => 'حسن علي']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('search', 'حسن علي')
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'حسن علي كاظم'));
    }

    public function test_dashboard_filters_profession_and_house_group(): void
    {
        $user = User::factory()->create();
        Family::factory()->create([
            'head_of_family_name' => 'علي',
            'profession' => 'موظف',
            'house_number' => 31798,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'حسن',
            'profession' => 'موظف',
            'house_number' => 32700,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['profession' => 'موظف', 'house_group' => 31]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'علي')
                ->where('filters.profession', 'موظف')
                ->where('filters.house_group', '31'));
    }

    public function test_dashboard_filters_house_group_by_thousands_digits(): void
    {
        $user = User::factory()->create();
        Family::factory()->create([
            'head_of_family_name' => 'مطابق',
            'house_number' => 31850,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'مطابق أطول',
            'house_number' => 131200,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'مختلف',
            'house_number' => 32798,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['house_group' => 31]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 2)
                ->where('filterOptions.house_groups.0.label', '31')
                ->where('filterOptions.house_groups.1.label', '32'));
    }

    public function test_dashboard_filters_landmark_birth_year_and_cards(): void
    {
        $user = User::factory()->create();
        Family::factory()->create([
            'head_of_family_name' => 'مطابق',
            'nearest_landmark' => 'جامع الحسين',
            'birth_date' => '1975-06-01',
            'ration_card_number' => 850010,
            'housing_card_number' => 420010,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'مختلف',
            'nearest_landmark' => 'سوق المحافظة',
            'birth_date' => '1988-06-01',
            'ration_card_number' => 850020,
            'housing_card_number' => 420020,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', [
                'nearest_landmark' => 'جامع الحسين',
                'birth_year' => 1975,
                'ration_card_number' => 850010,
                'housing_card_number' => 420010,
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'مطابق'));
    }
}
