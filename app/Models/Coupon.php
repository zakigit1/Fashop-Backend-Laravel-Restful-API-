<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table ='coupons';

    // protected $guarded =[
    //     'id',
    // ];

    protected $fillable = [
        'name', // string (required)
        'code', // string (unique) (required)
        'quantity',//integer (required)
        'max_use',//integer (required)
        'start_date',//date (required)
        'end_date',//date (required)
        'discount_type',//enum ('amount', 'percentage') (required)
        'discount',//float (required)
        'status',//tinyinteger (required)
        'total_used',//integer
        'min_purchase_amount'//float (default 0.00)
    ];

    protected $hidden =[
        'created_at',
        'updated_at',
        'pivot'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'max_use' => 'integer',
        'status'=> 'integer',

        'discount' => 'float',
        'min_purchase_amount' => 'float',

        'total_used' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    // public $timestamps = false;


    public function users(){
        return $this->belongsToMany(User::class,
        'coupon_users',
        'coupon_id',
        'user_id',
        'id',
        'id'
    );
    }
}
