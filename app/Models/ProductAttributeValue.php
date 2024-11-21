<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $table = 'product_attribute_values';

    
    // protected $fillable = [
    //     'product_id',
    //     'attribute_id',
    //     'attribute_value_id',
    // ];


    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];


    
     /**
     * Get the product that owns this attribute value
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute associated with this record
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the attribute value associated with this record
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
