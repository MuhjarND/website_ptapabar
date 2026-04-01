<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CkeditorUploadTest extends TestCase
{
    use DatabaseMigrations;

    public function test_ckeditor_upload_accepts_images()
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.ckeditor.upload'), [
            'upload' => UploadedFile::fake()->image('konten.png'),
        ]);

        $response->assertOk();
        $response->assertJson(['uploaded' => 1]);
        $this->assertCount(1, Storage::disk('public')->allFiles('uploads'));
    }

    public function test_ckeditor_upload_rejects_non_image_files()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->from(route('admin.dashboard'))->post(route('admin.ckeditor.upload'), [
            'upload' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHasErrors('upload');
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
