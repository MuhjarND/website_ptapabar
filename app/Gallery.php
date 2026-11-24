<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    public const TYPE_BANNER = 'banner';
    public const TYPE_GREETING = 'greeting';
    public const TYPE_CONDOLENCE = 'condolence';

    protected $fillable = ['title', 'type', 'gallery_category_id', 'file', 'video_url', 'description', 'order', 'is_active'];

    public static function typeOptions(): array
    {
        return [
            'image' => 'Foto',
            'video' => 'Video (YouTube)',
            self::TYPE_BANNER => 'Banner Beranda',
            self::TYPE_GREETING => 'Ucapan Selamat',
            self::TYPE_CONDOLENCE => 'Turut Berduka',
        ];
    }

    public function galleryCategory()
    {
        return $this->belongsTo(GalleryCategory::class);
    }

    public function getThumbnailAttribute()
    {
        if ($this->type === 'video') {
            $id = $this->youtube_id;
            return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
        }
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function getYoutubeIdAttribute()
    {
        return $this->type === 'video' ? self::youtubeId($this->video_url) : null;
    }

    public static function youtubeId(?string $url): ?string
    {
        $parts = parse_url($url ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if (!in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }

        if ($host === 'youtu.be') {
            $id = $path;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'www.youtube-nocookie.com'], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } else {
                preg_match('~^(?:embed|shorts|live)/([^/]+)$~', $path, $matches);
                $id = $matches[1] ?? null;
            }
        }

        return is_string($id ?? null) && preg_match('/^[a-zA-Z0-9_-]{11}$/D', $id) ? $id : null;
    }
}
