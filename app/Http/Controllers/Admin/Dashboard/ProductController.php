<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\imageUploadTrait;
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

            $products = Product::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                        //  $query->where('locale',config('app.locale'));
                    }])
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($products,'products','All Products',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    public function store(ProductRequest $request)
    {
        try{

            dd($request->all());

            DB::beginTransaction();
     
            /** Save logo  */

            $image_name= $this->uploadImage_Trait($request,'logo',self::FOLDER_PATH,self::FOLDER_NAME);

        
            $product = Product::create([
                "thumb_image" => $image_name,
                // "category_id" => $request->category_id,
                "brand_id" => $request->brand_id,
                "qty" => $request->qty,
                "video_link" => $request->video_link,
                "sku" => $request->sku,
                "price" => $request->price,
                "offer_price" => $request->offer_price,
                "offer_start_date" => $request->offer_start_date,
                "offer_end_date" => $request->offer_end_date,
                "status" => $request->status,
            ]);

            // dd(config('translatable.locales.'.config('translatable.locale')));
            // $Languages = config('translatable.locales.'.config('translatable.locale'));

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
                $product->translateOrNew($keyLang)->product_type = $request->input("product_type.$keyLang");
            }

            $product->save() ;



            DB::commit();
            return $this->success($product,'Created Successfully!',SUCCESS_CODE);


        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
