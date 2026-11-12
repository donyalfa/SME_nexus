<?php

namespace App\Modules\Collaboration\Providers;

use Illuminate\Support\ServiceProvider;

class CollaborationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/collaboration.php'));
    }
}
