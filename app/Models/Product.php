<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

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
        'status'
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['name','slug','description','product_type'];



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
        public function brand(){
            return $this->belongsTo(Brand::class,'brand_id','id','id');
        }

        public function gallery(){
            return $this->hasMany(ProductImageGallery::class,'product_id','id');
        }


        // public function attributes(){

        //     return $this->hasMany(ProductVariant::class,'product_id','id');
        // }



        // public function reviews(){
        //     return $this->hasMany(ProductReview::class,'product_id','id');
        // }

    /*                                                  End Relation                                    */

}
