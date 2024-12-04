<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{   
    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/products/';
    private const FOLDER_NAME_BARCODE = 'barcodes';
    private const ITEMS_PER_PAGE = 20;

    public function getProductVariants(string $productId): JsonResponse
    {
        try {
            $product = $this->findProduct($productId);
            $variants = $this->fetchProductVariants($product->id);
            $transformedVariants = $this->transformVariants($variants);

            return $this->paginationResponse(
                $variants,
                'productVariants',
                'All Product Variants',
                SUCCESS_CODE,
                $transformedVariants
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function storeProductVariant(ProductVariantRequest $request, int $productId): JsonResponse
    {
        try {
            $product = $this->findProduct($productId);
            $variant = $this->createVariant($request, $product);
            
            $this->handleExtraPrice($variant, $request, $product);
            $this->handleBarcodeUpload($variant, $request);
            $this->updateProductVariantQuantity($product, $request->quantity);
            $this->attachAttributeValues($variant, $request->attribute_values, $product->id);

            $transformedVariant = $this->transformSingleVariant($variant);

            return $this->success(
                $transformedVariant,
                'Product Variant Created Successfully!',
                SUCCESS_STORE_CODE,
                'productVariant'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function updateProductVariant(ProductVariantRequest $request, int $productId, int $id): JsonResponse
    {
        // return $productId;
        try {
            $product = $this->findProduct($productId);
            $variant = $this->findVariant($id,$productId);
            
            $variantHashOld = $variant->variant_hash;
            $variantHash = ProductVariant::generateVariantHash($request->attribute_values);

            $this->updateVariantDetails($variant, $request, $product, $variantHash);
            $this->handleBarcodeUpdate($variant, $request);
            $this->updateProductQuantity($product, $variant, $request->quantity);

            if ($variantHashOld !== $variantHash) {
                $this->updateAttributeValues($variant, $request->attribute_values, $productId);
            }

            $transformedVariant = $this->transformSingleVariant($variant);

            return $this->success(
                $transformedVariant,
                'Variant Updated Successfully!',
                SUCCESS_CODE,
                'productVariants'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function deleteProductVariant(int $productId, int $id): JsonResponse
    {
        try {
            $variant = $this->findVariantByProduct($productId, $id);
            $this->restoreProductQuantity($variant);
            $variant->delete();

            return $this->success(null, 'Variant Deleted Successfully', SUCCESS_CODE, 'productVariants');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    public function getVariantPrice(Request $request, int $id): JsonResponse
    {
        try {
            $variant = $this->findVariantByHash($request->input('attribute_values'), $id);
            return $this->success($variant, 'Get Variant Price', SUCCESS_CODE, 'getVariantPrice');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    private function findProduct(string $productId): Product
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Product Is Not Found');
        }
        return $product;
    }

    private function findVariant(int $id , int $productId): ProductVariant
    {
        $variant = ProductVariant::where('product_id',$productId)->find($id);
        if (!$variant) {
            throw new \Exception('This Product Variant Is Not Found');
        }
        return $variant;
    }

    private function findVariantByProduct(int $productId, int $id): ProductVariant
    {
        $variant = ProductVariant::where('product_id', $productId)->find($id);
        if (!$variant) {
            throw new \Exception('Variant is not found');
        }
        return $variant;
    }

    private function findVariantByHash(array $attributeValueIds, int $productId): ?ProductVariant
    {
        $variantHash = ProductVariant::generateVariantHash($attributeValueIds);
        return ProductVariant::where('product_id', $productId)
            ->where('variant_hash', $variantHash)
            ->select('final_price', 'in_stock', 'quantity')
            ->first();
    }

    private function fetchProductVariants(int $productId)
    {
        return ProductVariant::with('productVariantAttributeValues')
            ->where('product_id', $productId)
            ->orderBy('id', 'ASC')
            ->paginate(self::ITEMS_PER_PAGE);
    }

    private function transformVariants($variants): array
    {
        return $variants->getCollection()
            ->map(function ($variant) {
                return $this->transformSingleVariant($variant);
            })
            ->all();
    }

    private function transformSingleVariant(ProductVariant $variant): array
    {
        $variant->load('productVariantAttributeValues');
        $variantArray = $variant->toArray();
        $attributes = $this->transformVariantAttributes($variant);
        
        $variantArray['attributes'] = array_values($attributes);
        unset($variantArray['product_variant_attribute_values']);
        
        return $variantArray;
    }

    private function transformVariantAttributes(ProductVariant $variant): array
    {
        $attributes = [];
        
        foreach ($variant->productVariantAttributeValues as $value) {
            $attributeValue = $value->attributeValue;
            $attribute = $attributeValue->attribute;
            
            if (!isset($attributes[$attribute->id])) {
                $attributes[$attribute->id] = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'translations' => $attribute->translations,
                    'value' => [
                        'id' => $attributeValue->id,
                        'name' => $attributeValue->name,
                        'display_name' => $attributeValue->display_name,
                        'color_code' => $attributeValue->color_code,
                    ],
                ];
            }
        }
        
        ksort($attributes);
        return $attributes;
    }

    private function createVariant(ProductVariantRequest $request, Product $product): ProductVariant
    {
        return ProductVariant::create([
            'product_id' => (int) $product->id,
            'quantity' => (int) $request->quantity,
            'sku' => $request->sku,
            'final_price' => (float) ($product->price + 0.00),
            'in_stock' => (int) ($request->quantity > 0),
            'variant_hash' => ProductVariant::generateVariantHash($request->attribute_values),
        ]);
    }

    private function handleExtraPrice(ProductVariant $variant, ProductVariantRequest $request, Product $product): void
    {
        if ($request->has('extra_price')) {
            $variant->update([
                'extra_price' => (float) $request->extra_price,
                'final_price' => number_format((float) ($product->price + $request->extra_price), 2, '.', ','),
            ]);
        }
    }

    private function handleBarcodeUpload(ProductVariant $variant, ProductVariantRequest $request): void
    {
        if ($request->hasFile('barcode')) {
            $barcodeName = $this->uploadImage_Trait(
                $request,
                'barcode',
                self::FOLDER_PATH,
                self::FOLDER_NAME_BARCODE
            );
            $variant->update(['barcode' => $barcodeName]);
        }
    }

    private function handleBarcodeUpdate(ProductVariant $variant, ProductVariantRequest $request): void
    {
        if ($request->hasFile('barcode')) {
            $barcodeName = $this->updateImage_Trait(
                $request,
                'barcode',
                self::FOLDER_PATH,
                self::FOLDER_NAME_BARCODE,
                $variant->barcode
            );
            $variant->update(['barcode' => $barcodeName]);
        }
    }

    private function updateProductVariantQuantity(Product $product, int $quantity): void
    {
        $product->update([
            'variant_quantity' => $product->variant_quantity - $quantity,
        ]);
    }

    private function updateProductQuantity(Product $product, ProductVariant $variant, int $newQuantity): void
    {
        $product->update([
            'variant_quantity' => ($product->variant_quantity + $variant->quantity) - $newQuantity,
        ]);
    }

    private function restoreProductQuantity(ProductVariant $variant): void
    {
        $product = Product::where('id', $variant->product_id)
            ->select('id', 'variant_quantity')
            ->first();

        $product->update([
            'variant_quantity' => $product->variant_quantity + $variant->quantity,
        ]);
    }

    private function attachAttributeValues(ProductVariant $variant, array $attributeValues, int $productId): void
    {
        foreach ($attributeValues as $attributeValueId) {
            $variant->attributeValues()->attach($attributeValueId, [
                'product_id' => $productId,
            ]);
        }
    }

    private function updateAttributeValues(ProductVariant $variant, array $attributeValues, int $productId): void
    {
        $variant->attributeValues()->detach();
        $this->attachAttributeValues($variant, $attributeValues, $productId);
    }

    private function updateVariantDetails(
        ProductVariant $variant,
        ProductVariantRequest $request,
        Product $product,
        string $variantHash
    ): void {
        $updateData = [
            'quantity' => (int) $request->quantity,
            'sku' => $request->sku,
            'in_stock' => (int) ($request->quantity > 0),
            'variant_hash' => $variantHash,
        ];

        if ($request->has('extra_price')) {
            $updateData['extra_price'] = (float) $request->extra_price;
            $updateData['final_price'] = (float) ($product->price + $request->extra_price);
        } else {
            $updateData['extra_price'] = 0.00;
            $updateData['final_price'] = (float) $product->price;
        }

        $variant->update($updateData);
    }
}
