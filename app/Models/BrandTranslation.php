<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'brand_translations';
    protected $fillable = ['name','slug'];
    protected $hidden = ['id','brand_id'];
}