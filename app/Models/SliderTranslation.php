<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderTranslation extends Model
{

    public $timestamps = false;
    protected $fillable = ['title', 'description'];
    protected $hidden = ['id','slider_id'];
}
