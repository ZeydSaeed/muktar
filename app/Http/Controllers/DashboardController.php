<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\DashboardRequest;
use App\Services\Families\FamilyTablePageService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        DashboardRequest $request,
        FamilyTablePageService $page,
    ): Response {
        return Inertia::render('dashboard', $page->props($request));
    }
}
