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
        $productGallery = ProductImageGallery::with('product')->orderBy('id','desc')->get();
        return $this->success($productGallery,'Product Gallery',SUCCESS_CODE);    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductGalleryRequest $request)
    {
        // dd($request->all());
        try{
            // i add this for more secure
            $product = Product::find($request->product_id);

            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            $imagesNames=$this->upload_Multi_Image_Trait($request,'image',self::FOLDER_PATH,self::FOLDER_NAME);
        

            $data = [];
    
            foreach ($imagesNames as $imageName) {
                $data[] = [
                    'image' => $imageName,
                    'product_id' => $request->product_id
                ];
            }
            //  return print_r($data);
            $storeImages = ProductImageGallery::insert($data);
            
            return $this->success(null,'Product Images has been added successfully!',SUCCESS_STORE_CODE);    
    

        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
            // return $this->error("Product Images has not been added successfully!",ERROR_CODE);
            
        }


    }



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

    // public function destroyAllImages(string $id)
    // {
    //     try {
            
    //         $product = Product::find($id);
    //         if(!$product){
    //             return $this->error('Product Image Is Not Found!',NOT_FOUND_ERROR_CODE);
    //         }
    //         if(isset($product->gallery)  && count($product->gallery)>0){
    //             foreach($product->gallery as $product_image){
    //                 $this->deleteImage_Trait($product_image->image,self::FOLDER_PATH,self::FOLDER_NAME);
    //                 $product_image->delete();
    //             }
    //         }

    //         // Return success response
    //        return $this->success(null,'Product Images has been added successfully!',SUCCESS_CODE);    
    //     } catch (\Exception $e) {
    //         // Return error response
    //         return $this->error($e->getMessage(),ERROR_CODE);
    //     }
    // }
}
