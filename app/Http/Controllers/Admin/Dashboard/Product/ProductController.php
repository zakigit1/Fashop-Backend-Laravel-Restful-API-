<?php

namespace App\Http\Controllers\Admin\Dashboard\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($products,'products','All Products',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }
    }

    /**
     * Adding category(ies) for product.
    */

    public function addCategory(Request $request,string $id){
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request):JsonResponse
    {
        // dd($request->all());
        try{
            DB::beginTransaction();
    
            /** Save logo  */

            $image_name= $this->uploadImage_Trait($request,'thumb_image',self::FOLDER_PATH,self::FOLDER_NAME);

        
            $product = Product::create([
                "thumb_image" => $image_name,
                "brand_id" => $request->brand_id,
                "qty" => $request->qty,
                "sku" => $request->sku,
                "price" => $request->price,
                "offer_price" => $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" => $request->status,
            ]);

            $product->categories()->syncWithoutDetaching($request->categories);
            // // $product->categories()->attach($request->categories);

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
                $product->translateOrNew($keyLang)->product_type = $request->input("product_type.$keyLang");
            }

            $product->save() ;

            $product->load('categories');

            DB::commit();
            return $this->success($product,'Created Successfully!',SUCCESS_CODE);
            
// return $this->success([
            //     'product' => $product,
            //     'categories' => $product->categories],
            //     'Created Successfully!',
            //     SUCCESS_CODE);

        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    
    /**
     * Display the specified resource.
     */
    public function show(string $id):JsonResponse
    {
        try{
            $product = Product::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                        //  $query->where('locale',config('app.locale'));
                    }])->find($id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            // $product->load('translations');

            // dd($product);
            return $this->success($product,'Product Details',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id) :JsonResponse
    {
        // dd($request->all());
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

            /**  if you use postman */
            // if($request->has('status')){
            //     $product->update(["status" => $request->status]);
            // }
            
            $product->update([
                "brand_id" => $request->brand_id,
                "qty" => $request->qty,
                "sku" => $request->sku,
                "price" => $request->price,
                "offer_price" => $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "video_link" => $request->video_link,
                "status" => $request->status
            ]);

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                /** if u use post man  */
                // if($request->input("name.$keyLang") != null){
                //     $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                //     $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                //     $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
                //     $product->translateOrNew($keyLang)->product_type = $request->input("product_type.$keyLang");
                // }
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
                $product->translateOrNew($keyLang)->product_type = $request->input("product_type.$keyLang");
            }
    
            $product->save();

            DB::commit();
            return $this->success($product,'Updated Successfully!',SUCCESS_CODE);

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
            //     return response(['status'=>'error','message'=>"This Product Have Order(s) You Can'\t Delete it!"]);
            // }


            //********************   Delete Product the thumb image    ******************** */
            
            $this->deleteImage_Trait($product->thumb_image);
            

            //********************   Delete Product Gallery    ******************** */

            #M1: 
            // $product_gallery =ProductImageGallery::where('product_id',$product->id)->get();
            
            // foreach($product_gallery as $product_image){
            //     $this->deleteImage_Trait($product_image->image);
            //     $product_image->delete();
            // }


            #M2: 
            // if(isset($product->gallery)  && count($product->gallery)>0){
            //     foreach($product->gallery as $product_image){
            //         $this->deleteImage_Trait($product_image->image);
            //         $product_image->delete();
            //     }
            // }
            //********************   Delete variants & items     ******************** */
            
            ##M1: 
            // $variants = ProductVariant::where('product_id',$product->id)->get();
        
            // foreach($variants as $variant){
            //     $variant->items()->delete();//if you use after calling a relation a method you need to add in the name of relation bracket like tahe RelationName() 
            //     $variant->delete();
            // }

            
            ##M2: 
            // if (isset($product->variants) && count($product->variants) > 0) {
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
            return $this->success(null,'Deleted Successfully!',SUCCESS_CODE);
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }
}
