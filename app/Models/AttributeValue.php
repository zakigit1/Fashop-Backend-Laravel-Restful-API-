<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AttributeValue extends Model 
{
    use HasFactory;

    protected $table = 'attribute_values';

    protected $fillable = ['attribute_id','name','display_name','color_code','sort_order','status'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];



    public function attribute(){
        return $this->belongsTo(Attribute::class,'attribute_id','id');
    }

    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->withPivot('attribute_id','attribute_value_id','extra_price', 'quantity','is_default');
    }
}
