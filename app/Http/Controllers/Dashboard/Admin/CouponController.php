<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $coupons = Coupon::orderBy('id','DESC')
                        ->paginate(20);

            return $this->paginationResponse($coupons,'coupons','All Coupons',SUCCESS_CODE);

        }catch(\Exception $ex){    
            return $this->error($ex->getMessage(),ERROR_CODE); 
        }
        
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request)
    {
        // return $request->all();

        try{

            $coupon = new Coupon();
            $coupon->name = $request->name ;
            $coupon->code = $request->code ;
            $coupon->quantity = (int) $request->quantity ;
            $coupon->max_use = (int) $request->max_use ;
            $coupon->start_date = $request->start_date ;
            $coupon->end_date = $request->end_date ;
            $coupon->discount_type = $request->discount_type ;
            $coupon->discount = (float) $request->discount ;
            $coupon->total_used = 0 ;
            $coupon->status = (int) $request->status ;
            $coupon->save();


            return $this->success($coupon,'Created Successfully!',SUCCESS_STORE_CODE,'coupon');

        }catch (ValidationException $ex) {   
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, string $id)
    {
        // return $request->all();

        try{

            $coupon = Coupon::find($id);

            if (!$coupon) {
                return $this->error('Coupon Is Not Found!', NOT_FOUND_ERROR_CODE);
            }

            $coupon->name = $request->name ;
            $coupon->code = $request->code ;
            $coupon->quantity = (int) $request->quantity ;
            $coupon->max_use = (int) $request->max_use ;
            $coupon->start_date = $request->start_date ;
            $coupon->end_date = $request->end_date ;
            $coupon->discount_type = $request->discount_type ;
            $coupon->discount = (float) $request->discount ;

            $coupon->status = (int) $request->status ;
            $coupon->save();

            return $this->success( $coupon,'Updated Successfully!',SUCCESS_CODE,'coupon');
            
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

            $coupon = Coupon::find($id);

            if (!$coupon) {
                return $this->error('Coupon Is Not Found!', NOT_FOUND_ERROR_CODE);
            }

            //! check if the coupon is not use it in order

            // $coupon->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }



}
