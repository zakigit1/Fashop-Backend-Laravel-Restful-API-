<?php

namespace App\Http\Controllers\Admin\Dashboard\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductAttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $attributes = AttributeValue::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));// this is work 100%
                        //  $query->where('locale',config('app.locale'));
                    },
                    'attribute'=>function($query){
                        $query->with(['translations' => function($query){
                            $query->where('locale',config('translatable.locale'));// this is work 100%

                        }]);
                    }])
                ->where('status',1)
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($attributes,'productAttributeValues','All Product Attribute',SUCCESS_CODE);
           
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
            $attribute_value = AttributeValue::with(['translations' => function($query){
                        $query->where('locale',config('translatable.locale'));
                    }])->find($id);

            if(!$attribute_value){
                return $this->error('Product Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            return $this->success($attribute_value,'Product Attribute Value Details',SUCCESS_CODE,'productAttributeValue');

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeValueRequest $request)
    {
        // dd($request->all());
        try{

            DB::beginTransaction();

            $attribute_value = AttributeValue::create([
                "attribute_id" =>(int) $request->attribute_id,
                "color_code" => $request->color_code,
                "status" =>(int) $request->status,
            ]);

            if($request->has('extra_price')){
                $attribute_value->update([
                    "extra_price" =>(float) $request->extra_price,
                ]);
            }
            if($request->has('quantity')){
                $attribute_value->update([
                   "quantity" =>(int) $request->quantity,
                ]);
            }
            if($request->has('is_default')){
                $attribute_value->update([
                    "is_default" =>(int) $request->is_default,
                ]);
            }
            if($request->has('sort_order')){
                $attribute_value->update([
                    "sort_order" =>(int) $request->sort_order,
                ]);
            }



            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $attribute_value->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $attribute_value->translateOrNew($keyLang)->display_name = $request->input("display_name.$keyLang");
            }

            $attribute_value->save();

            DB::commit();
            return $this->success($attribute_value,'Created Successfully!',SUCCESS_STORE_CODE,'productAttributeValue');

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
    public function update(AttributeValueRequest $request, string $id)
    {
        // dd($request->all());
        try{

            DB::beginTransaction();
            $attribute_value = AttributeValue::find($id);

            if(!$attribute_value){
                return $this->error('Product Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            $attribute_value->update([
                "attribute_id" =>(int) $request->attribute_id,
                "color_code" => $request->color_code,
                "status" =>(int) $request->status,
            ]);

            if($request->has('extra_price')){
                $attribute_value->update([
                    "extra_price" =>(float) $request->extra_price,
                ]);
            }
            if($request->has('quantity')){
                $attribute_value->update([
                   "quantity" =>(int) $request->quantity,
                ]);
            }
            if($request->has('is_default')){
                $attribute_value->update([
                    "is_default" =>(int) $request->is_default,
                ]);
            }
            if($request->has('sort_order')){
                $attribute_value->update([
                    "sort_order" =>(int) $request->sort_order,
                ]);
            }


            foreach (config('translatable.locales.'.config('translatable.locale')) as $keyLang => $lang) { // keyLang = en ,$lang = english
                $attribute_value->translateOrNew($keyLang)->name = $request->input("name.$keyLang");
                $attribute_value->translateOrNew($keyLang)->display_name = $request->input("display_name.$keyLang");
            }

            $attribute_value->save();

            DB::commit();
            return $this->success($attribute_value,'Updated Successfully!',SUCCESS_CODE,'productAttributeValue');

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
            $attribute_value = AttributeValue::find($id);

            if(!$attribute_value){
                return $this->error('Product Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            $attribute_value->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
