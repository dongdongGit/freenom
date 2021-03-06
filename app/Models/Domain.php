<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;

class Domain extends Model
{
    use LogsActivity;

    protected static $logName = 'freenom_update';

    protected static $ignoreChangedAttributes = ['updated_at'];

    protected static $logAttributes = ['renew', 'expires_date'];

    protected static $recordEvents = [];

    protected static $logOnlyDirty = true;

    protected $casts = [
        'renew'              => 'integer',
        'domain_id'          => 'integer',
        'user_id'            => 'integer',
        'enabled_auto_renew' => 'boolean',
        'register_date'      => 'date',
        'expires_date'       => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
