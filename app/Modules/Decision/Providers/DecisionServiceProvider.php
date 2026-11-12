<?php

namespace App\Modules\Decision\Providers;

use Illuminate\Support\ServiceProvider;

class DecisionServiceProvider extends ServiceProvider
{
    /**
     * Register layanan modul Decision.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap layanan modul Decision.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/decision.php'));
    }
}
