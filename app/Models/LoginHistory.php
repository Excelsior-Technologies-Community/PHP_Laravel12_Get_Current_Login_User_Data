<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_at'
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    public function getDeviceInfoAttribute()
    {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);

        $browser = $agent->browser();
        $platform = $agent->platform();

        return $browser . ' on ' . $platform;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}