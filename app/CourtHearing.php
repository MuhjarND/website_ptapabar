<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourtHearing extends Model
{
    protected $fillable = ['hearing_date', 'hearing_time', 'title', 'location', 'is_published'];
    protected $casts = ['hearing_date' => 'date', 'is_published' => 'boolean'];

    public function setHearingDateAttribute($value)
    {
        $this->attributes['hearing_date'] = $this->asDate($value)->format('Y-m-d');
    }
}
