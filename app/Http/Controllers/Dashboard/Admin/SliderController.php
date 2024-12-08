<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SliderController extends Controller
{

    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/';
    private const FOLDER_NAME = 'sliders';

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try{
            $sliders = Slider::orderBy('id', 'ASC')
            ->paginate(20);

                return $this->paginationResponse($sliders,'sliders','All Sliders',SUCCESS_CODE);
           
            }catch(\Exception $ex){ 
                return $this->error($ex->getMessage(),ERROR_CODE);
            }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderRequest $request): JsonResponse
    {
        // return $request->all();
        try{
            // DB::beginTransaction();
            
            if (Slider::where('image', $request->file('image')->getClientOriginalName())->exists()) {
                return $this->error('This image already exists.', VALIDATION_ERROR_CODE);
            }
        
            /** Save image  */
 
            $image_name= $this->uploadImage_Trait2($request,'image',self::FOLDER_PATH,self::FOLDER_NAME);
          

            /**  Get the maximum order value and increment it by 1 */
            $maxOrder = Slider::max('order') ?? 0;
            $newOrder = $maxOrder + 1;

        
             /**  Store the slider */
            $slider = Slider::create([
                "image" => $image_name,
                "order" => (int) $newOrder,
                "status" =>(int) $request->status,
                "button_link" => $request->button_link,
            ]);
            
            // if ($request->has('background_color')) {
            //     $slider->update([
            //         'background_color' => $request->background_color,
            //     ]);
            // }
            // if ($request->has('title_color')) {
            //     $slider->update([
            //         'title_color' => $request->title_color,
            //     ]);
            // }
            // if ($request->has('description_color')) {
            //     $slider->update([
            //         'description_color' => $request->description_color,
            //     ]);
            // }



            /** Store translations for each locale */
            // foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
            //     $slider->translateOrNew($keyLang)->title = $request->input("title.$keyLang");
            //     $slider->translateOrNew($keyLang)->description = str_replace(' ', '-', $request->input("description.$keyLang"));
            // }

            // $slider->save() ;

            // DB::commit();
            return $this->success($slider,'Created Successfully!',SUCCESS_STORE_CODE,'slider');

        }catch (ValidationException $ex) {
            // DB::rollBack();     
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            // DB::rollBack();  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(SliderRequest $request,string $id){
    //    return $request->all();
        try{
            DB::beginTransaction();

            $slider = Slider::find($id);

            if(!$slider){
                return $this->error('Slider Is Not Found!',NOT_FOUND_ERROR_CODE);
            }


            if($request->hasFile('image')){
                if (Slider::where('image', $request->file('image')->getClientOriginalName())->exists()) {
                    return $this->error('This image already exists.', VALIDATION_ERROR_CODE);
                }
    
                $old_image = $slider->image;
                $image_name = $this->updateImage_Trait2($request,'image',self::FOLDER_PATH,self::FOLDER_NAME,$old_image);
                $slider->update(['image'=>$image_name]);
            }

            $slider->update([
                "button_link" => $request->button_link,
                "order" => (int) $request->order,
                "status" =>(int) $request->status
            ]);

            // foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english

            //     $slider->translateOrNew($keyLang)->title = $request->input("title.$keyLang");
            //     $slider->translateOrNew($keyLang)->description = str_replace(' ', '-', $request->input(key: "description.$keyLang"));
            // }
    
            // $slider->save();

            DB::commit();
            return $this->success($slider,'Updated Successfully!',SUCCESS_CODE,'slider');

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
    public function destroy(string $id)
    {
        try{
            $slider = Slider::find($id);

            if(!$slider){
                return $this->error('Slider Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            $this->deleteImage_Trait($slider->image ,self::FOLDER_PATH,self::FOLDER_NAME);

            $slider->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
