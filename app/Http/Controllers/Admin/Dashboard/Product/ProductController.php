<?php

namespace App\Http\Controllers\Admin\Dashboard\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Traits\imageUploadTrait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = 'uploads/images/products/';
    const FOLDER_NAME = 'thumb-images';


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $products = Product::with([
                    'categories',
                    // 'categories' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    'brand',
                    // 'brand' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    'gallery',
                    // 'attributes',
                    // 'attributes' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    // 'attribute_values',
                    // 'attribute_values' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // }
                ])    
                ->where('status',1)
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
                            //  $query->where('locale',config('app.locale'));
                        },
                    'categories',
                    // 'categories' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    'brand',
                    // 'brand' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    'gallery',
                    // 'attributes',
                    // 'attributes' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // },
                    // 'attribute_values',
                    // 'attribute_values' => function($query){
                    //     $query->with(['translations' => function($query){
                    //         $query->where('locale',config('translatable.locale'));// this is work 100%

                    //     },
                    //     ]);
                    // }
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
    public function store(ProductRequest $request):JsonResponse
    {
        dd($request->all());
        try{
            DB::beginTransaction();
    
            /** Save logo  */

            $image_name= $this->uploadImage_Trait($request,'thumb_image',self::FOLDER_PATH,self::FOLDER_NAME);

            $product = Product::create([
                "thumb_image" => $image_name,
                "brand_id" =>(is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" =>(int) $request->product_type_id,
                "qty" =>(int) $request->qty,
                "sku" => $request->sku,
                "price" =>(float) $request->price,
                "offer_price" =>(float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" =>(int) $request->status,
            ]);

            $product->categories()->syncWithoutDetaching($request->category_ids);
            $product->attributes()->syncWithoutDetaching($request->attribute_ids);
            
            // $product->categories()->attach($request->categories);

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }

            $product->save() ;

            $product->load('categories');

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
        dd($request->all());
        try{
            DB::beginTransaction();

            $product = Product::find($id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }


            if($request->hasFile('thumb_image')){
                $old_image = $product->thumb_image;
                $image_name = $this->updateImage_Trait($request,'thumb_image',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME,$old_image);
                $product->update(['thumb_image'=>$image_name]);
            }
            
            $product->update([
                "brand_id" =>(is_null($request->brand_id)) ? $request->brand_id :(int) $request->brand_id,
                "product_type_id" =>(int) $request->product_type_id,
                "qty" =>(int) $request->qty,
                "sku" => $request->sku,
                "price" =>(float) $request->price,
                "offer_price" =>(float) $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" =>(int) $request->status
            ]);

            $product->categories()->syncWithoutDetaching($request->category_ids);
            $product->attributes()->syncWithoutDetaching($request->attribute_ids);

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                /** if u use post man  */
                // if($request->input("name.$keyLang") != null){
                //     $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                //     $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                //     $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
                // }
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");

            }
    
            $product->save();

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
            
            $this->deleteImage_Trait($product->thumb_image ,self::FOLDER_PATH,self::FOLDER_NAME);

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
}
