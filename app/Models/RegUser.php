<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegUser extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_regusers';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'fname_user',
        'lname_user',
        'mname_user',
        'suffix_user',
        'birthdate',
        'sex_user',
        'sector_user',
        'number_user',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'user_id');
    }
}
