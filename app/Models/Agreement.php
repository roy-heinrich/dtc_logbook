<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $table = 'tbl_agreement';

    public $timestamps = false;

    protected $fillable = [
        'privacy_info',
        'tos_info',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
