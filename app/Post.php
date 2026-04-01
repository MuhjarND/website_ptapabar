<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Str;

class Post extends Model
{
    const NEWS_SCOPE_TERKINI = 'terkini';
    const NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT = 'peradilan-agama-papua-barat';

    const ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT = 'pta-papua-barat';
    const ANNOUNCEMENT_CATEGORY_MAHKAMAH_AGUNG = 'mahkamah-agung';
    const ANNOUNCEMENT_CATEGORY_BADILAG = 'badilag';
    const ANNOUNCEMENT_CATEGORY_LAIN_LAIN = 'lain-lain';

    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'image', 'category', 'post_category_id', 'news_scope', 'announcement_category', 'is_published', 'user_id'];

    public static function sanitizeExcerpt($value, $limit = null)
    {
        $cleaned = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned = str_replace("\xC2\xA0", ' ', $cleaned);
        $cleaned = preg_replace('/\s+/u', ' ', trim($cleaned));

        if ($limit) {
            return Str::limit($cleaned, $limit);
        }

        return $cleaned;
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug !== '' ? $baseSlug : 'post';
        $maxLength = 255;
        $counter = 2;

        if (Str::length($slug) > $maxLength) {
            $slug = Str::limit($slug, $maxLength, '');
        }

        $candidate = $slug;

        while (static::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $candidate)
            ->exists()) {
            $suffix = '-' . $counter;
            $candidate = Str::limit($slug, $maxLength - Str::length($suffix), '') . $suffix;
            $counter++;
        }

        return $candidate;
    }

    public static function newsScopeOptions()
    {
        return [
            self::NEWS_SCOPE_TERKINI => 'Berita Terkini',
            self::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT => 'Berita Peradilan Agama Papua Barat',
        ];
    }

    public static function announcementCategoryOptions()
    {
        return [
            self::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT => 'PTA Papua Barat',
            self::ANNOUNCEMENT_CATEGORY_MAHKAMAH_AGUNG => 'Mahkamah Agung',
            self::ANNOUNCEMENT_CATEGORY_BADILAG => 'Badilag',
            self::ANNOUNCEMENT_CATEGORY_LAIN_LAIN => 'Lain-lain',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(PostPhoto::class)->orderBy('order');
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeBerita($query)
    {
        return $query->where('category', 'berita');
    }

    public function scopePengumuman($query)
    {
        return $query->where('category', 'pengumuman');
    }

    public function scopeNewsScope($query, $newsScope)
    {
        if (!$newsScope) {
            return $query;
        }

        if ($newsScope === self::NEWS_SCOPE_TERKINI) {
            return $query->where(function ($innerQuery) use ($newsScope) {
                $innerQuery->where('news_scope', $newsScope)
                    ->orWhereNull('news_scope');
            });
        }

        return $query->where('news_scope', $newsScope);
    }

    public function scopeAnnouncementCategory($query, $announcementCategory)
    {
        if (!$announcementCategory) {
            return $query;
        }

        return $query->where('announcement_category', $announcementCategory);
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function canBeManagedBy(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $this->user_id !== (int) $user->id) {
            return false;
        }

        if ($user->isAuthorSatker()) {
            return $this->category === 'berita'
                && $this->news_scope === self::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT;
        }

        return true;
    }

    public function getAnnouncementCategoryLabelAttribute()
    {
        return static::announcementCategoryOptions()[$this->announcement_category] ?? null;
    }

    public function getNewsScopeLabelAttribute()
    {
        return static::newsScopeOptions()[$this->news_scope] ?? null;
    }

    public function getSourceAgencyLabelAttribute()
    {
        if (!$this->relationLoaded('user') || !$this->user) {
            return null;
        }

        if ($this->user->isAuthorSatker()) {
            return $this->user->name;
        }

        return 'PTA Papua Barat';
    }

    public function getExcerptPlainAttribute()
    {
        return static::sanitizeExcerpt($this->excerpt);
    }
}
