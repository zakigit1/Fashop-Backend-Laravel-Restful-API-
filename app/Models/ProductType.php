<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductType extends Model implements TranslatableContract
{

    use HasFactory,Translatable;

    protected $table = 'product_types';

    protected $fillable = ['status'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
    ];


    public $translatedAttributes = ['name'];


    public function products(){
        return $this->hasMany(Product::class,'product_type_id','id');
    }
}
