<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FloatingIcon extends Model
{
    public const POSITION_BOTTOM_RIGHT = 'bottom_right';
    public const POSITION_BOTTOM_LEFT = 'bottom_left';
    public const POSITION_TOP_RIGHT = 'top_right';
    public const POSITION_TOP_LEFT = 'top_left';

    protected $fillable = [
        'title',
        'text',
        'icon',
        'url',
        'position',
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

    public static function positionOptions()
    {
        return [
            self::POSITION_BOTTOM_RIGHT => 'Pojok Kanan Bawah',
            self::POSITION_BOTTOM_LEFT => 'Pojok Kiri Bawah',
            self::POSITION_TOP_RIGHT => 'Pojok Kanan Atas',
            self::POSITION_TOP_LEFT => 'Pojok Kiri Atas',
        ];
    }
}
