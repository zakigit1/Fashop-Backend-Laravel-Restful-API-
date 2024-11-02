<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name'];
    protected $hidden = ['id','attribute_id'];
}
