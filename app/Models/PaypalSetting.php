<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalSetting extends Model
{
    protected $table = 'paypal_settings';

    protected $fillable = [
        'status',
        'mode',
        'country_name',
        'currency_name',
        'currency_rate',
        'client_id',
        'secret_key',
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];
    protected $casts = [
        'currency_rate'=> 'float',
        'status' => 'integer',
    ];
}
