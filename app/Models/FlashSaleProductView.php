<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSaleProductView extends Model
{
    protected $table = 'flash_sale_products_view';
    public $timestamps = false;
    protected $primaryKey = 'product_id';
}
