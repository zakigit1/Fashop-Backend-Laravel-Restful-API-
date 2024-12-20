<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
// use App\Http\Requests\ProductAttributeValueRequest;
use App\Http\Requests\ProductRequest;
// use App\Models\Attribute;
// use App\Models\AttributeValue;
use App\Models\Product;
// use App\Models\ProductAttributeValue;
use App\Traits\imageUploadTrait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/products/';
    const FOLDER_NAME_THUMB_IMAGE = 'thumb-images';
    const FOLDER_NAME_BARCODE = 'barcodes';


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $products = Product::with([
                    'categories',
                    'brand',
                    'gallery',
                    'attributes',
                    'attributeValues',
                    'productType'
                ])    

                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($products,'products','All Products',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }
    }

    /**
     * Display the specified resource.
    */
    public function show(string $id):JsonResponse
    {
        try{
            $product = Product::with([
                    'translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        },
                    'categories' => function($query){
                        $query->with(['translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        },
                        ]);
                    },
       
                    'brand' => function($query){
                        $query->with(['translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        },
                        ]);
                    },
                    'gallery',
       
                    'attributes' => function($query){
                        $query->with(['translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        },
                        ]);
                    },
                    'attributeValues',
                    'productType' => function($query){
                        $query->with(['translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        },
                        ]);
                    },
                ])    
            ->find($id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            // $product->load('translations');

            // dd($product);
            return $this->success($product,'Product Details',SUCCESS_CODE,'product');

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
    //   return  $request->all();
        try{
            DB::beginTransaction();
    
            /** Save thumb_image  */

            $image_name= $this->uploadImage_Trait($request,'thumb_image',self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);
           


            // $price = $request->price;
            // $offer_price = (int) $request->offer_price;

            /** this use it if the price and offer price are integer */
            // if (is_int($price)) {
            //     $price = number_format($price, 2, '.', '');
            // }

            // if (is_int($offer_price)) {
            //     $offer_price = number_format($offer_price, 2, '.', '');
            // }


            // if (strpos($price, '.') === false) {
            //     // $price = number_format($price, 2, '.', '');
            //     $price = $price . '.00';
            // }
            // if (strpos($offer_price, '.') === false) {
            //     // $offer_price = number_format($offer_price, 2, '.', '');
            //     $offer_price = $offer_price . '.00';
            // }

            // return $price . " || " . $offer_price;
           

            $product = Product::create([
                "thumb_image" => $image_name,
                "brand_id" =>(is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" =>(is_null($request->product_type_id)) ? $request->product_type_id :(int) $request->product_type_id,
                "qty" =>(int) $request->qty,
                "sku" => $request->sku,
                "price" => (float) $request->price ,
                "offer_price" => (float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" =>(int) $request->status,
            ]);

            if($request->hasFile('barcode')){
                $barcode_image_name = $this->uploadImage_Trait($request,'barcode',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME_BARCODE);
                $product->update(['barcode' => $barcode_image_name]);
            }


            $product->categories()->syncWithoutDetaching($request->category_id);
            // $product->categories()->attach($request->categories);

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }

            $product->save() ;

            $product->load('categories','brand','productType');

            DB::commit();
            return $this->success($product,'Created Successfully!',SUCCESS_STORE_CODE,'product');
        }catch (ValidationException $ex) {
            DB::rollBack();  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id) :JsonResponse
    {
        // $request->all();
        try{
            DB::beginTransaction();

            $product = Product::find($id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }


            if($request->hasFile('thumb_image')){
                $old_image = $product->thumb_image;
                $image_name = $this->updateImage_Trait($request,'thumb_image',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME_THUMB_IMAGE,$old_image);
                $product->update(['thumb_image'=>$image_name]);
            }

            if($request->hasFile('barcode')){
                $old_barcode_image = $product->barcode;
                $barcode_image_name = $this->updateImage_Trait($request,'barcode',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME_BARCODE,$old_barcode_image);
                $product->update(['barcode' => $barcode_image_name]);
            }
            
            $product->update([
                "brand_id" =>(is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" =>(is_null($request->product_type_id)) ? $request->product_type_id :(int) $request->product_type_id,
                "qty" =>(int) $request->qty,
                "sku" => $request->sku,
                "price" =>(float) $request->price,
                "offer_price" =>(float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" =>(int) $request->status
            ]);

            if($request->has('category_id')){
                /**sync() method doing dettach and after attach to data */
                $product->categories()->sync($request->category_id);
            }


            // $product->categories()->syncWithoutDetaching( $request->category_ids);
           

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }
    
            $product->save();
            $product->load('categories','brand','productType');

            DB::commit();
            return $this->success($product,'Updated Successfully!',SUCCESS_CODE,'product');
        }catch (ValidationException $ex) {
            DB::rollBack();  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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


            //********************   Delete Product the thumb image    ******************** */
            
            $this->deleteImage_Trait($product->thumb_image ,self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);
            $this->deleteImage_Trait($product->barcode ,self::FOLDER_PATH,self::FOLDER_NAME_BARCODE);

            //********************   Delete Product Gallery    ******************** */


            #M2: 

            if(isset($product->gallery)  && count($product->gallery)>0){
                foreach($product->gallery as $product_image){
                    $this->deleteImage_Trait($product_image->image,self::FOLDER_PATH,'gallery');
                    $product_image->delete();
                }
            }

            //********************   Delete variants & items     ******************** */
            
  
            ##M2: 
            // if (isset($product->attributes) && count($product->attributes) > 0) {
            //     foreach ($product->variants as $variant) {
            //         if (isset($variant->items) && count($variant->items) > 0) {
            //             foreach ($variant->items as $item) {
            //                 $item->delete();
            //             }
            //             $variant->delete();
            //         } else {
            //             $variant->delete();
            //         }
            //     }
            // }

            
            //********************   Delete Product  *****************//
               
            // $product->delete();
            $product->forceDelete();// because we are using soft delete after add this feature .


            // we are using ajax : 
            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }


    // public function indexNew()
    // {
    //     $productsQuery = Product::with([
    //         'translations',
    //         'categories',
    //         'brand',
    //         'productType',
    //         'productAttributeValues.attributeValue.attribute'
    //     ])
    //     ->orderBy('id', 'ASC');


    //     $paginator = $productsQuery->paginate(20);

    //    return  $transformedProducts = $paginator->getCollection()
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
    //                     'values' => []
    //                 ];
    //             }

    //             $attributes[$attribute->id]['values'][] = [
    //                 'id' => $attributeValue->id,
    //                 'name' => $attributeValue->name
    //             ];
    //         }

    //         $productArray['attributes'] = array_values($attributes);
    //         unset($productArray['product_attribute_values']);

    //         return $productArray;
    //     })->all();

    //     $newPaginator = new LengthAwarePaginator(
    //         $transformedProducts,
    //         $paginator->total(),
    //         $paginator->perPage(),
    //         $paginator->currentPage(),
    //         ['path' => Paginator::resolveCurrentPath()]
    //     );

    //     return response()->json($newPaginator);
    // }



    // public function save_product_attribute_value(Request $request,string $id)
    // {
    //     // return $request->all();
    //     $request->validate([
    //         /** this validation if you want to save multiple attribute with multiple values for one product : */
    //             // 'product_id' => 'required|integer|exists:products,id|gt:0',

    //             // 'attributes' => 'required|array|min:1',
    //             // 'attributes.*.attribute_id' => 'required|integer|exists:attributes,id|gt:0',

    //             // 'attributes.*.values' => 'required|array',
    //             // 'attributes.*.values.*.attribute_value_id' => 'required|exists:attribute_values,id',

    //             // 'attributes.*.values.*.extra_price' => 'required|numeric|min:0',
    //             // 'attributes.*.values.*.quantity' => 'required|integer|min:0',
    //             // 'attributes.*.values.*.is_default' => 'required|boolean',

    //         /** this validation if you want to save single attribute with single values for one product : */

    //             // 'product_id' => 'required|integer|exists:products,id|gt:0',
    //             'attribute_id' => 'required|integer|exists:attributes,id|gt:0',
    //             'attribute_value_id' => 'required|integer|exists:attribute_values,id|gt:0|required_with:attribute_id',
    //             'extra_price' => 'required|numeric|min:0',//need modify to decimal value
    //             'quantity' => 'required|integer|min:0',
    //             'is_default' => 'required|boolean',
    //     ]);

    
    //     try{
    //         DB::beginTransaction();
            
    //         $product = Product::find($id);
            
    //         if(!$product){
    //             return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
    //         }
            
    //         $attributeValue = AttributeValue::where('id',$request->attribute_value_id)->first();

    //         if($attributeValue->attribute_id != $request->attribute_id){
    //             return $this->error('This Attribute is not matched with Value , Please Check Again !',NOT_FOUND_ERROR_CODE);
    //         }

    //         $product->attributeValues()->attach($request->attribute_value_id,[
    //             'attribute_id' => $request->attribute_id,
    //             'extra_price' => $request->extra_price,
    //             'quantity' => $request->quantity,
    //             'is_default' => $request->is_default,
    //         ]);



    //         //// if you want to delete all attribute values for this product :
    //         // $product->attributeValues()->detach();

    //         // // if you want to delete specific attribute values for this product :
    //         // $product->attributeValues()->detach($request->attribute_value_id);


    //         DB::commit();
    //         return $this->success('Created Successfully !',SUCCESS_CODE);
            
    //     }catch (ValidationException $ex) {
    //         DB::rollBack();  
    //         return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
    //     }catch(\Exception $ex){ 
    //         DB::rollBack();
    //         return $this->error($ex->getMessage(),ERROR_CODE);
    //     }


    // }

    // /**
    //  * Update product attribute value
    //  *
    //  * @param Request $request
    //  * @param string $id
    //  * @param int $attributeValueId
    //  * @return JsonResponse
    //  *
    //  * @throws ValidationException
    //  * @throws \Exception
    //  */
    // public function update_product_attribute_value(Request $request, string $id, int $attributeValueId)
    // {
    //     $request->validate([
    //         'attribute_id' => 'required|integer|exists:attributes,id|gt:0',
    //         'attribute_value_id' => 'required|integer|exists:attribute_values,id|gt:0',
    //         'extra_price' => 'required|numeric|min:0',
    //         'quantity' => 'required|integer|min:0',
    //         'is_default' => 'required|boolean',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $product = Product::find($id);

    //         if (!$product) {
    //             return $this->error('Product Is Not Found!', NOT_FOUND_ERROR_CODE);
    //         }

    //         $attributeValue = AttributeValue::find($attributeValueId);

    //         if (!$attributeValue) {
    //             return $this->error('Attribute Value Is Not Found!', NOT_FOUND_ERROR_CODE);
    //         }

            

    //         if($attributeValue->attribute_id != $request->attribute_id){
    //             return $this->error('This Attribute is not matched with Value , Please Check Again !',NOT_FOUND_ERROR_CODE);
    //         }

    //         $product->attributeValues()->updateExistingPivot($attributeValueId, [
    //             'attribute_id' => $request->attribute_id,
    //             'attribute_value_id' => $request->attribute_value_id,
    //             'extra_price' => $request->extra_price,
    //             'quantity' => $request->quantity,
    //             'is_default' => $request->is_default,
    //         ]);

    //         DB::commit();
    //         return $this->success('Product Attribute Value Updated Successfully !', SUCCESS_CODE);

    //     } catch (ValidationException $ex) {
    //         DB::rollBack();
    //         return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
    //     } catch (\Exception $ex) {
    //         DB::rollBack();
    //         return $this->error($ex->getMessage(), ERROR_CODE);
    //     }
    // }
    
}
