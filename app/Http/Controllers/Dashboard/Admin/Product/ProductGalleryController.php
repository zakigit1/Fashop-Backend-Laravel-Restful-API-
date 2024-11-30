<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductGalleryRequest;
use App\Models\Product;
use App\Models\ProductImageGallery;
use App\Traits\imageUploadTrait;
use Illuminate\Http\Request;

class ProductGalleryController extends Controller
{
    use imageUploadTrait;


    const FOLDER_PATH = '/uploads/images/products/';
    const FOLDER_NAME = 'gallery';


    public function index(){
        // $productGallery = ProductImageGallery::with('product')->orderBy('id','desc')->get();
        // return $this->success($productGallery,'Product Gallery',SUCCESS_CODE);    


        try{

            $productGallery = ProductImageGallery::orderBy('id','ASC')
                ->paginate(20);

            return $this->paginationResponse($productGallery,'productGallery','Product Gallery',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(ProductGalleryRequest $request)
    {
        try {
            $product = Product::find($request->product_id);

            if (!$product) {
                return $this->error('Product not found', NOT_FOUND_ERROR_CODE);
            }


            $imagesNames=$this->upload_Multi_Image_Trait($request,'image',self::FOLDER_PATH,self::FOLDER_NAME);

            $productGallery = collect($imagesNames)->map(function ($imageName) use ($request) {
                return [
                    'product_id' => (int) $request->product_id,
                    'image' => $imageName,
                ];
            })->each(function ($item) {
                ProductImageGallery::create($item);
            });

            return $this->success($productGallery, 'Product images added successfully', SUCCESS_STORE_CODE, 'productGallery');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ERROR_CODE);
        }
    }

    // public function store(ProductGalleryRequest $request)
    // {
    //     // dd($request->all());
    //     try{
    //         // i add this for more secure
    //         $product = Product::find($request->product_id);

    //         if(!$product){
    //             return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
    //         }

    //         $imagesNames=$this->upload_Multi_Image_Trait($request,'image',self::FOLDER_PATH,self::FOLDER_NAME);
        


    //         $data = [];
    //         foreach ($imagesNames as $imageName) {
    //             $data[] = [
    //                 'product_id' => $request->product_id,
    //                 'image' => $imageName,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
            
    //         ProductImageGallery::insert($data);
            
    //         $productGallery = ProductImageGallery::latest('created_at')->take(count($data))->get();
            
    //         return $this->success($productGallery, 'Product Images have been added successfully!', SUCCESS_STORE_CODE, 'productGallery');

    //     }catch(\Exception $e){
    //         return $this->error($e->getMessage(),ERROR_CODE); 
    //     }


    // }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{ 

            $product_gallery = ProductImageGallery::find($id);

            if(!$product_gallery){
                return $this->error('Product Image Is Not Found!',NOT_FOUND_ERROR_CODE);
            }


            $this->deleteImage_Trait($product_gallery->image,self::FOLDER_PATH,self::FOLDER_NAME);

            $product_gallery->delete();

            // we are using ajax : 
            return $this->success(null,'Image Has Been Deleted Successfully!',SUCCESS_DELETE_CODE);    
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }

    public function destroyAllImages(string $id)
    {
        try {
            
            $product = Product::find($id);
            if(!$product){
                return $this->error('Product Image Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            if(isset($product->gallery)  && count($product->gallery)>0){
                foreach($product->gallery as $product_image){
                    $this->deleteImage_Trait($product_image->image,self::FOLDER_PATH,self::FOLDER_NAME);
                    $product_image->delete();
                }
            }

            // Return success response
           return $this->success(null,'Product Gallery Has Been Deleted Successfully!',SUCCESS_CODE);    
        } catch (\Exception $e) {
            // Return error response
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }
}
