<?php

namespace App\Providers;

use App\Models\Caso;
use App\Observers\CasoObserver;
use App\Policies\CasoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Caso::class, CasoPolicy::class);
        Caso::observe(CasoObserver::class);
    }
}
