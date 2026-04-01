<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'type', 'gallery_category_id', 'file', 'video_url', 'description', 'order', 'is_active'];

    public function galleryCategory()
    {
        return $this->belongsTo(GalleryCategory::class);
    }

    public function getThumbnailAttribute()
    {
        if ($this->type === 'video' && $this->video_url) {
            // Extract YouTube ID
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $matches);
            return $matches[1] ?? null ? "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg" : null;
        }
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function getYoutubeIdAttribute()
    {
        if ($this->type !== 'video' || !$this->video_url) return null;
        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $matches);
        return $matches[1] ?? null;
    }
}
