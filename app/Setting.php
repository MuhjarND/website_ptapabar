<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
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
}
