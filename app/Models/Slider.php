<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

// class Slider extends Model implements TranslatableContract
class Slider extends Model 
{

    // use HasFactory,Translatable;

    protected $table = 'sliders';

    protected $fillable = [
        'image',
        // 'background_color',
        // 'title_color',
        // 'description_color',
        'button_link',
        'order',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // public $translatedAttributes = ['title','description'];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
    */
    protected $casts = [
        'order'=> 'integer',
        'status' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];




        /**
     * Get the created at attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->setTimezone('Africa/Algiers')->format('Y-m-d H:i:s');
    }

    /**
     * Get the updated at attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->setTimezone('Africa/Algiers')->format('Y-m-d H:i:s');
    }
}
