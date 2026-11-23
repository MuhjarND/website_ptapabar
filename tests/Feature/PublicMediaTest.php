<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PublicMediaTest extends TestCase
{
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
