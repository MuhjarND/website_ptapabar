<?php

namespace App\Support;

class HomeImage
{
    public const SIZES = [640, 960, 1600];

    public static function url(?string $path, int $size, ?string $external = null): ?string
    {
        if (!$path) {
            return $external;
        }

        return function_exists('imagewebp') && preg_match('~\A[^/]+/[A-Za-z0-9][A-Za-z0-9._-]*\.(?:jpe?g|png|webp)\z~i', $path)
            ? route('public-storage.preview', ['size' => $size, 'path' => $path])
            : asset('storage/' . $path);
    }

    public static function wordpressPreview(?string $url, int $width): ?string
    {
        if (!$url || !str_starts_with($url, WordPressContent::ORIGIN . '/wp-content/uploads/')) {
            return null;
        }

        return 'https://i0.wp.com/' . substr($url, strlen('https://')) . '?w=' . $width;
    }

    public static function preview(string $source, string $mime, int $size): ?string
    {
        if (!function_exists('imagewebp') || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return null;
        }

        $bytes = filesize($source);
        if ($bytes < 32 * 1024) {
            return null;
        }

        $directory = storage_path('framework/cache/image-previews');
        $cache = $directory . '/' . hash('sha256', $source . '|' . filemtime($source) . '|' . $bytes . '|' . $size) . '.webp';
        if (is_file($cache)) {
            return $cache;
        }

        [$width, $height] = @getimagesize($source) ?: [0, 0];
        $pixels = $width * $height;
        if ($mime === 'image/webp' && max($width, $height) <= $size) {
            return null;
        }

        $limit = ini_parse_quantity((string) ini_get('memory_limit'));
        if (!$pixels || $pixels > 24_000_000 || ($limit > 0 && memory_get_usage(true) + 8 * ($pixels + $size ** 2) + 8 * 1024 ** 2 > $limit)) {
            return null;
        }

        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            return null;
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
        };
        if (!$image) {
            return null;
        }

        $ratio = min(1, $size / max($width, $height));
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));
        $preview = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($preview, false);
        imagesavealpha($preview, true);
        imagecopyresampled($preview, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        unset($image);

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($source, 'IFD0')['Orientation'] ?? 1);
            if (in_array($orientation, [2, 5, 7], true)) {
                imageflip($preview, IMG_FLIP_HORIZONTAL);
            } elseif ($orientation === 4) {
                imageflip($preview, IMG_FLIP_VERTICAL);
            }
            $angle = [3 => 180, 5 => 90, 6 => -90, 7 => -90, 8 => 90][$orientation] ?? 0;
            if ($angle !== 0) {
                $preview = imagerotate($preview, $angle, 0) ?: $preview;
            }
        }

        $temp = @tempnam($directory, 'preview-');
        if ($temp === false) {
            return null;
        }

        try {
            if (@imagewebp($preview, $temp, 82) && ($newBytes = filesize($temp)) > 0 && $newBytes < $bytes) {
                @rename($temp, $cache);
            }

            return is_file($cache) ? $cache : null;
        } finally {
            if (is_file($temp)) {
                @unlink($temp);
            }
        }
    }
}
