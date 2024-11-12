<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Slider extends Model implements TranslatableContract
{

    use HasFactory,Translatable;

    protected $table = 'sliders';

    protected $fillable = ['image','image_url','button_link','order','status'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['title','description'];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
    */
    protected $casts = [
        'order'=> 'integer',
        'status' => 'integer',
    ];


}
