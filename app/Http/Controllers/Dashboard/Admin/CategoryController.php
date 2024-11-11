<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\imageUploadTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{

    use imageUploadTrait;

    const FOLDER_PATH = '/uploads/images/';
    const FOLDER_NAME = 'categories';



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $categories = Category::with([
                'children',
                '_parent'
                ])
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($categories,'categories','All Categories',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    { 
        try{
            $category = Category::with(['translations' => function($query){
                    $query->where('locale',config('translatable.locale'));// this is work 100%
            },'children'=>function($query){
                    $query->with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                    }]);
            },'_parent'=>function($query){
                $query->with(['translations' => function($query){
                    $query->where('locale',config('translatable.locale'));// this is work 100%
                }]);
            },
            ])
            ->find($id);
            if(!$category){
                return $this->error('Category Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            // $category->load('translations');// if you want to get all the tranlation
            // dd($category);
            
            return $this->success($category,'Category Details',SUCCESS_CODE,'category');
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        // dd($request->all());
        
        try{

            DB::beginTransaction();

            /** Save logo  */
            $icon_name= $this->uploadImage_Trait($request,'icon',self::FOLDER_PATH,self::FOLDER_NAME);

            $category = Category::create([
                "icon" => $icon_name,
                "parent_id" => (is_null($request->parent_id)) ? $request->parent_id :(int) $request->parent_id,
                "status" =>(int) $request->status,
            ]);



            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $category->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                
                /** if you use arabic lang this is not good because doen't give you slug in arabic */
                // $brand->translateOrNew($keyLang)->slug = Str::slug($request->input("name.$keyLang"), '-');

                /**use this one it is good */
                $category->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
            }


            $category->save();

            DB::commit();
            return $this->success($category,'Created Successfully!',SUCCESS_STORE_CODE,'category');

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
    public function update(CategoryRequest $request, string $id)
    {
        try{
            DB::beginTransaction();

            $category = Category::find($id);

            if(!$category){
                return $this->error('Category Is Not Found!',NOT_FOUND_ERROR_CODE);
            }


            if($request->hasFile('icon')){
    
                $old_icon = $category->icon;
                $icon_name = $this->updateImage_Trait($request,'icon',CategoryController::FOLDER_PATH,CategoryController::FOLDER_NAME,$old_icon);
                $category->update(['icon'=>$icon_name]);
            }

            $category->update([
                "parent_id" =>(is_null($request->parent_id)) ? $request->parent_id :(int) $request->parent_id,
                "status" =>(int) $request->status,
            ]);
           
            // Update translations

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $category->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $category->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                
            }

            $category->save();

            DB::commit();
            return $this->success( $category,'Updated Successfully!',SUCCESS_CODE,'category');
            
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
    public function destroy(string $id){
        try{
            $category = Category::find($id);

            if(!$category){
                return $this->error('Category Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            # Check if the category have product(s): [using relation]
            if(isset($category->products)  && count($category->products) > 0){
                return $this->error('You Can\'t Delete This Category Because They Have Products Communicated With It !',CONFLICT_ERROR_CODE);
            }

            # Check if the category have subcategory(ies): [without using relation]
            if(isset($category->children)  && count($category->children) > 0){
                return $this->error('You Can\'t Delete This Category Because They Have Products Communicated With It !',CONFLICT_ERROR_CODE);
            }

            $this->deleteImage_Trait($category->icon ,self::FOLDER_PATH,self::FOLDER_NAME);
            $category->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

}