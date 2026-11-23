<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicMediaController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
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
