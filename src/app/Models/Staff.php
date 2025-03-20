<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Notifications\Notifiable;


class Staff extends Authenticatable
{
    use HasFactory, TwoFactorAuthenticatable;

    protected $table = 'staffs';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    use Notifiable;

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->sendEmailVerificationNotification();
        });
    }


}
