<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductType;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductNewController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/products/';
    const FOLDER_NAME_THUMB_IMAGE = 'thumb-images';


    public function get_attributes():JsonResponse
    {

        try{
            /* Get Attributes: (Attributes With Values) **/

            $attributes = Attribute::with([
                'translations',
                'values'=>function($q){
                    $q->select('id','attribute_id','name','display_name','color_code');
                } 
            ])->select('id')
            ->orderBy('id','asc')
            ->get();

            return $this->success($attributes,'All Attributes with Values',SUCCESS_CODE,'attributes');
            
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);  
        }    
    }


    public function get_product_types():JsonResponse
    {

        try{
            /* Get Attributes: (Attributes With Values) **/

            $product_types = ProductType::with([
                'translations',
            ])->select('id')
            ->orderBy('id','asc')
            ->get();

            return $this->success($product_types,'All Product Types',SUCCESS_CODE,'productTypes');
            
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);  
        }    
    }


    public function index():JsonResponse
    {
        try{

            /* Get All Products: **/
            $products = Product::with([
                'translations',
                'category',
                'brand',
                'productType',
                'productAttributeValues.attributeValue.attribute',
                // 'attributeValues'=>function($q){
                //     $q->select('attribute_values.id','attribute_values.attribute_id','name','display_name','color_code');
                // }  ,
                // 'category' => function($q){
                //     $q->select('category.id');    
                // },
                // 'variants'=>function($q){
                //     $q->with('attributeValues');
                // },
                'productVariantAttributeValues.variant', // eager load the variant relationship
                'productVariantAttributeValues.attributeValue.productVariants',
                'gallery',
                ])
            ->orderBy('id','ASC')
            ->paginate(20);


            // /** For custom attributes with values for product  */
            // $customProducts = $products->getCollection()
            //     ->map(function ($product) {
            //         $productArray = $product->toArray();
            //         $attributes = [];
        
            //         foreach ($product->productAttributeValues as $productAttributeValue) {
            //             $attributeValue = $productAttributeValue->attributeValue;
            //             $attribute = $attributeValue->attribute;
        
            //             if (!isset($attributes[$attribute->id])) {
            //                 $attributes[$attribute->id] = [
            //                     'id' => $attribute->id,
            //                     'name' => $attribute->name,
            //                     'translations' => $attribute->translations,
            //                     'values' => []
            //                 ];
            //             }
        
            //             $attributes[$attribute->id]['values'][] = [
            //                 'id' => $attributeValue->id,
            //                 'name' => $attributeValue->name,
            //                 'display_name' => $attributeValue->display_name,
            //                 'color_code' => $attributeValue->color_code,
            //             ];
            //         }
        
            //         $productArray['attributes'] = array_values($attributes);
            //         unset($productArray['product_attribute_values']);
        
            //         return $productArray;
            //     })
            // ->all();


            // /** For custom the attributes with values inside relation variants   */
            // $customProducts = $products->getCollection()
            // ->map(function ($product) {
            //     $productArray = $product->toArray();
            //     $variants = [];
            //     $attributes = [];

            //     foreach ($product->productVariantAttributeValues as $productVariantAttributeValue) {
            //         $attributeValue = $productVariantAttributeValue->attributeValue;
            //         $attribute = $attributeValue->attribute;
            //         $variant = $productVariantAttributeValue->variant;

            //         if (!isset($variants[$variant->id])) {
            //             $variants[$variant->id] = [
            //                 'id' => $variant->id,
            //                 'extra_price' => $variant->extra_price,
            //                 'final_price' => $variant->final_price,
            //                 'quantity' => $variant->quantity,
            //                 'sku' => $variant->sku,
            //                 'barcode' => $variant->barcode,
            //                 'variant_hash' => $variant->variant_hash,
            //                 'attributes' => []
            //             ];
            //         }

            //         if (!isset($variants[$variant->id]['attributes'][$attribute->id])) {
            //             $attributes[$attribute->id] = [
            //                 'id' => $attribute->id,
            //                 'name' => $attribute->name,
            //                 'translations' => $attribute->translations,
            //                 'value' => [
            //                     'id' => $attributeValue->id,
            //                     'name' => $attributeValue->name,
            //                     'display_name' => $attributeValue->display_name,
            //                     'color_code' => $attributeValue->color_code,
            //                 ],
            //             ];
            //             $variants[$variant->id]['attributes'][$attribute->id] = $attributes[$attribute->id];
            //         }
            //     }

            //     // Reset the array keys for the "attributes" array
            //     foreach ($variants as &$variant) {
            //         $variant['attributes'] = array_values($variant['attributes']);
            //     }

            //     $productArray['variants'] = array_values($variants);
            //     unset($productArray['product_variant_attribute_values']);

            //     return $productArray;
            // })
            // ->all();

            /** Fusion of two previous code i commented  */
           
            $customProducts = $products->getCollection()
                ->map(function ($product) {
                    $productArray = $product->toArray();

                    // Remove product_id from gallery items
                    if (isset($productArray['gallery'])) {
                        $productArray['gallery'] = collect($productArray['gallery'])->map(function ($galleryItem) {
                            unset($galleryItem['product_id']);
                            return $galleryItem;
                        })->toArray();
                    }

                    $attributes = [];
                    $variants = [];
               
                    // First transformation: custom attributes with values for product
                    foreach ($product->productAttributeValues as $productAttributeValue) {
                        $attributeValue = $productAttributeValue->attributeValue;
                        $attribute = $attributeValue->attribute;

                        if (!isset($attributes[$attribute->id])) {
                            $attributes[$attribute->id] = [
                                'id' => $attribute->id,
                                'name' => $attribute->name,
                                'translations' => $attribute->translations,
                                'values' => []
                            ];
                        }

                        $attributes[$attribute->id]['values'][] = [
                            'id' => $attributeValue->id,
                            'name' => $attributeValue->name,
                            'display_name' => $attributeValue->display_name,
                            'color_code' => $attributeValue->color_code,
                        ];
                    }

                    // Second transformation: custom attributes with values inside relation variants
                    foreach ($product->productVariantAttributeValues as $productVariantAttributeValue) {
                        $attributeValue = $productVariantAttributeValue->attributeValue;
                        $attribute = $attributeValue->attribute;
                        $variant = $productVariantAttributeValue->variant;

                        if (!isset($variants[$variant->id])) {
                            $variants[$variant->id] = [
                                'id' => $variant->id,
                                'extra_price' => $variant->extra_price,
                                'final_price' => $variant->final_price,
                                'quantity' => $variant->quantity,
                                'sku' => $variant->sku,
                                'barcode' => $variant->barcode,
                                // 'variant_hash' => $variant->variant_hash,
                                'attributes' => []
                            ];
                        }

                        if (!isset($variants[$variant->id]['attributes'][$attribute->id])) {
                            $variants[$variant->id]['attributes'][$attribute->id] = [
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

                    // Reset the array keys for the "attributes" array
                    foreach ($variants as &$variant) {
                        ksort($variant['attributes']); // Sort the attributes array in ascending order
                        $variant['attributes'] = array_values($variant['attributes']);
                    }

                    



                    $productArray['attributes'] = array_values($attributes);
                    $productArray['variants'] = array_values($variants);
                  
                    unset($productArray['product_attribute_values']);
                    unset($productArray['product_variant_attribute_values']);
                    
                    return $productArray;
            })
            ->all();


            return $this->paginationResponse($products,'products','All Products',SUCCESS_CODE,$customProducts);
            
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);  
        }

    }


    public function store(ProductRequest $request): JsonResponse
    {
        // return $request->all();
        try{
            DB::beginTransaction();
        
            /** Save thumb_image  */

            $image_name= $this->uploadImage_Trait($request,'thumb_image',self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);
           

            $product = Product::create([
                "thumb_image" => $image_name,
                "brand_id" => (is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" => (is_null($request->product_type_id)) ? $request->product_type_id :(int) $request->product_type_id,
                "qty" => (int) $request->qty,
                "variant_quantity" => (int) $request->qty ,//firstly take the same value of original quantity (qty) after when we add variant we be decrement depending on qty of variants.
                "price" => (float) $request->price ,
                "offer_price" => (is_null($request->offer_price)) ? $request->offer_price :(float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" => (int) $request->status,
            ]);

        
            $product->category()->syncWithoutDetaching($request->category_id);
        
            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }

            $product->save() ;
            

            // save product attributes values : 
                // Expected request format:
                    // attributes : [
                    //     {
                    //         attribute_id: 1, // size
                    //         values: [
                    //             {
                    //                 attribute_value_id: 1, // S
                    //             },
                    //             {
                    //                 attribute_value_id: 2, // M
                    //             }
                    //         ]
                    //     }
                    // ]
                
            $this->storeProductAttributeValue($request->productAttributes,$product);

            // $product->load([
            //     'category'=>function($query){
            //         $query->select('category.id');
            //     },'brand'=>function($query){
            //         $query->select('brand.id');
            //     },'productType'=>function($query){
            //         $query->select('product_types.id');
            //     },'attributes'=>function($query){
            //         $query->select('attributes.id');
            //     },'attributeValues'=>function($query){
            //         $query->select('attribute_values.id','name','display_name','color_code');
            //     }
            // ]);


            $product->load([
                'translations',
                'category',
                'brand',
                'productType',
                'productAttributeValues.attributeValue.attribute',
                // 'variants',
                // 'gallery',
            ]);



            $customProduct = $product->toArray();
            $attributes = [];
            
            foreach ($product->productAttributeValues as $productAttributeValue) {
                $attributeValue = $productAttributeValue->attributeValue;
                $attribute = $attributeValue->attribute;
            
                if (!isset($attributes[$attribute->id])) {
                    $attributes[$attribute->id] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'translations' => $attribute->translations,
                        'values' => []
                    ];
                }
            
                $attributes[$attribute->id]['values'][] = [
                    'id' => $attributeValue->id,
                    'name' => $attributeValue->name,
                    'display_name' => $attributeValue->display_name,
                    'color_code' => $attributeValue->color_code,
                ];
            }
            
            $customProduct['attributes'] = array_values($attributes);
            unset($customProduct['product_attribute_values']);
            
            
            DB::commit();
            return $this->success($customProduct,'Created Successfully!',SUCCESS_STORE_CODE,'product');
        }catch (ValidationException $ex) {
            DB::rollBack();  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    public function update(ProductRequest $request,int $id):JsonResponse
    {
        try{
            DB::beginTransaction();

            $product = Product::find($id);

            if(!$product){
                return $this->error('Product Is Not Found',NOT_FOUND_ERROR_CODE);
            }
        
            /** Update thumb_image  */

            if($request->hasFile('thumb_image')){
                $old_image = $product->thumb_image;
                $image_name = $this->updateImage_Trait($request,'thumb_image',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME_THUMB_IMAGE,$old_image);
                $product->update(['thumb_image'=>$image_name]);
            }
           
           
            $product->update([
                "brand_id" =>(is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" =>(is_null($request->product_type_id)) ? $request->product_type_id :(int) $request->product_type_id,
                "qty" =>(int) $request->qty,
                "variant_quantity" =>(int) $request->qty ,//firstly take the same value of original quantity (qty) after when we add variant we be decrement depending on qty of variants.
                "price" => (float) $request->price ,
                "offer_price" => (float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" =>(int) $request->status,
            ]);
            
            if($request->has('category_id')){
                // sync() method doing dettach and after attach to data but if user send empty array it will delete all data
                $product->category()->sync($request->category_id);
            }
            

        
            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }

            $product->save() ;

            

            /* update product attributes values : **/

            $product->attributeValues()->detach();// delete the product attribute values
            // DB::table('product_attribute_values')->where('product_id', $product->id)->delete();// delete the product attribute values

            $this->storeProductAttributeValue($request->productAttributes,$product);

            //         $product->attributeValues()->updateExistingPivot($attributeValueId, [
            //             'attribute_id' => $request->attribute_id,
            //             'attribute_value_id' => $request->attribute_value_id,
            //         ]);


            // to get the relations : 
            $product->load([
                'translations',
                'category',
                'brand',
                'productType',
                'productAttributeValues.attributeValue.attribute',
                // 'variants',
                // 'gallery',
            ]);



            $customProduct = $product->toArray();
            $attributes = [];
            
            foreach ($product->productAttributeValues as $productAttributeValue) {
                $attributeValue = $productAttributeValue->attributeValue;
                $attribute = $attributeValue->attribute;
            
                if (!isset($attributes[$attribute->id])) {
                    $attributes[$attribute->id] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'translations' => $attribute->translations,
                        'values' => []
                    ];
                }
            
                $attributes[$attribute->id]['values'][] = [
                    'id' => $attributeValue->id,
                    'name' => $attributeValue->name,
                    'display_name' => $attributeValue->display_name,
                    'color_code' => $attributeValue->color_code,
                ];
            }
            
            $customProduct['attributes'] = array_values($attributes);
            unset($customProduct['product_attribute_values']);
            


            DB::commit();
            return $this->success($customProduct,'Updated Successfully!',SUCCESS_CODE,'product');
            
        }catch (ValidationException $ex) {
            DB::rollBack();  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    public function destroy(string $id):JsonResponse
    {
        try{ 
            $product = Product::find($id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            //you can use relation 
            // if(OrderProduct::where('product_id',$product->id)->count() > 0){
            // return $this->error('This Product Have Order(s) You Can'\t Delete it !',CONFLICT_ERROR_CODE);
            // }

            // Detach category 
            // $product->category()->detach(); 
            // DB::table('product_attribute_values')->where('product_id', $product->id)->delete();



            //********************   Delete Product the thumb image    ******************** */
            
            $this->deleteImage_Trait($product->thumb_image ,self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);            

            //********************   Delete Product Gallery    ******************** */


            #M2: 

            // if(isset($product->gallery)  && count($product->gallery)>0){
            //     foreach($product->gallery as $product_image){
            //         $this->deleteImage_Trait($product_image->image,self::FOLDER_PATH,'gallery');
            //         $product_image->delete();
            //     }
            // }

            //********************   Delete variants & items     ******************** */
            
  
            ##M2: 
            // if (isset($product->attributes) && count($product->attributes) > 0) {
            //     foreach ($product->attributes as $attribute) {
            //         if (isset($attribute->items) && count($attribute->items) > 0) {
            //             foreach ($attribute->items as $item) {
            //                 $item->delete();
            //             }
            //             $attribute->delete();
            //         } else {
            //             $attribute->delete();
            //         }
            //     }
            // }

            
            //********************   Delete Product  *****************//
               
            $product->delete();
            
            // we are using ajax : 
            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }


    public function storeProductAttributeValue($productAttributes,$product):void
    {
        foreach ($productAttributes as $attributeData) {

            $attribute_id = $attributeData['attribute_id'];
            
            foreach ($attributeData['values'] as $valueData) {
                $product->attributeValues()->attach(
                    $valueData['attribute_value_id'],
                    [
                        'attribute_id' => $attribute_id,
                    ]
                );
            }
        }
    }





    // public function storeProductVariants(Request $request , int $id){

    //     try {
    //         $product = Product::find($id);
    //         if (!$product) {
    //             return $this->error('Product Is Not Found',ERROR_CODE);
    //         }
    
    //         // return $product->price;
    //         // Creating a variant
    //         $variant = ProductVariant::create([
    //             'product_id' => $product->id,
    //             'extra_price' => 9.99,
    //             'final_price' => $product->price + 9.99,
    //             'quantity' => 10,
    //             'sku' => 'PROD-A-RED-8',
    //             'variant_hash' => ProductVariant::generateVariantHash([8, 1]), // attribute value IDs for red and S
    //         ]);

    //         if($request->hasFile('barcode')){
    //             $barcode_image_name = $this->uploadImage_Trait($request,'barcode',ProductNewController::FOLDER_PATH,ProductNewController::FOLDER_NAME_BARCODE);
    //             $variant->update(['barcode' => $barcode_image_name]);
    //         }

    //         // return $variant;
    //         // Attach attribute values
    //         // $variant->attributeValues()->attach([8, 1]);


    //         // foreach ($request->attributeValue_ids as $attributeValueId) {
    //         foreach ([8,1] as $attributeValueId) {
    //             $variant->attributeValues()->attach(
    //                 $attributeValueId,
    //                 [
    //                     'product_id' => $id,
    //                 ]
    //             );
    //         }


    //         $variant->load('productVariantAttributeValues');

    //         $customVariant = $variant->toArray();
    //         $attributes = [];
            
    //         foreach ($variant->productVariantAttributeValues as $productVariantAttributeValue) {
    //             $attributeValue = $productVariantAttributeValue->attributeValue;
    //             $attribute = $attributeValue->attribute;
            
    //             if (!isset($attributes[$attribute->id])) {
    //                 $attributes[$attribute->id] = [
    //                     'id' => $attribute->id,
    //                     'name' => $attribute->name,
    //                     'translations' => $attribute->translations,
    //                     'value' => [
    //                         'id' => $attributeValue->id,
    //                         'name' => $attributeValue->name,
    //                         'display_name' => $attributeValue->display_name,
    //                         'color_code' => $attributeValue->color_code,
    //                     ],
    //                 ];
    //             }
    //         }
            
    //         $customVariant['attributes'] = array_values($attributes);
    //         unset($customVariant['product_variant_attribute_values']);

            

    //         return $this->success($customVariant,'Variant Created Successfully!',SUCCESS_STORE_CODE,'productVariants');

    //     } catch (\Exception $e) {
    //         // Handle the exception, for example:
    //         return $this->error($e->getMessage(), ERROR_CODE);
    //     }

    //     // IDs for red and S
    // }

    // // public function getVariantPrice(Request $request)
    // public function getVariantPrice(Request $request,int $id)
    // {
    //     $attributeValueIds = $request->input('attribute_value_ids');
    //     $variantHash = ProductVariant::generateVariantHash([8,1]);
    //     // $variantHash = ProductVariant::generateVariantHash($attributeValueIds);
        
    //     // $variant = ProductVariant::where('product_id', $request->product_id)
    //     $variant = ProductVariant::where('product_id', $id)
    //         ->where('variant_hash', $variantHash)
    //         ->select('final_price', 'in_stock', 'quantity')
    //         ->first();
            

    //     return $this->success($variant,'Get Variant Price',SUCCESS_CODE,'getVariantPrice');
    //     // return response()->json([
    //     //     'price' => $variant->final_price,
    //     //     'in_stock' => $variant->in_stock,
    //     //     'quantity' => $variant->quantity
    //     // ]);
    // }



}
