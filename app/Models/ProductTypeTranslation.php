<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'product_type_translations';
    protected $fillable = ['name'];
    protected $hidden = ['id','product_type_id'];
}
