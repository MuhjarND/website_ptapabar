<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostPhoto extends Model
{
    protected $fillable = ['post_id', 'image', 'caption', 'order'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
