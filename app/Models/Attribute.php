<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model implements TranslatableContract
{
    use HasFactory,Translatable;

    protected $table = 'attributes';

    protected $fillable = ['type','is_required','is_filterable','sort_order','status'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['name'];






   /**
     * Get all possible values for this attribute
     */
    public function values(): HasMany
    {
        
        return $this->hasMany(AttributeValue::class,'attribute_id','id');
    }

    /**
     * Get all products that have this attribute through pivot
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
                    ->withPivot('attribute_value_id', 'extra_price', 'quantity', 'is_default')
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
