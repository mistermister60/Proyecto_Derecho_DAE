<?php

namespace App\Providers;

use App\Models\Caso;
use App\Observers\CasoObserver;
use App\Policies\CasoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * ═══════════════════════════════════════════════════════
 * PROVIDER: AppServiceProvider
 * ═══════════════════════════════════════════════════════
 * Proveedor de servicios principal de la aplicación.
 *
 * Registra las políticas de autorización (Policies) y los observadores
 * de modelos (Observers) que se cargan al arrancar la aplicación.
 *
 * - CasoPolicy: Reglas de autorización para el modelo Caso.
 * - CasoObserver: Eventos de auditoría para el modelo Caso (created, updated).
 */
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
     *
     * Registra la política de autorización para el modelo Caso
     * y adjunta el observer de auditoría al mismo modelo.
     */
    public function boot(): void
    {
        Gate::policy(Caso::class, CasoPolicy::class);
        Caso::observe(CasoObserver::class);
    }
}
