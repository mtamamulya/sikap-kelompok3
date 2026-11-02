<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat 4 Role Utama
        $roles = [
            ['id_role' => 1, 'nama_role' => 'admin'],
            ['id_role' => 2, 'nama_role' => 'kaprodi'],
            ['id_role' => 3, 'nama_role' => 'dosen'],
            ['id_role' => 4, 'nama_role' => 'mahasiswa'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['id_role' => $role['id_role']],
                ['nama_role' => $role['nama_role']] // Sesuaikan nama kolom jika di migrasi menggunakan 'nama'
            );
        }

        // 2. Buat Akun Penguji Awal
        User::factory()->create([
            'email'   => 'admin@trk.ac.id',
            'id_role' => 1, // Admin
            'status'  => true,
        ]);
    }
}