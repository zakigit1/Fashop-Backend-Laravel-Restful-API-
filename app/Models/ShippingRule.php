<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $table ='shipping_rules';

    // protected $guarded =[
    //     'id',
    // ];

    protected $fillable = [
        'name',
        'type',
        'min_cost',
        'max_cost',
        'cost',
        'weight_limit',
        'description',
        'region',
        'carrier',
        'delivery_time',
        'status',
    ];

    protected $hidden =[
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'min_cost' => 'float',
        'max_cost' => 'float',
        'cost' => 'float',
        'weight_limit'=> 'float',
        'status'=> 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    // public $timestamps = false;
}
