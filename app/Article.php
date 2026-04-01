<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    const CATEGORY_PTA_PAPUA_BARAT = 'pta-papua-barat';
    const CATEGORY_MAHKAMAH_AGUNG = 'mahkamah-agung';
    const CATEGORY_BADILAG = 'badilag';
    const CATEGORY_LAIN_LAIN = 'lain-lain';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'pdf_file',
        'pdf_url',
        'article_category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeArticleCategory($query, $category)
    {
        if (!$category) {
            return $query;
        }

        return $query->where('article_category', $category);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::limit(Str::slug($title), 180, '');
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'artikel';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $slug)
            ->exists()) {
            $suffixText = '-' . $suffix;
            $slug = Str::limit($baseSlug, 180 - strlen($suffixText), '') . $suffixText;
            $suffix++;
        }

        return $slug;
    }

    public static function sanitizeExcerpt($value, $limit = 180)
    {
        $clean = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
        $clean = trim(preg_replace('/\s+/u', ' ', $clean));

        if ($limit === null) {
            return $clean;
        }

        return Str::limit($clean, $limit);
    }

    public function getExcerptPlainAttribute()
    {
        return static::sanitizeExcerpt($this->excerpt ?: $this->content);
    }

    public static function categoryOptions()
    {
        return [
            static::CATEGORY_PTA_PAPUA_BARAT => 'PTA Papua Barat',
            static::CATEGORY_MAHKAMAH_AGUNG => 'Mahkamah Agung',
            static::CATEGORY_BADILAG => 'Badilag',
            static::CATEGORY_LAIN_LAIN => 'Lain-lain',
        ];
    }

    public function getArticleCategoryLabelAttribute()
    {
        return static::categoryOptions()[$this->article_category ?: static::CATEGORY_LAIN_LAIN] ?? null;
    }

    public function getPdfHrefAttribute()
    {
        if ($this->pdf_url) {
            return $this->pdf_url;
        }

        if ($this->pdf_file) {
            return asset('storage/' . $this->pdf_file);
        }

        return null;
    }

    public function getTargetUrlAttribute()
    {
        return $this->pdf_href ?: route('article.detail', $this);
    }

    public function getTargetIsExternalAttribute()
    {
        return (bool) $this->pdf_href;
    }

    public function hasPdf()
    {
        return (bool) $this->pdf_href;
    }
}
