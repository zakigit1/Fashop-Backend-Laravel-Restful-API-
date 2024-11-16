<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CODSetting extends Model
{
    protected $table = 'c_o_d_settings';
    protected $fillable =[ 
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
