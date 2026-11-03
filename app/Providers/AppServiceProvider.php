<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
{
    // TODO-DB: ganti ke EloquentKurikulumRepository / EloquentMataKuliahRepository saat database dipakai
    $this->app->bind(
        \App\Repositories\KurikulumRepository::class,
        \App\Repositories\JsonKurikulumRepository::class
    );
    $this->app->bind(
        \App\Repositories\MataKuliahRepository::class,
        \App\Repositories\JsonMataKuliahRepository::class
    );
}
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
