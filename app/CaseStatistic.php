<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseStatistic extends Model
{
    protected $fillable = ['period', 'opening', 'received', 'decided', 'closing', 'source', 'is_published'];
    protected $casts = ['period' => 'date', 'is_published' => 'boolean'];

    public function setPeriodAttribute($value)
    {
        $this->attributes['period'] = $this->asDate($value)->format('Y-m-d');
    }
}
