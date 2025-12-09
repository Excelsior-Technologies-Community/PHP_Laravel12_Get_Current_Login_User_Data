<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'status', 'created_by', 'updated_by'
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (Auth::check()) {
                $user->created_by = Auth::id();
               
            }
        });

       
    }
}
