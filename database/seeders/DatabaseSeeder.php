<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('DatabaseSeeder berisi akun uji coba dan tidak boleh dijalankan di production.');
        }

        $this->call([
            JenisDokumenSeeder::class,
            
        ]);

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
                ['nama_role' => $role['nama_role']]
            );
        }

        $accounts = [
            ['email' => 'admin@trk.ac.id', 'id_role' => 1],
            ['email' => 'kaprodi@trk.ac.id', 'id_role' => 2],
            ['email' => 'dosen@trk.ac.id', 'id_role' => 3],
            ['email' => 'mahasiswa@trk.ac.id', 'id_role' => 4],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'id_role' => $account['id_role'],
                    'password' => 'password',
                    'status' => true,
                ]
            );
        }
    }
}