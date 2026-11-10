<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah; // Panggil Model MataKuliah yang sudah ada

class AkademikController extends Controller
{
    public function index()
    {
        // Mengambil semua data mata kuliah dari database
        $mataKuliah = MataKuliah::all();

        // Nanti data ini bisa dikirim ke tampilan frontend
        // Contoh sementara, kita tampilkan dulu datanya dalam bentuk JSON di browser
        return view('mahasiswa.mata-kuliah', compact('mataKuliah'));
    }
}
