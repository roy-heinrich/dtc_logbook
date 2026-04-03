<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSnapshot extends Model
{
    protected $fillable = [
        'snapshot_key',
        'payload',
        'refreshed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'refreshed_at' => 'datetime',
    ];
}
