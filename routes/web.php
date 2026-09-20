<?php

use App\Http\Controllers\Auth\HomeController;
use App\Http\Controllers\Auth\MukhtarLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Data\BackupController;
use App\Http\Controllers\Data\ExcelImportController;
use App\Http\Controllers\Data\RestoreController;
use App\Http\Controllers\FamilyChildController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('enter', [MukhtarLoginController::class, 'enter'])
    ->middleware('throttle:5,1')
    ->name('enter');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('statistics/charts', [StatisticsController::class, 'charts'])->name('statistics.charts');

    Route::get('families', [FamilyController::class, 'index'])->name('families.index');
    Route::get('families/create', [FamilyController::class, 'create'])->name('families.create');
    Route::post('families', [FamilyController::class, 'store'])->name('families.store');
    Route::delete('families', [FamilyController::class, 'bulkDestroy'])->name('families.bulk-destroy');
    Route::get('families/{family}/edit', [FamilyController::class, 'edit'])->name('families.edit');
    Route::get('families/{family}/record', [FamilyController::class, 'record'])->name('families.record');
    Route::put('families/{family}', [FamilyController::class, 'update'])->name('families.update');
    Route::delete('families/{family}', [FamilyController::class, 'destroy'])->name('families.destroy');
    Route::get('families/delete', [FamilyController::class, 'deletion'])->name('families.deletion');
    Route::post('families/{family}/children', [FamilyChildController::class, 'store'])
        ->name('families.children.store');
    Route::put('families/{family}/children/{familyChild}', [FamilyChildController::class, 'update'])
        ->name('families.children.update');
    Route::delete('families/{family}/children/{familyChild}', [FamilyChildController::class, 'destroy'])
        ->name('families.children.destroy');
    Route::get('data/excel-import', [ExcelImportController::class, 'show'])
        ->name('data.excel-import');
    Route::get('data/excel-import/template', [ExcelImportController::class, 'template'])
        ->name('data.excel-import.template');
    Route::post('data/excel-import', [ExcelImportController::class, 'store'])
        ->name('data.excel-import.store');
    Route::get('data/backup', [BackupController::class, 'show'])->name('data.backup');
    Route::post('data/backup', [BackupController::class, 'store'])->name('data.backup.store');
    Route::get('data/backup/download', [BackupController::class, 'download'])
        ->name('data.backup.download');
    Route::get('data/restore', [RestoreController::class, 'show'])->name('data.restore');
    Route::post('data/restore', [RestoreController::class, 'store'])->name('data.restore.store');
});

require __DIR__.'/settings.php';
