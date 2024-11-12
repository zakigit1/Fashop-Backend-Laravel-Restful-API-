<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $attributes = AttributeValue::with(['attribute'])
                ->orderBy('id','DESC')
                ->paginate(20);

            return $this->paginationResponse($attributes,'AttributeValues','All Attribute Value',SUCCESS_CODE);
           
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
            $attribute_value = AttributeValue::with(['attribute'])->find($id);

            if(!$attribute_value){
                return $this->error('Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            return $this->success($attribute_value,'Attribute Value Details',SUCCESS_CODE,'AttributeValue');

        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    public function store(AttributeValueRequest $request)
    {
        // return $request->all();
        try{

            DB::beginTransaction();

            $attribute_value = AttributeValue::create([
                "attribute_id" =>(int) $request->attribute_id,
                "name" => $request->name,
                "display_name" => $request->display_name,
                "color_code" => $request->color_code,
                "status" =>(int) $request->status,
            ]);

            if($request->has('sort_order')){
                $attribute_value->update([
                    "sort_order" =>(int) $request->sort_order,
                ]);
            }


            $attribute_value->save();

            $attribute_value->load('attribute');

            DB::commit();
            return $this->success($attribute_value,'Created Successfully!',SUCCESS_STORE_CODE,'AttributeValue');

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
                return $this->error('Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            $attribute_value->update([
                "attribute_id" =>(int) $request->attribute_id,
                "name" => $request->name,
                "display_name" => $request->display_name,
                "color_code" => $request->color_code,
                "status" =>(int) $request->status,
            ]);


            if($request->has('sort_order')){
                $attribute_value->update([
                    "sort_order" =>(int) $request->sort_order,
                ]);
            }

            $attribute_value->save();

            $attribute_value->load('attribute');

            DB::commit();
            return $this->success($attribute_value,'Updated Successfully!',SUCCESS_CODE,'AttributeValue');

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
                return $this->error('Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            $attribute_value->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
