<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteApplication extends Model
{
    public const GROUP_PTA_INOVASI = 'pta_inovasi';
    public const GROUP_MAHKAMAH_AGUNG = 'mahkamah_agung';

    protected $fillable = ['title', 'description', 'icon', 'url', 'group_type', 'order', 'is_active'];

    public static function groupOptions()
    {
        return [
            self::GROUP_PTA_INOVASI => 'Aplikasi Inovasi PTA Papua Barat',
            self::GROUP_MAHKAMAH_AGUNG => 'Aplikasi Mahkamah Agung',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
