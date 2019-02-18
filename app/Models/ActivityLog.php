<?php

namespace App\Models;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $columns = [
        'id',
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'created_at',
        'updated_at',
    ];

    public function subject()
    {
        $subject = $this->morphTo();
        return method_exists($subject, 'withTrashed') ? $subject->withTrashed() : $subject;
    }

    public function causer()
    {
        $causer = $this->morphTo();
        return method_exists($causer, 'withTrashed') ? $causer->withTrashed() : $causer;
    }
}
