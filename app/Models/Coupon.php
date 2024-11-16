<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table ='coupons';

    // protected $guarded =[
    //     'id',
    // ];

    protected $fillable = [
        'name',
        'code',
        'quantity',
        'max_use',
        'start_date',
        'end_date',
        'discount_type',
        'discount',
        'status',
        'total_used',
    ];

    protected $hidden =[
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'max_use' => 'integer',
        'discount' => 'float',
        'status'=> 'integer',
        'total_used' => 'integer',
    ];
    
    // public $timestamps = false;
}
