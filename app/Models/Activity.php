<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LoginLog;
use App\Models\RegUser;

class Activity extends Model
{
    protected $table = 'tbl_activities';
    protected $primaryKey = 'activity_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'facility_used',
        'service_type',
        'activity_date',
        'activity_time',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(RegUser::class, 'user_id', 'user_id');
    }

    }
