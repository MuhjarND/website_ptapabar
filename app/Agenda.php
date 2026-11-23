<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agenda extends Model
{
    public const TYPE_KEGIATAN = 'kegiatan';
    public const TYPE_KALENDER_SIDANG = 'kalender-sidang';
    public const TYPE_AGENDA_PIMPINAN = 'agenda-pimpinan';

    protected $fillable = [
        'title',
        'slug',
        'agenda_type',
        'summary',
        'description',
        'location',
        'organizer',
        'start_at',
        'end_at',
        'order',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAgendaType($query, $agendaType)
    {
        if (!$agendaType) {
            return $query;
        }

        return $query->where('agenda_type', $agendaType);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function ($innerQuery) {
            $innerQuery->whereNull('end_at')->where('start_at', '>=', now())
                ->orWhere('end_at', '>=', now());
        });
    }

    public static function typeOptions()
    {
        return [
            static::TYPE_KEGIATAN => 'Kegiatan Pimpinan',
            static::TYPE_KALENDER_SIDANG => 'Kalender Sidang',
            static::TYPE_AGENDA_PIMPINAN => 'Agenda Pimpinan',
        ];
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::limit(Str::slug($title), 180, '');
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'agenda';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('slug', $slug)
            ->exists()) {
            $suffixText = '-' . $suffix;
            $slug = Str::limit($baseSlug, 180 - strlen($suffixText), '') . $suffixText;
            $suffix++;
        }

        return $slug;
    }

    public function getAgendaTypeLabelAttribute()
    {
        return static::typeOptions()[$this->agenda_type] ?? ucfirst($this->agenda_type);
    }
}
