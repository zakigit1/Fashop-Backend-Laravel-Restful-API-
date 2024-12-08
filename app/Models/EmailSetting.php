<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    
    protected $fillable = [
        'name',
        'email',
        'host',
        'username',
        'password',
        'port',
        'encryption',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'port' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
