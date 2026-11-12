<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL dasar API
    |--------------------------------------------------------------------------
    | Kosong = server yang sama (URL aplikasi + /api/v1). Untuk backend terpisah
    | isi API_BASE_URL, mis. http://127.0.0.1:8001/api/v1
    */
    'base_url' => env('API_BASE_URL'),

    'timeout' => (int) env('API_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Token layanan (layanan ke layanan)
    |--------------------------------------------------------------------------
    | Dibuat dengan: php artisan api:token-layanan <email> <nama>
    | Simpan hanya di .env, jangan di-commit.
    */
    'service_token' => env('API_SERVICE_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Token pengguna di sesi web
    |--------------------------------------------------------------------------
    | Bila true, setiap login web ikut menerbitkan token Sanctum atas nama
    | pengguna dan menyimpannya di sesi. Butuh tabel personal_access_tokens,
    | jadi aktifkan setelah "php artisan migrate" dijalankan di database.
    */
    'token_sesi' => (bool) env('API_TOKEN_SESI', false),

    'token_sesi_nama' => 'web',
];
