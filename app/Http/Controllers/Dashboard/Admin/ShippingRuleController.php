<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingRuleRequest;
use App\Models\ShippingRule;
use Illuminate\Validation\ValidationException;

class ShippingRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $shippingRules = ShippingRule::with('translations')
                    ->orderBy('id','asc')
                    ->paginate(20);

            return $this->paginationResponse($shippingRules,'shippingRules','All Shipping Rules',SUCCESS_CODE);

        }catch(\Exception $ex){    
            return $this->error($ex->getMessage(),ERROR_CODE); 
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingRuleRequest $request)
    {
        return $request->all();
                        
        try{

            $shippingRule = new ShippingRule();

            $shippingRule->name = $request->name;
            $shippingRule->type = $request->type;
            $shippingRule->min_cost = (is_null($request->min_cost)) ? $request->min_cost :(float) $request->min_cost;
            $shippingRule->max_cost = (is_null($request->max_cost)) ? $request->max_cost :(float) $request->max_cost;
            $shippingRule->cost = (float) $request->cost ;
            $shippingRule->weight_limit = (is_null($request->weight_limit)) ? $request->weight_limit :(float) $request->weight_limit ;
            $shippingRule->description = $request->description ;
            $shippingRule->region = $request->region ;
            $shippingRule->carrier = $request->carrier ;
            $shippingRule->delivery_time = $request->delivery_time ;
            $shippingRule->status = (int) $request->status ;

            $shippingRule->save();

            return $this->success($shippingRule,'Created Successfully!',SUCCESS_STORE_CODE,'shippingRule');

        }catch (ValidationException $ex) {   
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ShippingRuleRequest $request, string $id)
    {
        return $request->all();

        try{

            $shippingRule = ShippingRule::find($id);

            if (!$shippingRule) {
                return $this->error('Shipping Rule Is Not Found!', NOT_FOUND_ERROR_CODE);
            }

    
            $shippingRule->name = $request->name;
            $shippingRule->type = $request->type;
            $shippingRule->min_cost = (is_null($request->min_cost)) ? $request->min_cost :(float) $request->min_cost;
            $shippingRule->max_cost = (is_null($request->max_cost)) ? $request->max_cost :(float) $request->max_cost;
            $shippingRule->cost = (float) $request->cost ;
            $shippingRule->weight_limit = (is_null($request->weight_limit)) ? $request->weight_limit :(float) $request->weight_limit ;
            $shippingRule->description = $request->description ;
            $shippingRule->region = $request->region ;
            $shippingRule->carrier = $request->carrier ;
            $shippingRule->delivery_time = $request->delivery_time ;
            $shippingRule->status = (int) $request->status ;

            $shippingRule->save();

            
            return $this->success( $shippingRule,'Updated Successfully!',SUCCESS_CODE,'shippingRule');
            
        }catch (ValidationException $ex) {  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{ 

            $shippingRule = ShippingRule::find($id);

            if (!$shippingRule) {
                return $this->error('Shipping Rule Is Not Found!', NOT_FOUND_ERROR_CODE);
            }

            //! check if the shipping rule is not use it in order

            // $shippingRule->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }
}
