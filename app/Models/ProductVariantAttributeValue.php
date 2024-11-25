<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductVariantAttributeValue extends Model
{
    
    protected $table = 'product_variant_attribute_values';



    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the attribute associated with this record
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function variant(){
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
