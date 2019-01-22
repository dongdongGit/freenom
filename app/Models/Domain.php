<?php

namespace App\Models;

class Domain extends Model
{
    protected $casts = [
        'user_id'            => 'integer',
        'enabled_auto_renew' => 'boolean',
        'register_time'      => 'date',
        'expires_time'       => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
