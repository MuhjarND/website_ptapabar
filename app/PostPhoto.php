<?php

namespace App;

use App\Support\WordPressContent;
use Illuminate\Database\Eloquent\Model;

class PostPhoto extends Model
{
    protected $fillable = ['post_id', 'image', 'caption', 'order', 'wordpress_path'];

    public function getFileUrlAttribute(): ?string
    {
        return $this->wordpress_path
            ? WordPressContent::fileUrl($this->wordpress_path)
            : asset('storage/' . $this->image);
    }

    public function getFilenameAttribute(): string
    {
        return rawurldecode(basename($this->wordpress_path ?: $this->image));
    }

    public function getIsPdfAttribute(): bool
    {
        return str_ends_with(strtolower($this->filename), '.pdf');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
