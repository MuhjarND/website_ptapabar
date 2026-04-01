<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorSession extends Model
{
    protected $fillable = [
        'visitor_key',
        'ip_address',
        'user_agent',
        'device_type',
        'country_code',
        'country_name',
        'total_requests',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $dates = ['first_seen_at', 'last_seen_at'];
}
