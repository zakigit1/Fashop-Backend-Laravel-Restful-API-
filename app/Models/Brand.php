<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Brand extends Model implements TranslatableContract

{
    use HasFactory;
    use Translatable;

    protected $table = 'brands';

    protected $fillable = ['logo','status'];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public $translatedAttributes = ['name','slug'];










    /*                                                 Begin GET                                          */
    public function getLogoAttribute($value)
    {
        $base_url = env('BASE_URL_API','http://localhost:8000');
        // return ($value !== NULL) ? asset( $base_url.'/storage/uploads/images/brands/'.$value) : " ";
        
        return ($value !== NULL) ? asset( '/storage/uploads/images/brands/'.$value) : " ";
    }
/*                                                 End GET                                            */




    /*                                                  Begin Relation                                  */


    public function products(){
        return $this->hasMany(Product::class,'brand_id','id');
    }



/*                                                  End Relation                                  */












}






