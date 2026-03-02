<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $primaryKey = 'login_log_id';

    protected $fillable = [
        'user_id',
        'activity_id',
        'user_type',
        'login_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    /**
     * Get the owning user (Admin or User)
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the associated activity
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');    }
}