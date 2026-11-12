<?php

use App\Modules\Decision\Controllers\ActionPlanController;
use App\Modules\Decision\Controllers\DecisionController;
use Illuminate\Support\Facades\Route;

// File ini harus tetap dimuat dari routes/web.php atau bootstrap/app.php.
Route::middleware(['web', 'auth', 'verified', 'role:sme_owner'])
    ->prefix('decision')
    ->name('decision.')
    ->group(function (): void {
        Route::get('/', [DecisionController::class, 'index'])->name('index');

        Route::post('/action-plans', [ActionPlanController::class, 'store'])
            ->name('action-plans.store');

        Route::post('/action-plans/from-step', [ActionPlanController::class, 'storeFromStep'])
            ->name('action-plans.from-step');

        Route::put('/action-plans/{actionPlan}', [ActionPlanController::class, 'update'])
            ->name('action-plans.update');

        Route::patch('/action-plans/{actionPlan}/status', [ActionPlanController::class, 'updateStatus'])
            ->name('action-plans.status');
    });
