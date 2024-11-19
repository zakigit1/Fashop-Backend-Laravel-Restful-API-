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
        'variant_quantity',
        'video_link',
        'price',
        'offer_price',
        'offer_start_date',
        'offer_end_date',
        'status',
        'product_type_id',
        
    ];

    protected $hidden = [
        'pivot',
    //     'created_at',
    //     'updated_at'
    ];


    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'brand_id' => 'integer',
        'product_type_id' => 'integer',
        'price' => 'float',
        'offer_price' => 'float',
        // 'offer_start_date' => 'datetime',
        // 'offer_end_date' => 'datetime',
        'qty' => 'integer',
        'status' => 'integer',
    ];

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

    public function categories(){//i change it to category because we are storing just one category in product (categories )
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
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')
                    ->select(['attribute_values.id','name','display_name','color_code'])
                    ->withPivot('attribute_id')
                    ->withTimestamps();
    }
    


    /**
     *  
     * Get all attributes associated with this product through pivot
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')
                ->select(['attributes.id'])
                // ->withPivot('attribute_value_id')//ida bgghit tzid t3rad fl pivot attribute_value_id
                ->withTimestamps()
                ->distinct();// remove the repetition
                    
    }
    

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id','id');
    }

    public function gallery(){
        return $this->hasMany(ProductImageGallery::class,'product_id','id');
    }


    public function productType(){
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }



    // public function reviews(){
    //     return $this->hasMany(ProductReview::class,'product_id','id');
    // }

    /*                                                  End Relation                                    */

}
