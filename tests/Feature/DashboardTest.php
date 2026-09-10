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
            'house_number' => 1523,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'حسن',
            'profession' => 'معلم',
            'house_number' => 1299,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['profession' => 'موظف', 'house_group' => 15]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'علي')
                ->where('filters.profession', 'موظف')
                ->where('filters.house_group', '15'));
    }

    public function test_dashboard_filters_males_greater_than(): void
    {
        $user = User::factory()->create();
        Family::factory()->create([
            'head_of_family_name' => 'كثير',
            'males_count' => 5,
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'قليل',
            'males_count' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['males_op' => 'gt', 'males_count' => 3]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'كثير'));
    }

    public function test_dashboard_filters_karbala_date_before(): void
    {
        $user = User::factory()->create();
        Family::factory()->create([
            'head_of_family_name' => 'قديم',
            'karbala_residence_date' => '2010-01-01',
        ]);
        Family::factory()->create([
            'head_of_family_name' => 'حديث',
            'karbala_residence_date' => '2020-01-01',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', [
                'karbala_op' => 'before',
                'karbala_date' => '2015-01-01',
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('families.total', 1)
                ->where('families.data.0.head_of_family_name', 'قديم'));
    }
}
