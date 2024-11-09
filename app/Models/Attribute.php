<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

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



   public function values(){
       return $this->hasMany(AttributeValue::class,'attribute_id','id');
   }

   public function products()
   {
       return $this->belongsToMany(Product::class, 'product_attribute_values')
                   ->withPivot('attribute_value_id', 'extra_price', 'quantity');
   }



    
}
