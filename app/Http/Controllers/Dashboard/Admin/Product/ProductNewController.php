<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductNewController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/products/';
    const FOLDER_NAME_THUMB_IMAGE = 'thumb-images';
    const FOLDER_NAME_BARCODE = 'barcodes';


    public function index(){
        try{
            // Get Attributes:
            // $attributes = Attribute::with('translations','values')
            //     ->orderBy('id','desc')
            //     ->get();


            $attributes = Attribute::with([
                'translations',
                'values'=>function($q){
                    $q->select('id','attribute_id','name','display_name','color_code');
                } 
            ])->select('id')
            ->orderBy('id','asc')
            ->get();

            


            // Get Attribute Values:
            // $attribute_values = AttributeValue::orderBy('id','desc')
            //     ->get();

            // $attribute_values = AttributeValue::orderBy('id','desc')
            //     ->get(['id','name','display_name','color_code']);


            // Get All Products:
            $products = Product::with(['translations','categories','brand','productType',
                'attributes',
                'attributeValues',
                // 'categories' => function($q){
                //     $q->select('categories.id');    
                // },
                // 'gallery'
                ])
                ->orderBy('id','DESC')
                ->paginate(20);

            

            $productsPagination = [
                'pagination'=> [
                    'currentPage' => $products->currentPage(),
                    'totalPage' => $products->total(),
                    'perPage' => $products->perPage(),
                    'lastPage' => $products->lastPage(),
                    'hasNext' => $products->hasMorePages(),
                    'hasPrevious' => $products->currentPage() > 1,
                ],
                "products" => $products->items(),
            ];



            return $this->success([
                'attributes' =>$attributes,
                // 'attribute_values' => $attribute_values,
                'products' => $productsPagination
            ],'You get everything you need (attributes , products) successfully!',SUCCESS_CODE);
            
        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }

    }

    /**
     * Store a newly created resource in storage.
     */
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

        
            $product->categories()->syncWithoutDetaching($request->category_id);
        
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
            //     'categories'=>function($query){
            //         $query->select('categories.id');
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
                'categories',
                'brand',
                'productType',
                'attributes',
                'attributeValues'
            ]);

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


    public function update(ProductRequest $request,int $id){
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
                $product->categories()->sync($request->category_id);
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



            // $product->load([
            //     'categories',
            //     'brand',
            //     'productType',
            //     'attributes',
            //     'attributeValues'
            // ]);

            
            $product->load([
                'categories'=>function($query){
                    $query->select('categories.id');
                },'brand'=>function($query){
                    $query->select('brand.id');
                },'productType'=>function($query){
                    $query->select('product_types.id');
                },'attributes'=>function($query){
                    $query->select('attributes.id');
                },'attributeValues'=>function($query){
                    $query->select('attribute_values.id');
                }
            ]);


            DB::commit();
            return $this->success($product,'Updated Successfully!',SUCCESS_STORE_CODE,'product');
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

            // Detach categories 
            // $product->categories()->detach(); 
            // DB::table('product_attribute_values')->where('product_id', $product->id)->delete();



            //********************   Delete Product the thumb image    ******************** */
            
            $this->deleteImage_Trait($product->thumb_image ,self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);            

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
               
            $product->delete();
            


            // we are using ajax : 
            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }



    public function storeProductAttributeValue($productAttributes,$product){
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
