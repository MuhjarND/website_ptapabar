<?php

namespace Tests\Feature;

use App\FloatingIcon;
use App\User;
use Tests\MigratesTestDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FloatingIconManagementTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_can_create_and_update_floating_icon()
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.floating-icons.store'), [
            'title' => 'Buku Tamu',
            'text' => 'Lihat ketersediaan pegawai',
            'icon' => UploadedFile::fake()->image('buku-tamu.png'),
            'url' => 'https://bukutamu.pta-papuabarat.go.id/employees',
            'position' => FloatingIcon::POSITION_TOP_LEFT,
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.floating-icons.index'));

        $floatingIcon = FloatingIcon::where('title', 'Buku Tamu')->firstOrFail();

        $this->assertDatabaseHas('floating_icons', [
            'id' => $floatingIcon->id,
            'text' => 'Lihat ketersediaan pegawai',
            'position' => FloatingIcon::POSITION_TOP_LEFT,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.floating-icons.update', $floatingIcon), [
            'title' => 'Buku Tamu Pegawai',
            'text' => 'Cek ketersediaan pegawai',
            'url' => 'https://bukutamu.pta-papuabarat.go.id/employees',
            'position' => FloatingIcon::POSITION_BOTTOM_LEFT,
            'order' => 2,
        ])->assertRedirect(route('admin.floating-icons.index'));

        $this->assertDatabaseHas('floating_icons', [
            'id' => $floatingIcon->id,
            'title' => 'Buku Tamu Pegawai',
            'text' => 'Cek ketersediaan pegawai',
            'position' => FloatingIcon::POSITION_BOTTOM_LEFT,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin Floating',
            'email' => 'admin-floating@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
