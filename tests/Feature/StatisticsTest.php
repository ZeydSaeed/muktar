<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FamilyChild;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_statistics(): void
    {
        $this->get(route('statistics'))->assertRedirect(route('home'));
        $this->get(route('statistics.charts'))->assertRedirect(route('home'));
    }

    public function test_authenticated_user_can_view_detailed_statistics(): void
    {
        $user = User::factory()->create();
        $teacher = Family::factory()->create([
            'profession' => 'معلم',
            'house_number' => 31800,
            'males_count' => 2,
            'females_count' => 3,
        ]);
        Family::factory()->create([
            'profession' => 'معلم',
            'house_number' => 32700,
            'males_count' => 1,
            'females_count' => 1,
        ]);
        FamilyChild::factory()->create([
            'family_id' => $teacher->id,
            'name' => 'أحمد',
            'profession' => 'طالب',
        ]);

        $this->actingAs($user)
            ->get(route('statistics'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('statistics/index')
                ->where('report.title', 'إحصائية الاسكان العسكري')
                ->where('report.summary.families', 2)
                ->where('report.summary.children', 1)
                ->where('report.summary.people', 3)
                ->where('report.summary.families_with_children', 1)
                ->where('report.summary.males', 3)
                ->where('report.summary.females', 4)
                ->where('report.sections.0.title', 'توزيع المهن لرب الأسرة')
                ->where('report.sections.0.rows.0.label', 'معلم')
                ->where('report.sections.0.rows.0.total', 2));
    }

    public function test_authenticated_user_can_view_chart_statistics(): void
    {
        $user = User::factory()->create();
        $family = Family::factory()->create([
            'profession' => 'معلم',
            'males_count' => 2,
            'females_count' => 1,
        ]);
        FamilyChild::factory()->create([
            'family_id' => $family->id,
            'profession' => 'طالب',
        ]);

        $this->actingAs($user)
            ->get(route('statistics.charts'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('statistics/charts')
                ->where('report.title', 'الإحصائية البيانية للاسكان العسكري')
                ->where('report.kpis.0.label', 'العوائل')
                ->where('report.kpis.0.value', 1)
                ->where('report.compositions.0.title', 'تركيب السكان')
                ->where('report.bars.0.title', 'أبرز المهن لرب الأسرة'));
    }
}
