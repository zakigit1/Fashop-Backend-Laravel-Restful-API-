<?php

namespace App\Http\Controllers;


use App\Http\Requests\BrandRequest;

use App\Models\Brand;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;

class BrandController extends Controller
{
    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/';
    const FOLDER_NAME = 'brands';


    public function index(){
        try{

            $brands = Brand::where('status',1)->orderBy('id','DESC')->paginate(20);
            return $this->success($brands,'All Brands',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    public function show(string $id){
        try{
            $brand = Brand::find($id);
            if(!$brand){
                return $this->error('Brand Is Not Found!',ERROR_CODE);
            }
            
            // $brand->load('translations');

            // dd($brand);
            return $this->success($brand,'Brand Details',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    public function store(BrandRequest $request)
    {
        try{

            // return $request->all();

            DB::beginTransaction();
     
            /** Save logo  */

            $logo_name= $this->uploadImage_Trait($request,'logo',self::FOLDER_PATH,self::FOLDER_NAME);

            ## M1
            $brand = Brand::create([
                "logo" => $logo_name,
               "status" => $request->status,
            ]);

            // dd(config('translatable.locales.'.config('translatable.locale')));
            // $Languages = config('translatable.locales.'.config('translatable.locale'));

            /** Store translations for each locale */
            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $brand->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                
                /** if you use arabic lang this is not good because doen't give you slug in arabic */
                // $brand->translateOrNew($keyLang)->slug = Str::slug($request->input("name.$keyLang"), '-');

                /**use this one it is good */
                $brand->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
            }

            $brand->save() ;



            DB::commit();
            return $this->success($brand,'Created Successfully!',SUCCESS_CODE);


        }catch(\Exception $ex){
            DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }

    public function update(BrandRequest $request,string $id){
        try{
            // return $request->all();
            DB::beginTransaction();

            
            $brand = Brand::find($id);

            if(!$brand){
                return $this->error('Brand Is Not Found!',ERROR_CODE);
            }


            if($request->hasFile('logo')){
    
                $old_logo = $brand->logo;
                $logo_name = $this->updateImage_Trait($request,'logo',BrandController::FOLDER_PATH,BrandController::FOLDER_NAME,$old_logo);
                $brand->update(['logo'=>$logo_name]);
            }
            if($request->has('status')){
                $brand->update(["status" => $request->status]);
            }
            

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
               
                if($request->input("name.$keyLang") != null){

                    $brand->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                    $brand->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                }
            }
    
            $brand->save();

            DB::commit();
            return $this->success($brand,'Updated Successfully!',SUCCESS_CODE);

        }catch(\Exception $ex){
            DB::rollBack();
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    public function destroy(string $id){
        try{
            $brand = Brand::find($id);

            if(!$brand){
                return $this->error('Brand Is Not Found!',ERROR_CODE);
            }

            
            
            # Check if the brand have product(s): [without using relation]
            // if(Product::where('brand_id',$brand->id)->count() > 0){
                
            //     return response(['status'=>'error','message'=>"You Can't Delete This Brand Because They Have Products Communicated With It !"]);
            // }
            # Check if the brand have product(s): [using relation]
            // if(isset($brand->products)  && count($brand->products) > 0){

            //     return response(['status'=>'error','message'=>"You Can't Delete This Brand Because They Have Products Communicated With It !"]);
            // }

            
            $this->deleteImage_Trait($brand->logo);

            $brand->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_CODE);

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    // public function change_status(string $id){
    //     try{

    //     }catch(\Exception $ex){
    //         return $this->error($ex->getMessage(),ERROR_CODE);
    //     }
    // }

    
}
