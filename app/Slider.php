<?php

namespace App;

use App\Support\WordPressContent;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['title', 'description', 'image', 'link', 'text_position', 'order', 'is_active'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : WordPressContent::imageUrl($this->wordpress_cover_path);
    }
}
