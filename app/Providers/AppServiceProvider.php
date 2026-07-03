<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Caso;
use App\Policies\CasoPolicy;
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
       Gate::policy(Caso::class, CasoPolicy::class);
    }
}
