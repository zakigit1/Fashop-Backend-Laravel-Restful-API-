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

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];






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
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
                    ->withPivot('attribute_id', 'extra_price', 'quantity', 'is_default')
                    ->withTimestamps();
    }

    /**
     * Get all product attribute value records
     */
    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
