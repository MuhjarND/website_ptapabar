<?php

namespace App\Support;

use App\ActivityLog;

class ActivityLogger
{
    public static function record($action, $subject = null, $description = null, array $properties = [])
    {
        $user = auth()->user();

        ActivityLog::create([
            'user_id' => optional($user)->id,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => optional($subject)->id,
            'subject_title' => optional($subject)->title ?? optional($subject)->name,
            'description' => $description,
            'properties' => $properties ?: null,
        ]);
    }
}
