<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeValue extends Model 
{
    use HasFactory;

    protected $table = 'attribute_values';

    protected $fillable = ['attribute_id','name','display_name','color_code','sort_order','status'];

    protected $hidden = [
        'pivot',
        // 'attribute_id',
        'created_at',
        'updated_at'
    ];


    protected $casts = [
        // 'attribute_id'=> 'integer',
        'sort_order'=> 'integer',
        'status'=> 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];




    /**
     * Get the attribute that owns this value
    */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class,'attribute_id','id');
    }

    /**
     * Get all products that have this attribute value through pivot
     */

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
                ->withPivot('attribute_id')
                ->withTimestamps();
    }
    

    public function productAttributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_value_id');
    }

    public function productVariantAttributeValues()
    {
        return $this->hasMany(ProductVariantAttributeValue::class, 'attribute_value_id');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_attribute_values', 'attribute_value_id', 'product_variant_id');
    }

}
