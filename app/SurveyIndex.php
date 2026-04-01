<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyIndex extends Model
{
    protected $fillable = ['title', 'icon', 'value', 'label', 'order', 'is_active'];

    public function getDisplayValueAttribute()
    {
        return rtrim(rtrim(number_format((float) $this->value, 2, '.', ''), '0'), '.');
    }
}
