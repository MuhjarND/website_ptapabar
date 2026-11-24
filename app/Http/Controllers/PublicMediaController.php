<?php

namespace App\Http\Controllers;

use App\Support\HomeImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicMediaController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
    {
        return $this->serve($path);
    }

    public function preview(int $size, string $path): BinaryFileResponse
    {
        abort_unless(in_array($size, HomeImage::SIZES, true), 404);

        return $this->serve($path, $size);
    }

    private function serve(string $path, ?int $size = null): BinaryFileResponse
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowed = match ($extension) {
            'jpg', 'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'bmp' => ['image/bmp', 'image/x-ms-bmp'],
            'pdf' => in_array(dirname($path), ['articles/pdfs', 'post-photos'], true) ? ['application/pdf'] : [],
            'svg' => $path === 'floating-icons/buku-tamu-default.svg' ? ['image/svg+xml'] : [],
            default => [],
        };
        abort_unless($allowed, 404);

        $disk = Storage::disk('public');
        $root = realpath($disk->path(''));
        $file = realpath($disk->path($path));
        abort_unless($root && $file && str_starts_with($file, $root . DIRECTORY_SEPARATOR)
            && is_file($file) && is_readable($file), 404);

        $mime = File::mimeType($file);
        abort_unless(in_array($mime, $allowed, true), 404);

        if ($size !== null) {
            abort_unless(str_starts_with($mime, 'image/') && $extension !== 'svg', 404);
            if ($preview = HomeImage::preview($file, $mime, $size)) {
                return response()->file($preview, [
                    'Content-Type' => 'image/webp',
                    'Cache-Control' => 'public, max-age=86400',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        $headers = [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ];
        if ($extension === 'svg') {
            $headers['Content-Security-Policy'] = "sandbox; default-src 'none'";
        }

        return response()->file($file, $headers);
    }
}
