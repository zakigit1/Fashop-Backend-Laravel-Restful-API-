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

    protected $fillable = ['type','is_filterable','sort_order','status'];

    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at'
    ];

    public $translatedAttributes = ['name'];


    // public $timestamps = false;

    protected $casts = [

        'is_filterable'=> 'integer',
        'sort_order'=> 'integer',
        'status'=> 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];



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
                    ->withPivot('attribute_value_id')
                    ->withTimestamps();
    }


    public function productVariantAttributeValues()
    {
        return $this->hasMany(ProductVariantAttributeValue::class, 'attribute_id');
    }


}
