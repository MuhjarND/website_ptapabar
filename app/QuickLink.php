<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'url', 'order', 'is_active'];
}
