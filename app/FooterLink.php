<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    public const SECTION_QUICK = 'quick';
    public const SECTION_RELIGIOUS_COURTS = 'religious_courts';
    public const SECTION_EXTERNAL = 'external';

    protected $fillable = [
        'section',
        'title',
        'url',
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

    public static function sectionOptions()
    {
        return [
            static::SECTION_QUICK => 'Tautan Cepat',
            static::SECTION_RELIGIOUS_COURTS => 'PA Sewilayah Hukum',
            static::SECTION_EXTERNAL => 'Tautan Eksternal',
        ];
    }

    public function getSectionLabelAttribute()
    {
        return static::sectionOptions()[$this->section] ?? ucfirst($this->section);
    }
}
