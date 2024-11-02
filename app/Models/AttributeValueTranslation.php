<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValueTranslation extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $fillable = ['name','display_name'];
    protected $hidden = ['id','attribute_value_id'];
}
