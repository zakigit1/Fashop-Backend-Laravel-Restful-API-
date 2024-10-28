<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            $categories = Category::where('status',1)->orderBy('id','DESC')->paginate(20);
            return $this->success($categories,'All Categories',SUCCESS_CODE);

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        
        try{
            DB::beginTransaction();



            $category = New Category();
                $category->slug = $request->slug;
                $category->parent_id = $request->parent_id;
                $category->status = $request->status;
         
            // Store translations for each locale
            foreach (config('translatable.locales') as $locale) {
                $category->translateOrNew($locale)->name = $request->input("name.$locale");
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
    public function show(string $slug)
    {
        try{
            $category = Category::where('slug',$slug)->first();
            if(!$category){
                return $this->error('Category Is Not Found!',ERROR_CODE);
            }
            dd($category);
            
            $category->load('translations');

            dd($category);
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

            $category->slug = $request->slug;
            $category->parent_id = $request->parent_id;
            $category->status = $request->status;
           
            // Update translations
            foreach (config('translatable.locales') as $locale) {
                if ($request->has("name.$locale")) {
                    $category->translateOrNew($locale)->name = $request->input("name.$locale");
                }
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

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    public function change_status(string $id){
        try{

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
