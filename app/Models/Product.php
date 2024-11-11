<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $table = 'products';

    // protected $guarded =['id'];

    protected $fillable = [
        'thumb_image',
        'brand_id',
        'qty',
        'video_link',
        'sku',
        'price',
        'offer_price',
        'offer_start_date',
        'offer_end_date',
        'status',
        'product_type_id',
        'barcode',
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['name','slug','description'];



    /*                                                 Begin Local Scopes                                 */
        public function scopeActive($query) // to show just the active slide in store
        {
            return $query->where('status', 1);
        }
    /*                                                  End Local Scopes                                  */

    /*                                                 Begin GET                                          */
    // public function getThumbImageAttribute($value)
    // {
    //     return ($value !== NULL) ? asset( 'storage/uploads/images/products/thumb-images/'.$value) : " ";
    // }
    /*                                                 End GET                                            */


    /*                                                  Begin Relation                                  */

    public function categories(){
        return $this->belongsToMany(
            Category::class,
            'product_categories',
            'product_id',
            'category_id',
            'id',
            'id'
        );
    }

 

    /**
     * Get all attribute values associated with this product through pivot
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')
                    ->withPivot('attribute_id', 'extra_price', 'quantity', 'is_default')
                    ->withTimestamps();
    }

    /**
     * Get all attributes associated with this product through pivot
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')
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



    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id','id');
    }

    public function gallery(){
        return $this->hasMany(ProductImageGallery::class,'product_id','id');
    }




        // public function reviews(){
        //     return $this->hasMany(ProductReview::class,'product_id','id');
        // }

    /*                                                  End Relation                                    */

}
