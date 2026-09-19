<?php

namespace App\Http\Controllers;

use App\Services\Statistics\FamilyStatisticsService;
use App\Services\Statistics\StatisticsChartBuilder;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController extends Controller
{
    public function index(FamilyStatisticsService $statistics): Response
    {
        return Inertia::render('statistics/index', [
            'report' => $statistics->report(),
        ]);
    }

    public function charts(StatisticsChartBuilder $charts): Response
    {
        return Inertia::render('statistics/charts', [
            'report' => $charts->report(),
        ]);
    }
}
