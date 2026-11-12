<?php

use App\Modules\Collaboration\Controllers\CollaborationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:sme_owner'])
    ->prefix('collaboration')
    ->name('collaboration.')
    ->group(function (): void {
        Route::get('/', [CollaborationController::class, 'index'])
            ->name('index');

        Route::post('/swipe', [CollaborationController::class, 'swipe'])
            ->name('swipe');
    });
