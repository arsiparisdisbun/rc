<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
        Gate::define('akses-surat-masuk', fn ($user) => $user->bolehSuratMasuk());
        Gate::define('kelola-akun', fn ($user) => $user->superadmin());
        Paginator::useBootstrapFive();
    }
}
