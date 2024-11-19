<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model implements TranslatableContract
{
    use HasFactory; 
    use Translatable;

    protected $table = 'categories';

    protected $fillable = ['parent_id','status','icon'];

    protected $hidden = [   
        'pivot',
    //     'created_at',
    //     'updated_at'
    ];

    public $translatedAttributes = ['name','slug'];

    protected $casts = [
        // 'parent_id'=> 'integer',
        'status'=> 'integer',
    ];



    /**###################################################Scopes Start################################################## */
        public function scopeParent($query){
            return $query -> whereNull('parent_id');
        }

        public function scopeChild($query){
            return $query -> whereNotNull('parent_id');
        }
        public function scopeActive($query){
            return $query -> where('is_active', 1) ;
        }
    /**###################################################Scopes End#################################################### */





    /**###################################################Relations End#################################################### */
        public function _parent():BelongsTo
        {
            return $this->belongsTo(Category::class, 'parent_id');
        }

        public function children(): HasMany
        {
            return $this->hasMany(Category::class, 'parent_id');
        }


        public function products()
        {
            return $this -> belongsToMany(
                Product::class,
                'product_categories',
                'category_id',
                'product_id',
                'id',
                'id'
            );
        }
    /**###################################################Relations End#################################################### */















}








   

