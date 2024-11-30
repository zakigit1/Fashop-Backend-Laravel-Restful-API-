<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\imageUploadTrait;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{   
    use imageUploadTrait;
    const FOLDER_PATH = '/uploads/images/products/';
    const FOLDER_NAME_BARCODE = 'barcodes';



    public function getProductVariants(string $productId){

        
        $product = Product::find($productId);

        if (!$product) {
            return $this->error('Product Is Not Found',ERROR_CODE);
        }

        $productVariants = ProductVariant::with('productVariantAttributeValues')
                    ->where('product_id', $product->id)
                    ->orderBy('id','ASC')
                    ->paginate(20);

        
    
        $customVariants = $productVariants->getCollection()
            ->map(function ($productVariant) {
                    $productVariantArray = $productVariant->toArray();
                    $attributes = [];

                    foreach ($productVariant->productVariantAttributeValues as $productVariantAttributeValue) {
                        $attributeValue = $productVariantAttributeValue->attributeValue;
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

                    unset($productVariantArray['product_attribute_values']);
                    unset($productVariantArray['product_variant_attribute_values']);

                    ksort($attributes); // Sort the attributes array in ascending order

                    $productVariantArray['attributes'] = array_values($attributes);

                    return $productVariantArray;
                })
            ->all();

        return $this->paginationResponse($productVariants,'productVariants','All Product Variants',SUCCESS_CODE,$customVariants);

    }


    // public function getProductVariantsV2(int $id){

    //     $product = Product::select('id')->find($id);
    //     if (!$product) {
    //         return $this->error('Product Is Not Found',ERROR_CODE);
    //     }

    //     $product->load([                
    //         'productVariantAttributeValues.variant', // eager load the variant relationship
    //         'productVariantAttributeValues.attributeValue.productVariants'
    //     ]);
    //     $customProductVariants= '';


    //     return $this->paginationResponse($product,'productVariants','All Product Variants',SUCCESS_CODE,$customProductVariants);

    // }



    public function storeProductVariant(ProductVariantRequest $request , int $productId){

        // return $request->all();

        try {

            $product = Product::find($productId);
            
            if (!$product) {
                return $this->error('Product Is Not Found',ERROR_CODE);
            }

            
            // Creating a variant
            $variant = ProductVariant::create([
                'product_id' =>(int) $product->id,
                'quantity' => (int) $request->quantity,
                'sku' => $request->sku,
                "final_price" => (float) ($product->price + 0.00),
                "in_stock" => ($request->quantity == 0) ? (int) 0 : (int) 1,
                'variant_hash' => ProductVariant::generateVariantHash($request->attribute_values), // attribute value IDs for red and S
            ]);


            if($request->has('extra_price')){
                $variant->update([
                    "extra_price" =>(float) $request->extra_price,
                    "final_price" =>  number_format((float) ($product->price + $request->extra_price), 2, '.', ','),
                ]);
            }


            // if($request->qunatity == 0){
            //     $variant->update([
            //         'in_stock' => (int) 0
            //     ]);
            // }

            if($request->hasFile('barcode')){
                $barcode_image_name = $this->uploadImage_Trait($request,'barcode',ProductVariantController::FOLDER_PATH,ProductVariantController::FOLDER_NAME_BARCODE);
                $variant->update(['barcode' => $barcode_image_name]);
            }

            
            // update the variant quantity of product: 
            $product->update([
                'variant_quantity' => $product->variant_quantity - $request->quantity,
            ]);




            // Store the variant attribute values
            foreach ($request->attribute_values as $attributeValueId) {
                $variant->attributeValues()->attach(
                    $attributeValueId,
                    [
                        'product_id' => $product->id,
                    ]
                );
            }

            $variant->load('productVariantAttributeValues');

            $customVariant = $variant->toArray();
            $attributes = [];
            
            foreach ($variant->productVariantAttributeValues as $productVariantAttributeValue) {
                $attributeValue = $productVariantAttributeValue->attributeValue;
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

            ksort($attributes); // Sort the attributes array in ascending order
            $customVariant['attributes'] = array_values($attributes);
            unset($customVariant['product_variant_attribute_values']);

            return $this->success($customVariant,'Product Variant Created Successfully!',SUCCESS_STORE_CODE,'productVariant');

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }


    public function updateProductVariant(ProductVariantRequest $request , int $productId, int $id){

        // return $request->all();

        try {

            $product = Product::find($productId);
            if (!$product) {
                return $this->error('Product Is Not Found',ERROR_CODE);
            }


            $variant = ProductVariant::find($id);
            if (!$variant) {
                return $this->error('This Product Variant Is Not Found',ERROR_CODE);
            }

            $variantHashOld = $variant->variant_hash;
            
            $attributeValueIds = $request->input('attribute_values');
            $variantHash = ProductVariant::generateVariantHash($attributeValueIds);
            

            //extra price : 
            if($request->has('extra_price')){
                $variant->update([
                    'quantity' =>(int) $request->quantity,
                    'sku' => $request->sku,
                    "extra_price" =>(float) $request->extra_price,
                    "final_price" => (float) ($product->price + $request->extra_price),
                    "in_stock" => ($request->quantity == 0) ? (int) 0 : (int) 1,
                    'variant_hash' => $variantHash,
                ]);
            }else{
                $variant->update([
                    'quantity' =>(int) $request->quantity,
                    'sku' => $request->sku,
                    "extra_price" => (float) 0.00,
                    "final_price" => (float) ($product->price + 0.00 ),
                    // "final_price" => (float) ($product->price - $variant->extra_price ),
                    "in_stock" => ($request->quantity == 0) ? (int) 0 : (int) 1,
                    'variant_hash' => $variantHash,
                ]);
            }

            // barcode 
            if($request->hasFile('barcode')){
                $old_barcode_image = $variant->barcode;
                $barcode_image_name = $this->updateImage_Trait($request,'barcode',ProductVariantController::FOLDER_PATH,ProductVariantController::FOLDER_NAME_BARCODE,$old_barcode_image);
                $variant->update(['barcode' => $barcode_image_name]);
            }


            // update the variant quantity of product: 
            $product->update([
                'variant_quantity' => ($product->variant_quantity + $variant->quantity) - $request->quantity,
            ]);


            // store new values 
            if($variantHashOld  != $variantHash){
               
                $variant->attributeValues()->detach();// delete the product attribute values 
            
                foreach ($request->attribute_values as $attributeValueId) {
                    $variant->attributeValues()->attach(
                        $attributeValueId,
                        [
                            'product_id' => $productId,
                        ]
                    );
                } 
            } 
            
            
            $variant->load('productVariantAttributeValues');

            $customVariant = $variant->toArray();
            $attributes = [];
            
            foreach ($variant->productVariantAttributeValues as $productVariantAttributeValue) {
                $attributeValue = $productVariantAttributeValue->attributeValue;
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
            
            $customVariant['attributes'] = array_values($attributes);
            unset($customVariant['product_variant_attribute_values']);

            

            return $this->success($customVariant,'Variant Updated Successfully!',SUCCESS_CODE,'productVariants');

        } catch (\Exception $e) {
            // Handle the exception, for example:
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }


    public function deleteProductVariant(int $productId ,int $id){
        try {
            
            $variant = ProductVariant::where('product_id',$productId)->find($id);

            if (!$variant) {
                return $this->error('Variant is not found', NOT_FOUND_ERROR_CODE);
            }

            
            $product = Product::where('id',$variant->product_id)->select('id','variant_quantity')->first();


            $product->update([
                'variant_quantity' => $product->variant_quantity + $variant->quantity ,
            ]);



            $variant->delete();

            return $this->success(null, 'Variant Deleted Successfully', SUCCESS_CODE, 'productVariants');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }

    }




    // public function getVariantPrice(Request $request)
    public function getVariantPrice(Request $request,int $id)
    {
        $attributeValueIds = $request->input('attribute_values');
        $variantHash = ProductVariant::generateVariantHash($attributeValueIds);
        
        // $variant = ProductVariant::where('product_id', $request->product_id)
        $variant = ProductVariant::where('product_id', $id)
            ->where('variant_hash', $variantHash)
            ->select('final_price', 'in_stock', 'quantity')
            ->first();
            

        return $this->success($variant,'Get Variant Price',SUCCESS_CODE,'getVariantPrice');
        // return response()->json([
        //     'price' => $variant->final_price,
        //     'in_stock' => $variant->in_stock,
        //     'quantity' => $variant->quantity
        // ]);
    }
}
