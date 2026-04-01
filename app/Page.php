<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = ['parent_id', 'title', 'slug', 'content', 'menu_group', 'order', 'is_active'];

    public static function menuGroupOptions()
    {
        return [
            'tentang-pengadilan' => 'Tentang Pengadilan',
            'informasi-umum' => 'Informasi Umum',
            'informasi-hukum' => 'Informasi Hukum',
            'transparansi' => 'Transparansi',
            'peraturan-kebijakan' => 'Peraturan dan Kebijakan',
            'informasi' => 'Informasi',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('order');
    }

    public function activeChildren()
    {
        return $this->hasMany(Page::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    public function ancestors()
    {
        $ancestors = collect();
        $page = $this->parent;
        while ($page) {
            $ancestors->prepend($page);
            $page = $page->parent;
        }
        return $ancestors;
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug !== '' ? $baseSlug : 'halaman';
        $originalSlug = $slug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $slug)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
