<?php

namespace App\Support;

use DOMDocument;
use DOMNode;
use DOMElement;

final class WordPressContent
{
    public const ORIGIN = 'https://pta-papuabarat.go.id';
    private const UPLOADS = '/wp-content/uploads/';
    private const SAFE_TAGS = [
        'p', 'br', 'hr', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i',
        'u', 's', 'sup', 'sub', 'blockquote', 'ol', 'ul', 'li', 'table', 'caption',
        'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'pre', 'code', 'a', 'img', 'figure', 'figcaption',
    ];
    private const DROP_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template', 'noscript',
        'form', 'input', 'button', 'textarea', 'select', 'video', 'audio', 'head',
    ];

    public static function imagePath(string $value): ?string
    {
        return self::mediaPath($value, 'jpe?g|png|gif|webp|avif');
    }

    public static function imageUrl(?string $path): ?string
    {
        $valid = $path ? self::imagePath($path) : null;
        return $valid ? self::mediaBase() . $valid : null;
    }

    public static function filePath(string $value): ?string
    {
        return self::mediaPath($value, 'jpe?g|png|gif|webp|avif|pdf');
    }

    public static function fileUrl(string $path): ?string
    {
        $valid = self::filePath($path);
        return $valid ? self::mediaBase() . $valid : null;
    }

    public static function archiveUrl(string $url): string
    {
        $uploads = self::ORIGIN . self::UPLOADS;
        return str_starts_with($url, $uploads) ? self::mediaBase() . substr($url, strlen($uploads)) : $url;
    }

    public static function archiveHtml(?string $html): string
    {
        return str_replace(self::ORIGIN . self::UPLOADS, htmlspecialchars(self::mediaBase(), ENT_QUOTES, 'UTF-8'), $html ?? '');
    }

    private static function mediaBase(): string
    {
        $url = config('wordpress.media_url');
        $parts = is_string($url) && filter_var($url, FILTER_VALIDATE_URL) ? parse_url($url) : [];
        if (($parts['scheme'] ?? null) !== 'https' || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])) {
            return self::ORIGIN . self::UPLOADS;
        }

        return rtrim($url, '/') . '/';
    }

    private static function mediaPath(string $value, string $extensions): ?string
    {
        if (str_starts_with($value, '$upload$/')) {
            $value = substr($value, 9);
        }
        if (str_starts_with($value, '/') || str_contains($value, '://')) {
            $url = self::link($value);
            $source = $url ? parse_url($url) : [];
            $path = $source['path'] ?? '';
            $host = strtolower($source['host'] ?? '');
            if (in_array($host, ['i0.wp.com', 'i1.wp.com', 'i2.wp.com'], true)
                && preg_match('~^/(?:www\.)?pta-papuabarat\.go\.id(/wp-content/uploads/.+)$~D', $path, $match)) {
                $host = 'pta-papuabarat.go.id';
                $path = $match[1];
            }
            $value = '';
            foreach ([self::ORIGIN . self::UPLOADS, self::mediaBase()] as $uploads) {
                $base = parse_url($uploads);
                $prefix = $base['path'] ?? '/';
                if ($host === strtolower($base['host']) && str_starts_with($path, $prefix)) {
                    $value = substr($path, strlen($prefix));
                    break;
                }
            }
        }

        $path = rawurldecode($value);
        if (!preg_match('/\.(?:' . $extensions . ')$/iD', $path)
            || preg_match('/[%\\\\:\x00-\x1f\x7f?#]/', $path)) {
            return null;
        }
        $segments = explode('/', $path);
        if (array_intersect($segments, ['', '.', '..'])) {
            return null;
        }

        $encoded = implode('/', array_map('rawurlencode', $segments));
        return strlen($encoded) <= 255 ? $encoded : null;
    }

    public static function documentationReferences(string $html): array
    {
        $files = $ids = [];
        if (str_contains($html, '<')) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML('<meta charset="utf-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            foreach ($dom->getElementsByTagName('img') as $img) {
                $path = self::imagePath(trim($img->getAttribute('data-src')))
                    ?? self::imagePath(trim($img->getAttribute('src')));
                if ($path) {
                    $files[$path] = $img->getAttribute('alt');
                    if (ctype_digit($img->getAttribute('data-id'))) {
                        $ids[] = (int) $img->getAttribute('data-id');
                    }
                }
                if (preg_match('/\bwp-image-(\d+)\b/', $img->getAttribute('class'), $match)) {
                    $ids[] = (int) $match[1];
                }
            }
            foreach ($dom->getElementsByTagName('a') as $anchor) {
                $path = self::filePath(trim($anchor->getAttribute('href')));
                if ($path) {
                    $files[$path] ??= trim($anchor->textContent);
                }
            }
        }
        preg_match_all('~(?:\[gallery[^\]]*\bids\s*=\s*["\x27]([\d,\s]+)["\x27]|<!--\s+wp:gallery\b[^>]*?"ids"\s*:\s*\[([\d,\s]+)\])~i', $html, $galleries, PREG_SET_ORDER);
        foreach ($galleries as $gallery) {
            foreach (explode(',', ($gallery[1] ?: $gallery[2]) ?? '') as $id) {
                $ids[] = (int) $id;
            }
        }

        return [$files, array_unique($ids)];
    }

    public static function clean(string $html): array
    {
        $html = preg_replace('/\[\/?caption(?:\s[^\]]*)?\]/i', '', $html);
        if (strpos($html, '<') === false) {
            return [nl2br(htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false)), null, null];
        }

        $in = new DOMDocument('1.0', 'UTF-8');
        $in->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $html,
            LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $out = new DOMDocument('1.0', 'UTF-8');
        $root = $out->appendChild($out->createElement('div'));
        $firstImage = null;
        $firstPdf = null;
        $body = $in->getElementsByTagName('body')->item(0);
        if ($body) {
            self::copyChildren($body, $root, $out, $firstImage, $firstPdf);
        }

        $cleaned = '';
        foreach ($root->childNodes as $child) {
            $cleaned .= $out->saveHTML($child);
        }

        return [trim($cleaned), $firstImage, $firstPdf];
    }

    private static function copyChildren(DOMNode $source, DOMNode $parent, DOMDocument $out, ?string &$firstImage, ?string &$firstPdf): void
    {
        foreach ($source->childNodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $parent->appendChild($out->createTextNode($node->nodeValue));
                continue;
            }
            if (!$node instanceof DOMElement) {
                continue;
            }
            $tag = $node->tagName;
            if (in_array($tag, ['iframe', 'embed', 'object'], true)) {
                $raw = trim($node->getAttribute($tag === 'object' ? 'data' : 'src'));
                $pdf = self::mediaPath($raw, 'pdf');
                $url = $pdf ? self::mediaBase() . $pdf : self::link($raw);
                if ($url && str_starts_with($url, 'https://')) {
                    $parts = parse_url($url);
                    $path = $parts['path'] ?? '';
                    $host = strtolower($parts['host'] ?? '');
                    $isPdf = preg_match('/\.pdf$/iD', $path);
                    if ($isPdf || ($host === 'drive.google.com' && str_starts_with($path, '/file/d/'))
                        || ($host === 'docs.google.com' && preg_match('~^/(document|spreadsheets|presentation)/d/~', $path))) {
                        $link = $out->createElement('a', 'Buka dokumen');
                        $link->setAttribute('href', $url);
                        $link->setAttribute('rel', 'noopener noreferrer');
                        $parent->appendChild($link);
                        if ($isPdf) {
                            $firstPdf ??= $url;
                        }
                    }
                }
                continue;
            }
            if (in_array($tag, self::DROP_TAGS, true)) {
                continue;
            }
            if (!in_array($tag, self::SAFE_TAGS, true)) {
                self::copyChildren($node, $parent, $out, $firstImage, $firstPdf);
                continue;
            }
            $new = $out->createElement($tag);
            if ($tag === 'img') {
                $path = self::imagePath(trim($node->getAttribute('src')));
                if (!$path) {
                    continue;
                }
                $firstImage = $firstImage ?? $path;
                $new->setAttribute('src', self::imageUrl($path));
                $new->setAttribute('alt', $node->getAttribute('alt'));
                $new->setAttribute('loading', 'lazy');
            } elseif ($tag === 'a') {
                $raw = trim($node->getAttribute('href'));
                $mediaPath = self::mediaPath($raw, 'pdf|docx?|xlsx?|pptx?|zip|jpe?g|png|gif|webp|avif');
                $href = $mediaPath ? self::mediaBase() . $mediaPath : self::link($raw);
                if ($href) {
                    $new->setAttribute('href', $href);
                    $new->setAttribute('rel', 'noopener noreferrer');
                    if (str_starts_with($href, 'https://') && preg_match('/\.pdf$/iD', (string) parse_url($href, PHP_URL_PATH))) {
                        $firstPdf ??= $href;
                    }
                }
            } elseif ($tag === 'td' || $tag === 'th') {
                foreach (['colspan', 'rowspan'] as $attribute) {
                    if (preg_match('/^[1-9]\d?$/D', $node->getAttribute($attribute))) {
                        $new->setAttribute($attribute, $node->getAttribute($attribute));
                    }
                }
            }
            $parent->appendChild($new);
            self::copyChildren($node, $new, $out, $firstImage, $firstPdf);
        }
    }

    private static function link(string $value): ?string
    {
        if ($value === '' || preg_match('/[\x00-\x20\x7f\\\\]/', $value)) {
            return null;
        }
        if (strpos($value, '//') === 0) {
            $value = 'https:' . $value;
        } elseif ($value[0] === '/') {
            $value = self::ORIGIN . $value;
        }
        if (!preg_match('/^https?:\/\//i', $value) || !filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }
        $url = parse_url($value);
        if (!$url || isset($url['user']) || isset($url['pass'])) {
            return null;
        }
        if (in_array(strtolower($url['host'] ?? ''), ['pta-papuabarat.go.id', 'www.pta-papuabarat.go.id'], true)) {
            return self::ORIGIN . ($url['path'] ?? '/') . (isset($url['query']) ? '?' . $url['query'] : '')
                . (isset($url['fragment']) ? '#' . $url['fragment'] : '');
        }

        return $value;
    }
}
