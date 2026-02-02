<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WorkingPaper;
use App\Policies\WorkingPaperPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(WorkingPaper::class, WorkingPaperPolicy::class);
    }
}
