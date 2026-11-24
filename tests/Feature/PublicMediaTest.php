<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PublicMediaTest extends TestCase
{
    public function test_home_previews_resize_and_cache_large_uploads_without_replacing_the_original(): void
    {
        if (!function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP is not available.');
        }

        $disk = Storage::fake('public');
        $disk->makeDirectory('sliders');
        $original = $disk->path('sliders/banner.png');
        $image = imagecreatetruecolor(2400, 1200);
        imagefilledrectangle($image, 400, 300, 1600, 900, imagecolorallocate($image, 0, 90, 60));
        imagepng($image, $original, 0);
        $bytes = filesize($original);

        $url = '/media-preview/1600/sliders/banner.png';
        $response = $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('Cache-Control', 'max-age=604800, public');
        $preview = $response->baseResponse->getFile()->getRealPath();
        try {
            $this->assertSame([1600, 800], array_slice(getimagesize($preview), 0, 2));
            $this->assertLessThan($bytes, filesize($preview));
            $modified = filemtime($preview);
            $this->assertSame($preview, $this->get($url)->assertOk()->baseResponse->getFile()->getRealPath());
            $this->assertSame($modified, filemtime($preview));
            $this->assertSame($bytes, filesize($original));
            $this->get('/storage/sliders/banner.png')->assertOk()->assertHeader('Content-Type', 'image/png');
        } finally {
            unlink($preview);
        }

        $disk->put('sliders/small.png', UploadedFile::fake()->image('small.png')->getContent());
        $disk->put('articles/pdfs/report.pdf', '%PDF-1.4');
        $this->get('/media-preview/640/sliders/small.png')->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get('/media-preview/999/sliders/banner.png')->assertNotFound();
        $this->get('/media-preview/640/articles/pdfs/report.pdf')->assertNotFound();
    }

    public function test_public_uploads_are_served_without_symlink_or_database_and_private_files_are_excluded(): void
    {
        $disk = Storage::fake('public');
        Storage::fake('local')->put('private-service-requests/lampiran.pdf', '%PDF-1.4');
        $disk->put('home-popups/poster.png', UploadedFile::fake()->image('poster.png')->getContent());
        $disk->put('articles/pdfs/layanan.pdf', '%PDF-1.4');
        $disk->put('service-requests/legacy.pdf', '%PDF-1.4');
        $disk->put('posts/bukan-gambar.png', '<html>Not an image</html>');
        $disk->put('floating-icons/buku-tamu-default.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');
        $disk->put('floating-icons/lain.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');
        DB::connection()->enableQueryLog();

        $image = $this->get('/storage/home-popups/poster.png')->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertInstanceOf(BinaryFileResponse::class, $image->baseResponse);
        $this->assertSame(realpath($disk->path('home-popups/poster.png')), $image->baseResponse->getFile()->getRealPath());
        $this->head('/storage/articles/pdfs/layanan.pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->get('/storage/floating-icons/buku-tamu-default.svg')->assertOk()
            ->assertHeader('Content-Security-Policy', "sandbox; default-src 'none'");
        $this->assertSame([], DB::connection()->getQueryLog());

        foreach (['private-service-requests/lampiran.pdf', 'service-requests/legacy.pdf',
            'posts/bukan-gambar.png', 'posts/tidak-ada.jpg', 'floating-icons/lain.svg'] as $path) {
            $this->get('/storage/' . $path)->assertNotFound();
        }
    }
}
