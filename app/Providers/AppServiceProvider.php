<?php

namespace App\Providers;

use App\Repositories\JsonKurikulumRepository;
use App\Repositories\JsonMataKuliahRepository;
use App\Repositories\KurikulumRepository;
use App\Repositories\MataKuliahRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        // TODO-DB: ganti ke EloquentKurikulumRepository / EloquentMataKuliahRepository saat database dipakai
        $this->app->bind(
            KurikulumRepository::class,
            JsonKurikulumRepository::class
        );
        $this->app->bind(
            MataKuliahRepository::class,
            JsonMataKuliahRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
