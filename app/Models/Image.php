<?php

namespace App\Models;

class Image extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPathAttribute($path)
    {
        return asset('uploads/' . $path);
    }
}
