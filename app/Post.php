<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'image', 'category', 'is_published', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(PostPhoto::class)->orderBy('order');
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
}
