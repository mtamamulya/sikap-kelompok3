<?php

use App\Http\Controllers\AkademikController;
use Illuminate\Support\Facades\Route;

// Route khusus modul akademik
Route::get('/akademik/mata-kuliah', [AkademikController::class, 'index']);
