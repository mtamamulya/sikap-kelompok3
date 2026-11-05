<?php

namespace App\Providers;

use App\Repositories\AkreditasiDbStore;
use App\Repositories\AkreditasiStore;
use App\Repositories\DosenDbStore;
use App\Repositories\DosenStore;
use App\Repositories\EloquentKurikulumRepository;
use App\Repositories\EloquentMataKuliahRepository;
use App\Repositories\KurikulumDbStore;
use App\Repositories\KurikulumRepository;
use App\Repositories\KurikulumStore;
use App\Repositories\MahasiswaDbStore;
use App\Repositories\MahasiswaStore;
use App\Repositories\MataKuliahDbStore;
use App\Repositories\MataKuliahRepository;
use App\Repositories\MataKuliahStore;
use Illuminate\Support\ServiceProvider;

/**
 * Memilih sumber data modul admin/kaprodi/publik: DATA_SOURCE=json (default, data simulasi) atau database.
 * Didaftarkan SETELAH AppServiceProvider di bootstrap/providers.php, sehingga binding di sini menggantikan
 * binding JSON hanya saat DATA_SOURCE=database. AppServiceProvider tidak perlu diubah.
 */
class DataSourceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (config('admin.data_source', 'json') !== 'database') {
            return;
        }

        $this->app->bind(KurikulumStore::class, KurikulumDbStore::class);
        $this->app->bind(MataKuliahStore::class, MataKuliahDbStore::class);
        $this->app->bind(AkreditasiStore::class, AkreditasiDbStore::class);
        $this->app->bind(DosenStore::class, DosenDbStore::class);
        $this->app->bind(MahasiswaStore::class, MahasiswaDbStore::class);
        $this->app->bind(KurikulumRepository::class, EloquentKurikulumRepository::class);
        $this->app->bind(MataKuliahRepository::class, EloquentMataKuliahRepository::class);
    }
}