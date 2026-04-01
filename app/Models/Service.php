<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_services';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'services_name',
    ];
}
