<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegrityZone extends Model
{
    protected $fillable = [
        'title',
        'image',
        'url',
        'order',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
