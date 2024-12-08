<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'extra_price',
        'final_price',
        'quantity',
        'sku',
        'barcode',
        'in_stock',
        'variant_hash'
    ];

    protected $hidden = [
        'variant_hash',
    ];
    protected $casts = [
        'product_id' => 'integer',
        'extra_price' => 'float',
        'final_price' => 'float',
        'quantity' => 'integer',
        'in_stock' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // public $timestamps = false;

    /**
     * Get the product that owns the variant
    */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute values for this variant
    */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_values')
                    ->withTimestamps();
    }

    public function productVariantAttributeValues()
    {
        return $this->hasMany(ProductVariantAttributeValue::class);
    }


    /**
     * Generate variant hash from attribute value IDs
     */
    public static function generateVariantHash(array $attributeValueIds): string
    {
        sort($attributeValueIds); // Sort to ensure consistent hash
        return md5(implode('-', $attributeValueIds));
    }

    /**
     * Update stock status based on quantity
     */
    public function updateStockStatus(): void
    {
        $this->in_stock = $this->quantity > 0;
        $this->save();
    }
}
