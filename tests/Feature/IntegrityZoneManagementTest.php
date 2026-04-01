<?php

namespace Tests\Feature;

use App\IntegrityZone;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IntegrityZoneManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_integrity_zone()
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.integrity-zones.store'), [
            'title' => 'Area 1',
            'image' => UploadedFile::fake()->image('area-1.jpg', 800, 1200),
            'url' => 'https://example.com/zi/area-1',
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.integrity-zones.index'));

        $integrityZone = IntegrityZone::where('title', 'Area 1')->firstOrFail();

        Storage::disk('public')->assertExists($integrityZone->image);
        $this->assertDatabaseHas('integrity_zones', [
            'id' => $integrityZone->id,
            'title' => 'Area 1',
            'url' => 'https://example.com/zi/area-1',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.integrity-zones.update', $integrityZone), [
            'title' => 'Area 1 Reformasi',
            'url' => 'https://example.com/zi/area-1-reformasi',
            'order' => 2,
        ])->assertRedirect(route('admin.integrity-zones.index'));

        $this->assertDatabaseHas('integrity_zones', [
            'id' => $integrityZone->id,
            'title' => 'Area 1 Reformasi',
            'url' => 'https://example.com/zi/area-1-reformasi',
            'order' => 2,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-zi@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
