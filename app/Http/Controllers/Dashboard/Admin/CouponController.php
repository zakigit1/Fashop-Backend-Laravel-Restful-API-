<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\CouponUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class CouponController extends Controller
{


    private const ITEMS_PER_PAGE_DEFAULT = 20;
    private $ITEMS_PER_PAGE ;

    public function __construct() {
        $this->ITEMS_PER_PAGE = Cache::get('coupon_per_page', self::ITEMS_PER_PAGE_DEFAULT);
    }


    /**
     * Display a listing of the resource.
    */

    public function index(Request $request): JsonResponse
    {
        try{

            $coupons = Coupon::select('id','name','code','quantity','discount_type','discount','status')
                ->orderBy('id','asc')
                ->paginate($this->ITEMS_PER_PAGE, ['*'], 'page', $request->query('page', 1));

            return $this->paginationResponse($coupons,'coupons','All Coupons',SUCCESS_CODE);

        }catch(\Exception $ex){    
            return $this->error($ex->getMessage(),ERROR_CODE); 
        }
    }


    public function show(string $id): JsonResponse
    {
        try {
            $coupon = $this->findCouponOrFail($id);

            return $this->success($coupon, 'Coupon Details', SUCCESS_CODE, 'coupon');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request): JsonResponse
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
            $coupon->total_used =(int) 0 ;
            $coupon->status = (int) $request->status ;
            $coupon->min_purchase_amount = ($request->has('min_purchase_amount')) ? (float) $request->min_purchase_amount : (float) 0.00;
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
    public function update(CouponRequest $request, string $id): JsonResponse
    {
        // return $request->all();

        try{

            $coupon = $this->findCouponOrFail($id);

            $totalUsed = $coupon->total_used;

            $coupon->name = $request->name ;
            $coupon->code = $request->code ;
            $coupon->quantity = (int) $request->quantity ;
            $coupon->max_use = (int) $request->max_use ;
            $coupon->start_date = $request->start_date ;
            $coupon->end_date = $request->end_date ;
            $coupon->discount_type = $request->discount_type ;
            $coupon->discount = (float) $request->discount ;
            $coupon->min_purchase_amount = (float) $request->min_purchase_amount ;
            $coupon->status = (int) $request->status ;
            $coupon->total_used =(int) $totalUsed ;
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
    public function destroy(string $id): JsonResponse
    {
        try{ 

            $coupon = $this->findCouponOrFail($id);

            $coupon->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }

    /**
     * Get Coupon Users :
     */
    // public function get_coupon_users(string $id){

    //     try{
    //         $coupon = $this->findCouponOrFail($id);

    //         // $couponUser_max_use = CouponUser::where('coupon_id', $id)->get(['max_use']);
    //         $coupon_users = $coupon->users()
    //             ->select('name','email','image')
    //             ->addSelect(['available_use' => CouponUser::selectRaw('available_use')
    //             ->whereColumn('user_id','users.id')->where('coupon_id',$coupon->id)])
    //             ->get();

    //         return $this->success($coupon_users,'Coupon Users',SUCCESS_CODE,'couponUsers');

    //     }catch(\Exception $ex){
    //         return $this->error($ex->getMessage(),ERROR_CODE);
    //     }
    // }



    /**
     * Get Coupon Users (custom method):
     */
    public function get_coupon_users(string $id): JsonResponse
    {
        try {
            $coupon = $this->findCouponOrFail($id);
    
            $coupon_users = $coupon->users()
                ->select('users.name', 'users.email', 'users.image')
                ->addSelect(['available_use' => CouponUser::selectRaw('available_use')
                    ->whereColumn('user_id', 'users.id')
                    ->where('coupon_id', $coupon->id)
                    ->limit(1) // Ensure we get a single value
                ])
                ->get()
                ->map(function ($user) use ($coupon) {
                    // Calculate coupon_used as max_use - available_use
                    $user->coupon_used = $coupon->max_use - ($user->available_use ?? 0);
                    return $user;
                });
    
            return $this->success($coupon_users, 'Coupon Users', SUCCESS_CODE, 'couponUsers');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }

    public function changeStatus(Request $request): JsonResponse
    {
        try{

            $request->validate([
                'id' => 'integer|exists:coupons,id',
                'status' => 'required|boolean',
            ],[
                'id.exists' => 'Coupon Is Not Found!',
            ]);
    
    
            $coupon = $this->findCouponOrFail($request->id);
    
            $coupon->status = $request->status == 1 ? 1 : 0;
    
            $coupon->save();

            return $this->success(null,'Status Updated Successfully!',SUCCESS_CODE,'coupon');

        }catch (ValidationException $ex) {
    
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
       
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    public function dataPerPage(Request $request): JsonResponse
    {
        try{

            // return  $this->ITEMS_PER_PAGE;
            // return  Cache::get('coupon_per_page', self::ITEMS_PER_PAGE_DEFAULT);

            $request->validate([
                'coupon_per_page' => 'required|integer|max:100|gt:0'
            ]);

            if($request->coupon_per_page != $this->ITEMS_PER_PAGE){
                
                Cache::forget('coupon_per_page');

                /** Store the coupon per page in cache */
                // M1 : 
                Cache::put('coupon_per_page', $request->coupon_per_page);

                // M2 :
                // Cache::rememberForever('coupon_per_page', function () use ($request) {
                //     return $request->coupon_per_page;
                // });
            }

            return $this->success(null,'Coupons Per Page Has Been Updated Successfully!',SUCCESS_CODE);

        } catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }

    }


    private function findCouponOrFail(string $id): Coupon
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            throw new \Exception('Coupon Is Not Found!', NOT_FOUND_ERROR_CODE);
        }

        return $coupon;
    }

}
