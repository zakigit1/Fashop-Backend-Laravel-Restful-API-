<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    use HasFactory;

    protected $table ='flash_sale_items';

    protected $fillable =[
        'product_id',
        'flash_sale_id',
        'show_at_home',
        'status',
    ];

    protected $hidden =[
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        // 'flash_sale_id' => 'integer',
        'product_id'=>'integer',
        'show_at_home' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];



/*                                                 Begin Local Scopes                                 */
    public function scopeActive($query) // to show just the active slide in store 
    {
        return $query->where('status', 1);
    }       
/*                                                  End Local Scopes                                  */



/*                                                  Begin Relation                                  */
    public function product(): BelongsTo   
    {
        return $this->belongsTo(Product::class,'product_id','id','id');
    }
    


    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class,'flash_sale_id','id','id');
    }
/*                                                  End Relation                                  */

}
