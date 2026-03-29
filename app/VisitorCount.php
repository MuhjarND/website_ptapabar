<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorCount extends Model
{
    protected $fillable = ['visit_date', 'count'];
    protected $dates = ['visit_date'];
}
