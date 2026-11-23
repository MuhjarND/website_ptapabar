<?php

namespace Tests\Feature;

use App\Setting;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class HomePopupTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_can_publish_replace_and_remove_home_image_popup(): void
    {
        Storage::fake('public');
        $this->get('/')->assertOk()->assertDontSee('id="homeImagePopup"', false);
        $this->actingAs(User::factory()->create(['role' => User::ROLE_AUTHOR]))
            ->get(route('admin.home-popup.index'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->get(route('admin.home-popup.index'))->assertOk()->assertSee('Popup Beranda');
        $this->put(route('admin.home-popup.update'), [
            'image' => UploadedFile::fake()->create('bukan-gambar.pdf', 1, 'application/pdf'),
            'alt' => 'Poster',
        ])->assertSessionHasErrors('image');
        foreach (['lama.png', 'baru.png'] as $file) {
            $this->put(route('admin.home-popup.update'), [
                'image' => UploadedFile::fake()->image($file, 400, 550),
                'alt' => 'Poster informasi layanan',
            ])->assertRedirect(route('admin.home-popup.index'));
        }
        $image = Setting::homePopup()['image'];
        $this->assertSame([$image], Storage::disk('public')->allFiles('home-popups'));
        $this->get('/')->assertOk()->assertSee('id="homeImagePopup"', false)->assertSee('Poster informasi layanan');
        $this->get('/berita')->assertOk()->assertDontSee('id="homeImagePopup"', false);
        $this->delete(route('admin.home-popup.destroy'))->assertRedirect(route('admin.home-popup.index'));
        Storage::disk('public')->assertMissing($image);
        $this->get('/')->assertOk()->assertDontSee('id="homeImagePopup"', false);
    }
}
