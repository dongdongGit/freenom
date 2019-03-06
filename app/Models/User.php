<?php

namespace App\Models;

use ElemenX\ApiPagination\Paginatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, Paginatable;

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class)->latest();
    }

    public function images()
    {
        return $this->hasMany(Image::class)->latest('id');
    }
}
