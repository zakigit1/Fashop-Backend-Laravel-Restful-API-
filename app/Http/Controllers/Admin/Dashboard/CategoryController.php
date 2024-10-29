<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $categories = Category::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                        //  $query->where('locale',config('app.locale'));
                    }])
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($categories,'categories','All Categories',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    // public function store(Request $request)
    {
        
        try{

            // dd($request->all());
            DB::beginTransaction();

            $category = Category::create([
                "icon" => $request->icon,
                "parent_id" => $request->parent_id,
                "status" => $request->status,
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
            return $this->success($category,'Created Successfully!',SUCCESS_CODE);

        }catch(\Exception $ex){
            DB::rollBack();
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
                        //  $query->where('locale',config('app.locale'));
                    }])->find($id);


            if(!$category){
                return $this->error('Category Is Not Found!',ERROR_CODE);
            }

            
            // $category->load('translations');// if you want to get all the tranlation

            // dd($category);
            return $this->success($category,'Category Details',SUCCESS_CODE);

        }catch(\Exception $ex){ 
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
                return $this->error('Category Is Not Found!',ERROR_CODE);
            }

            $category->update([
                "icon" => $request->icon,
                "parent_id" => $request->parent_id,
                "status" => $request->status,
            ]);
           
            // Update translations

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $category->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $category->translateOrNew($keyLang)->slug = str_replace(' ', '-', $request->input("name.$keyLang"));
                
            }

            $category->save();

            DB::commit();
            return $this->success($category,'Updated Successfully!',SUCCESS_CODE);
            
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
                return $this->error('Category Is Not Found!',ERROR_CODE);
            }

            
            # Check if the category have product(s): [without using relation]
            // if(Product::where('category_id',$category->id)->count() > 0){
                
            //     return response(['status'=>'error','message'=>"You Can't Delete This Category Because They Have Products Communicated With It !"]);
            // }
            # Check if the category have product(s): [using relation]
            // if(isset($category->products)  && count($category->products) > 0){

            //     return response(['status'=>'error','message'=>"You Can't Delete This Category Because They Have Products Communicated With It !"]);
            // }

            # Check if the category have subcategory(ies): [without using relation]
            //if(isset($category->children)  && count($category->children) > 0){
            //if($category->parent_id != null){
                  //     return response(['status'=>'error','message'=>"You Can't Delete This Category Because They Have Products Communicated With It !"]);
            // }

        
            $category->delete();

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
