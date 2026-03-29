<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyIndex extends Model
{
    protected $fillable = ['title', 'icon', 'value', 'label', 'order', 'is_active'];
}
