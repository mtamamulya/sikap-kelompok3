<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MahasiswaDashboardIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_guest_uses_student_identity_instead_of_guest_or_staff_identity(): void
    {
        $this->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee(config('mahasiswa.nama'))
            ->assertSee('NIM:')
            ->assertSee(config('mahasiswa.nim'))
            ->assertSee('Angkatan '.config('mahasiswa.angkatan'))
            ->assertDontSee('NIP:');
    }

    public function test_student_dashboard_uses_configured_semester(): void
    {
        $this->get(route('mahasiswa.index'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.dashboard-mhs')
            ->assertSee(config('mahasiswa.nama'))
            ->assertSee('>'.config('mahasiswa.semester').'<', false);
    }

    public function test_profile_and_header_show_the_logout_action(): void
    {
        $this->get(route('mahasiswa.profil'))
            ->assertOk()
            ->assertViewIs('mahasiswa.mahasiswa.profil')
            ->assertSee(config('mahasiswa.nama'))
            ->assertSee(config('mahasiswa.nim'))
            ->assertSee('Keluar')
            ->assertSee('action="'.route('logout').'"', false);

        $this->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Keluar');
    }

    public function test_student_can_log_out_using_the_shared_logout_route(): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'nama_role' => 'mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_role');
        $userId = DB::table('users')->insertGetId([
            'id_role' => $roleId,
            'email' => 'mahasiswa-logout@test.local',
            'password' => bcrypt('password'),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_user');

        $this->actingAs(User::findOrFail($userId))
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
