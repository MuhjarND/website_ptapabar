<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const LEGACY_OFFICE_ADDRESS = 'Jl. Brawijaya, Kelurahan Manokwari Timur, Distrik Manokwari, Provinsi Papua Barat. Kode POS 98311';
    public const OFFICE_ADDRESS = 'Jalan Brigjen Marinir (Purna) Abraham O Atururi, Kompleks Perkantoran Gubernur Provinsi Papua Barat, Katebu, Distrik Manokwari Selatan, Kabupaten Manokwari, Papua Barat 98315';

    public static function officeAddress(?string $address): string
    {
        return !$address || $address === self::LEGACY_OFFICE_ADDRESS ? self::OFFICE_ADDRESS : $address;
    }

    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getMany(array $defaults = [])
    {
        if (empty($defaults)) {
            return [];
        }

        $values = static::whereIn('key', array_keys($defaults))->pluck('value', 'key');

        foreach ($defaults as $key => $default) {
            $defaults[$key] = $values->get($key, $default);
        }

        return $defaults;
    }

    public static function homePopup(): array
    {
        $popup = json_decode(static::get('home_popup') ?? '{}', true);
        return is_array($popup) ? $popup : [];
    }
}
