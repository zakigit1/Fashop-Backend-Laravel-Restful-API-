<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTypeRequest;
use Illuminate\Http\Request;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $product_types = ProductType::with([
                    'products'
                    ])
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($product_types,'productTypes','All Product Types',SUCCESS_CODE);
           
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
            $product_type = ProductType::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));
                    }])
                ->find($id);

            if(!$product_type){
                return $this->error('Product Type Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            return $this->success($product_type,'Product Type Details',SUCCESS_CODE,'productType');

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductTypeRequest $request)
    {
        // dd($request->all());
        try{

            DB::beginTransaction();

            $product_type = ProductType::create([
                "status" =>(int) $request->status,
            ]);


            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $product_type->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
            }

            $product_type->save();

            DB::commit();
            return $this->success($product_type,'Created Successfully!',SUCCESS_STORE_CODE,'productType');

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
    public function update(ProductTypeRequest $request, string $id)
    {
        // dd($request->all());
        try{
        DB::beginTransaction();

        $product_type = ProductType::find($id);

        if(!$product_type){
            return $this->error('Product Type Is Not Found!',NOT_FOUND_ERROR_CODE);
        }

        $product_type->update([
            "status" =>(int) $request->status,
        ]);



        // Update translations

        foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
            $product_type->translateOrNew($keyLang)->name = $request->input("name.$keyLang"); 
        }

        $product_type->save();

        DB::commit();
        return $this->success($product_type,'Updated Successfully!',SUCCESS_CODE,'productType');
        
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
            $product_type = ProductType::find($id);

            if(!$product_type){
                return $this->error('Product Type Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            $product_type->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
