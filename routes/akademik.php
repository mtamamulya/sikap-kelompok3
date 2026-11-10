<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkademikController;

// Route khusus modul akademik
Route::get('/akademik/mata-kuliah', [AkademikController::class, 'index']);