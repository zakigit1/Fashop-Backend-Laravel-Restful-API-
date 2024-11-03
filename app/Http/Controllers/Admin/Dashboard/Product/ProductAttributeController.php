<?php

namespace App\Http\Controllers\Admin\Dashboard\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\AttributeValue;
use App\Models\Attribute;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductAttributeController extends Controller
{
 /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $attributes = Attribute::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                        //  $query->where('locale',config('app.locale'));
                    },
                    'values',
                    ])
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($attributes,'productAttributes','All Product Attribute',SUCCESS_CODE);
           
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE); 
        }

       
    }


    public function show(string $id){
        try{
            $attribute = Attribute::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));
                    }])->find($id);

            if(!$attribute){
                return $this->error('Product Attribute Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            return $this->success($attribute,'Product Attribute Details',SUCCESS_CODE,'productAttribute');

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    // public function store(AttributeRequest $request)
    public function store(Request $request)
    {
        dd($request->all());
        try{

            // dd($request->all());
            DB::beginTransaction();

            $attribute = Attribute::create([
                "product_id"=>$request->product_id,
                "type" => $request->type,
                "is_required" => $request->is_required,
                "is_filterable" => $request->is_filterable,
                "sort_order" => $request->sort_order,
                "status" => $request->status,
            ]);



            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $attribute->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
            }

            $attribute->save();

            DB::commit();
            return $this->success($attribute,'Created Successfully!',SUCCESS_STORE_CODE,'productAttribute');

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
    // public function update(AttributeRequest $request, string $id)
    public function update(Request $request, string $id)
    {        
        dd($request->all());
        try{
            DB::beginTransaction();

            $attribute = Attribute::find($id);

            if(!$attribute){
                return $this->error('Product Attribute Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            $attribute->update([
                "product_id"=>$request->product_id,
                "type" => $request->type,
                "is_required" => $request->is_required,
                "is_filterable" => $request->is_filterable,
                "sort_order" => $request->sort_order,
                "status" => $request->status,
            ]);
           
            // Update translations

            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $attribute->translateOrNew($keyLang)->name = $request->input("name.$keyLang"); 
            }

            $attribute->save();

            DB::commit();
            return $this->success($attribute,'Updated Successfully!',SUCCESS_CODE,'productAttribute');
            
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
            $attribute = Attribute::find($id);

            if(!$attribute){
                return $this->error('Product Attribute Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            

            # Check if the attribute have product(s): [using relation]
            if(isset($attribute->values)  && count($attribute->values) > 0){
                return $this->error('You Can\'t Delete This Product Attribute Because They Have Product Attribute Values Communicated With It !',CONFLICT_ERROR_CODE);
            }


            $attribute->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}

