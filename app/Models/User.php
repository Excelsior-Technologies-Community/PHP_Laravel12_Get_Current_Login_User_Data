<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'status', 'created_by', 'updated_by', 'last_login_at', 'last_login_ip'
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (Auth::check()) {
                $user->created_by = Auth::id();
            }
        });

        static::updating(function ($user) {
            if (Auth::check()) {
                $user->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id')->orderBy('login_at', 'desc');
    }
}