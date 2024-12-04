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
    private const ITEMS_PER_PAGE = 20;

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
        try {
            $products = $this->getProductsWithRelations();
            $transformedProducts = $this->transformProducts($products);

            return $this->paginationResponse($products,'products','All Products',SUCCESS_CODE,$transformedProducts);
            
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    private function getProductsWithRelations()
    {
        return Product::with([
            'translations',
            'category',
            'brand',
            'productType',
            'productAttributeValues.attributeValue.attribute',
            'productVariantAttributeValues.variant',
            'productVariantAttributeValues.attributeValue.productVariants',
            'gallery',
        ])
        ->orderBy('id', 'ASC')
        ->paginate(self::ITEMS_PER_PAGE);
    }

    private function transformProducts($products)
    {
        return $products->getCollection()
            ->map(function ($product) {
                $productArray = $product->toArray();
                
                $this->transformGallery($productArray);
                $this->transformAttributes($productArray, $product);
                $this->transformVariants($productArray, $product);
                
                $this->cleanupProductArray($productArray);
                
                return $productArray;
            })
            ->all();
    }

    private function transformGallery(&$productArray): void
    {
        if (isset($productArray['gallery'])) {
            $productArray['gallery'] = collect($productArray['gallery'])
                ->map(function ($galleryItem) {
                    return [
                        'id' => $galleryItem['id'],
                        'image' => $galleryItem['image']
                    ];
                })
                ->values()
                ->all();
        }
    }

    private function transformAttributes(&$productArray, $product): void
    {
        $attributes = [];
        
        foreach ($product->productAttributeValues as $attributeValue) {
            $attribute = $attributeValue->attributeValue->attribute;
            $attributeId = $attribute->id;
            
            if (!isset($attributes[$attributeId])) {
                $attributes[$attributeId] = $this->createAttributeArray($attribute);
            }
            
            $attributes[$attributeId]['values'][] = $this->createAttributeValueArray($attributeValue->attributeValue);
        }
        
        $productArray['attributes'] = array_values($attributes);
    }

    private function createAttributeArray($attribute): array
    {
        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'translations' => $attribute->translations,
            'values' => []
        ];
    }

    private function createAttributeValueArray($attributeValue): array
    {
        return [
            'id' => $attributeValue->id,
            'name' => $attributeValue->name,
            'display_name' => $attributeValue->display_name,
            'color_code' => $attributeValue->color_code,
        ];
    }

    private function transformVariants(&$productArray, $product): void
    {
        $variants = [];
        
        foreach ($product->productVariantAttributeValues as $variantAttributeValue) {
            $variant = $variantAttributeValue->variant;
            $attribute = $variantAttributeValue->attributeValue->attribute;
            $variantId = $variant->id;
            
            if (!isset($variants[$variantId])) {
                $variants[$variantId] = $this->createVariantArray($variant);
            }
            
            $variants[$variantId]['attributes'][] = $this->createVariantAttributeArray(
                $attribute,
                $variantAttributeValue->attributeValue
            );
        }
        
        $productArray['variants'] = array_values($variants);
    }

    private function createVariantArray($variant): array
    {
        return [
            'id' => $variant->id,
            'extra_price' => $variant->extra_price,
            'final_price' => $variant->final_price,
            'quantity' => $variant->quantity,
            'sku' => $variant->sku,
            'barcode' => $variant->barcode,
            'attributes' => []
        ];
    }

    private function createVariantAttributeArray($attribute, $attributeValue): array
    {
        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'translations' => $attribute->translations,
            'value' => [
                'id' => $attributeValue->id,
                'name' => $attributeValue->name,
                'display_name' => $attributeValue->display_name,
                'color_code' => $attributeValue->color_code,
            ]
        ];
    }

    private function cleanupProductArray(&$productArray): void
    {
        unset($productArray['product_attribute_values']);
        unset($productArray['product_variant_attribute_values']);
    }



    public function show(int $id){
        try{
       
            $product = Product::find($id);

            if(!$product){
                return $this->error('Product Is Not Found',NOT_FOUND_ERROR_CODE);
            }

            // to get the relations : 
            $product->load([
                'translations',
                'category',
                'brand',
                'productType',
                'productAttributeValues.attributeValue.attribute',           
                'variants.productVariantAttributeValues.attributeValue.attribute',
                'gallery',
            ]);

            $customProduct = $product->toArray();

            // Transform product attributes
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

            // Transform variants
            $variants = [];
            foreach ($product->productVariantAttributeValues as $variantAttributeValue) {
                $variant = $variantAttributeValue->variant;
                $attribute = $variantAttributeValue->attributeValue->attribute;
                $variantId = $variant->id;
                
                if (!isset($variants[$variantId])) {
                    $variants[$variantId] = [
                        'id' => $variant->id,
                        'extra_price' => (float)$variant->extra_price,
                        'final_price' => (float)($variant->extra_price + $product->price),
                        'quantity' => (int)$variant->quantity,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'attributes' => []
                    ];
                }
                
                if (!collect($variants[$variantId]['attributes'])->contains('id', $attribute->id)) {
                    $variants[$variantId]['attributes'][] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'translations' => $attribute->translations,
                        'value' => [
                            'id' => $variantAttributeValue->attributeValue->id,
                            'name' => $variantAttributeValue->attributeValue->name,
                            'display_name' => $variantAttributeValue->attributeValue->display_name,
                            'color_code' => $variantAttributeValue->attributeValue->color_code,
                        ]
                    ];
                }
            }

            $customProduct['variants'] = array_values($variants);
            // Transform gallery
            if (isset($customProduct['gallery'])) {
                $customProduct['gallery'] = collect($customProduct['gallery'])
                    ->map(function ($galleryItem) {
                        return [
                            'id' => $galleryItem['id'],
                            'image' => $galleryItem['image']
                        ];
                    })
                    ->values()
                    ->all();
            }
            return $this->success($customProduct,'Get Product Successfully!',SUCCESS_CODE,'product');
            
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
                'variants',
                'gallery',
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
                'variants.productVariantAttributeValues.attributeValue.attribute',
                'gallery',
            ]);

            $customProduct = $product->toArray();
            
            // Transform product attributes
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

            // Transform variants
            $variants = [];
            foreach ($product->productVariantAttributeValues as $variantAttributeValue) {
                $variant = $variantAttributeValue->variant;
                $attribute = $variantAttributeValue->attributeValue->attribute;
                $variantId = $variant->id;
                
                if (!isset($variants[$variantId])) {
                    $variants[$variantId] = [
                        'id' => $variant->id,
                        'extra_price' => (float)$variant->extra_price,
                        'final_price' => (float)($variant->extra_price + $product->price),
                        'quantity' => (int)$variant->quantity,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'attributes' => []
                    ];
                }
                
                if (!collect($variants[$variantId]['attributes'])->contains('id', $attribute->id)) {
                    $variants[$variantId]['attributes'][] = [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'translations' => $attribute->translations,
                        'value' => [
                            'id' => $variantAttributeValue->attributeValue->id,
                            'name' => $variantAttributeValue->attributeValue->name,
                            'display_name' => $variantAttributeValue->attributeValue->display_name,
                            'color_code' => $variantAttributeValue->attributeValue->color_code,
                        ]
                    ];
                }
            }

            $customProduct['variants'] = array_values($variants);
            // Transform gallery
            if (isset($customProduct['gallery'])) {
                $customProduct['gallery'] = collect($customProduct['gallery'])
                    ->map(function ($galleryItem) {
                        return [
                            'id' => $galleryItem['id'],
                            'image' => $galleryItem['image']
                        ];
                    })
                    ->values()
                    ->all();
            }

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

            // Detach product attribute values 

            



            //********************   Delete Product the thumb image    ******************** */
            
            $this->deleteImage_Trait($product->thumb_image ,self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);            

            //********************   Delete Product Gallery    ******************** */


            #M2: 

            if(isset($product->gallery)  && count($product->gallery) > 0){
                foreach($product->gallery as $product_image){
                    $this->deleteImage_Trait($product_image->image,self::FOLDER_PATH,'gallery');
                    $product_image->delete();
                }
            }

            //********************   Delete attributes & values     ******************** */
            
            // DB::table('product_attribute_values')->where('product_id', $product->id)->delete();
            
            //********************   Delete variants    ******************** */
            

            
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
}
