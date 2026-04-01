<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order', 'is_active'];

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
