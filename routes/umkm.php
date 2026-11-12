<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'role:sme_owner'])
    ->prefix('intelligence')
    ->name('intelligence.')
    ->group(function () {
        Route::view('/', 'umkm.intelligence.dashboard')->name('dashboard');
    });
