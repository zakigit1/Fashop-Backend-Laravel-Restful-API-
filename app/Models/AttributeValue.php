<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class AttributeValue extends Model implements TranslatableContract
{
    use HasFactory,Translatable;

    protected $table = 'attribute_values';

    protected $fillable = ['attribute_id','color_code','sort_order','extra_price','quantity','is_default','status'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['name','display_name'];

    public function attribute(){
        return $this->belongsTo(Attribute::class,'attribute_id','id');
    }
}
