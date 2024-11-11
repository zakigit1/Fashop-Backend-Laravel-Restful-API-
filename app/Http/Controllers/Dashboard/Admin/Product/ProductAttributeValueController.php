<?php

namespace App\Http\Controllers\Dashboard\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttributeValueRequest;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Http\JsonResponse;
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

        $productAttributeValue = ProductAttributeValue::with(['product','attribute','attributeValue']) 
            ->orderBy('id','DESC')
            ->paginate(20);

        return $this->paginationResponse($productAttributeValue,'productAttributeValues','All Product Attribute Value',SUCCESS_CODE);

    }catch(\Exception $ex){ 
    
    return $this->error($ex->getMessage(),ERROR_CODE);
  
}
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(ProductAttributeValueRequest $request,string $productId)
    {
        
        return $request->all();
        $request->validate([
            'attribute_id' => 'required|integer|exists:attributes,id|gt:0',
            'attribute_value_id' => 'required|integer|exists:attribute_values,id|gt:0|required_with:attribute_id',
            'extra_price' => 'numeric|decimal:2',
            'quantity' => 'integer|min:0',
            'is_default' => 'boolean',
        ]);
        // return $request->all();
        
        try{
            DB::beginTransaction();
            
            $product = Product::find($id);
            
            if(!$product){
                return $this->error('Product Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
            
            $attributeValue = AttributeValue::where('id',$request->attribute_value_id)->first();

            if($attributeValue->attribute_id != $request->attribute_id){
                return $this->error('This Attribute is not matched with Value , Please Check Again !',NOT_FOUND_ERROR_CODE);
            }

            $productAttributeValue = ProductAttributeValue::create([
                'product_id' => (int) $id,
                'attribute_id' => (int) $request->attribute_id,
                'attribute_value_id' => (int) $request->attribute_value_id,
            ]);


            if($request->has('extra_price')){
                $productAttributeValue->update([
                    "extra_price" =>(float) $request->extra_price,
                ]);
            }
            if($request->has('quantity')){
                $productAttributeValue->update([
                    "quantity" =>(int) $request->quantity,
                ]);
            }
            if($request->has('is_default')){
                $productAttributeValue->update([
                    "is_default" =>(int) $request->is_default,
                ]);
            }

            DB::commit();

            // $productAttributeValue->load(['product','attribute','attributeValue']);
            return $this->success( $productAttributeValue,'Created Successfully !',SUCCESS_CODE,'ProductAttributeValue');

        }catch (ValidationException $ex) {
            DB::rollBack();  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductAttributeValueRequest $request, int $id,int $productId)
    {
        
        // return $request->all();
        try{
            DB::beginTransaction();
            $request->validate([
                'attribute_id' => 'required|integer|exists:attributes,id|gt:0',
                'attribute_value_id' => 'required|integer|exists:attribute_values,id|gt:0|required_with:attribute_id',
                'extra_price' => 'numeric|decimal:2',
                'quantity' => 'integer|min:0',
                'is_default' => 'boolean',
            ]);
            

            $product = Product::find($productId);

            if (!$product) {
                return $this->error('Product Is Not Found!', NOT_FOUND_ERROR_CODE);
            }
    
            $productAttributeValue = ProductAttributeValue::find($id);
    
            if (!$productAttributeValue) {
                return $this->error('Product Attribute Value this Not Found !', NOT_FOUND_ERROR_CODE);
            }

            if($productAttributeValue->product_id != $product->id){
                return $this->error('This Product doesn\'t have this value , Please Check Again !',NOT_FOUND_ERROR_CODE);
            }
    

            $attributeValue = AttributeValue::where('id',$request->attribute_value_id)->first();

            if($attributeValue->attribute_id != $request->attribute_id){
                return $this->error('This Attribute is not matched with Value , Please Check Again !',NOT_FOUND_ERROR_CODE);
            }


            $productAttributeValue->update([
                'attribute_id' => (int) $request->attribute_id,
                'attribute_value_id' => (int) $request->attribute_value_id,
            ]);
    
            if($request->has('extra_price')){
                $productAttributeValue->update([
                    "extra_price" =>(float) $request->extra_price,
                ]);
            }
            if($request->has('quantity')){
                $productAttributeValue->update([
                   "quantity" =>(int) $request->quantity,
                ]);
            }
            if($request->has('is_default')){
                $productAttributeValue->update([
                    "is_default" =>(int) $request->is_default,
                ]);
            }
    
            DB::commit();
            return $this->success( $productAttributeValue,'Product Attribute Value Updated Successfully !',SUCCESS_CODE,'ProductAttributeValue');
    
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
    public function destroy(int $id,int $productId)
    {
        try{ 

            
            $product = Product::find($productId);

            if (!$product) {
                return $this->error('Product Is Not Found!', NOT_FOUND_ERROR_CODE);
            }

            $productAttributeValue = ProductAttributeValue::find($id);

            if(!$productAttributeValue){
                return $this->error('Product Attribute Value Is Not Found!',NOT_FOUND_ERROR_CODE);
            }

            if($productAttributeValue->product_id != $product->id){
                return $this->error('This Product doesn\'t have this value , Please Check Again !',NOT_FOUND_ERROR_CODE);
            }
    
               
            $productAttributeValue->delete();


            // we are using ajax : 
            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $e){
            return $this->error($e->getMessage(),ERROR_CODE);
        }
    }
}
