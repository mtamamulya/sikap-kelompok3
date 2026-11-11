<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_four_active_role_accounts_and_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $accounts = [
            'admin@trk.ac.id' => 1,
            'kaprodi@trk.ac.id' => 2,
            'dosen@trk.ac.id' => 3,
            'mahasiswa@trk.ac.id' => 4,
        ];

        $this->assertDatabaseCount('users', count($accounts));

        foreach ($accounts as $email => $roleId) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertSame($roleId, (int) $user->id_role);
            $this->assertTrue($user->status);
            $this->assertTrue(Hash::check('password', $user->password));
        }
    }
}
