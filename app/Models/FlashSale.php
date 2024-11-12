<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $table ='flash_sales';

    protected $fillable =[
        'end_date',
    ];

    protected $hidden =[
        'created_at',
        'updated_at'
    ];
}
