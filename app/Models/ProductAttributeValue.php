<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $table = 'product_attribute_values';

    protected $fillable = ['product_id','attribute_id','attribute_value_id','attribute_value_id','extra_price', 'quantity','is_default'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];
}
