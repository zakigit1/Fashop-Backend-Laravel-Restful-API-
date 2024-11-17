<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Traits\imageUploadTrait;
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
            $products = Product::with('translations')
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
    // public function store(ProductRequest $request)
    public function store(Request $request)
    {
      return  $request->all();
        try{
            DB::beginTransaction();
    
            /** Save thumb_image  */

            $image_name= $this->uploadImage_Trait($request,'thumb_image',self::FOLDER_PATH,self::FOLDER_NAME_THUMB_IMAGE);


            $product = Product::create([
                "thumb_image" => $image_name,
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

            // if($request->hasFile('barcode')){
            //     $barcode_image_name = $this->uploadImage_Trait($request,'barcode',ProductController::FOLDER_PATH,ProductController::FOLDER_NAME_BARCODE);
            //     $product->update(['barcode' => $barcode_image_name]);
            // }


            $product->categories()->syncWithoutDetaching($request->category_id);
            // $product->categories()->attach($request->categories);

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $product->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                $product->translateOrNew($keyLang)->description = $request->input("description.$keyLang");
            }

            $product->save() ;


            // save product attributes values : 

            // Expected request format:
                    // attributes = [
                    //     {
                    //         attribute_id: 1, // size
                    //         values: [
                    //             {
                    //                 attribute_value_id: 1, // S
                    //                 extra_price: 0,
                    //                 quantity: 10,
                    //                 is_default: true
                    //             },
                    //             {
                    //                 attribute_value_id: 2, // M
                    //                 extra_price: 5,
                    //                 quantity: 15,
                    //                 is_default: false
                    //             }
                    //         ]
                    //     }
                    // ]




            foreach ($request->attributes as $attributeData) {
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



            $product->load('categories','brand','productType','attributeValues');

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


    public function storeProductAttributeValue(){

    }

}
