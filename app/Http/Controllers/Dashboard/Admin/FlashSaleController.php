<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlashSaleAddProductRequest;
use App\Http\Requests\FlashSaleEndDateRequest;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{   

    public function index(){
        try{


            // Get all products:
            $products = Product::select('id')->with(['translations' => function ($query) {
                $query->select('product_id', 'name', 'locale');
            }])
                ->active()
                ->orderBy('id','ASC')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'translations' => $product->translations->map(function ($translation) {
                            return [
                                'name' => $translation->name,
                                'locale' => $translation->locale
                            ];
                        })
                    ];
                });

                // ida khrjlk error the currentPage
                // ->through(function ($product) {
                //     return [
                //         'id' => $product->id,
                //         'translations' => $product->translations->map(function ($translation) {
                //             return [
                //                 'name' => $translation->name,
                //                 'locale' => $translation->locale
                //             ];
                //         })
                //     ];
                // });








            // Get flash sale end date:
            $flash_end_date = FlashSale::first();


            // Get flash sale items:
            $flashSaleItems = FlashSaleItem::with([
                'products',
                'flashSale'
                ])
                // ->where('status',1)
                ->orderBy('id','asc')
                ->paginate(20);


            $flashSaleItemsPagination = [
                'pagination'=> [
                    'currentPage' => $flashSaleItems->currentPage(),
                    'totalPage' => $flashSaleItems->total(),
                    'perPage' => $flashSaleItems->perPage(),
                    'lastPage' => $flashSaleItems->lastPage(),
                    'hasNext' => $flashSaleItems->hasMorePages(),
                    'hasPrevious' => $flashSaleItems->currentPage() > 1,
                ],
                "flashSaleItems" => $flashSaleItems->items(),
            ];



            return $this->success([
                'products' =>$products,
                'flashSaleEndDate' => $flash_end_date,
                'flashSaleItems' => $flashSaleItemsPagination
            ],'You get everything you need (products , flash sale end date , flash sale items) successfully!',SUCCESS_CODE);
            
        }catch(\Exception $ex){ 
            
            return $this->error($ex->getMessage(),ERROR_CODE);
          
        }

    }


    public function end_date(FlashSaleEndDateRequest $request){

        // return $request->all();
        try{
            $flashSaleEndDate = FlashSale::updateOrCreate(
                ['id'=>'1'],// ?  this is the condition of update Or create ( if the id =1 the data with update if the id doesn't equal 1 they will create new row with id 1 )
                ['end_date'=>$request->end_date]
            );

            
            $flashSaleItems = FlashSaleItem::where('flash_sale_id',null)->get();
            if(count($flashSaleItems) > 0){
                foreach($flashSaleItems as $item){
                    $item->update([
                        'flash_sale_id' => $flashSaleEndDate->id ,//Or 1
                    ]);
                }
            }

            return $this->success($flashSaleEndDate,'Updated Successfully!',SUCCESS_STORE_CODE,'flashSaleEndDate');

        }catch(\Exception $ex){
            
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }



    public function add_product(FlashSaleAddProductRequest $request){

        // return $request->all();

        try{

            $flash_end_date = FlashSale::first();
            // return empty($flash_end_date) ? 'yes':'no';
            $flashSaleItem = new FlashSaleItem();
            // $flashSaleItem->flash_sale_id = (is_null($flash_end_date) ? (int) 1 : (int) $flash_end_date->id) ;
            $flashSaleItem->flash_sale_id = optional($flash_end_date)->id;
            $flashSaleItem->product_id = $request->product_id;
            $flashSaleItem->show_at_home = $request->show_at_home;
            $flashSaleItem->status = $request->status;
            $flashSaleItem->save();

            return $this->success($flashSaleItem,'The product was successfully added to the flash sale',SUCCESS_STORE_CODE,'flashSaleItem');

        }catch(\Exception $ex){
            
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    public function destroy(string $id)
    {
        try{ 

            $flash_item = FlashSaleItem::find($id);

            if(!$flash_item){
                return $this->error('Flash Sale Item Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
   
            $flash_item->delete();

            return $this->success(null,'Deleted Successfully!',SUCCESS_DELETE_CODE);

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }



    // public function change_at_home_status(Request $request)
    // {
    //     $flash_item =FlashSaleItem::find($request->id);


    //     if(!$flash_item){
           
    //         // toastr()->error( 'Item is not found!');
    //         return to_route('admin.flash-sale.index');
    //         // return redirect()->back();
    //     }

    //     $product_name =$flash_item->product->name;

        
    //     $flash_item->show_at_home = $request->show_at_home_status == 'true' ? 1 : 0;
         
    //     $flash_item->save();

    //     $status =($flash_item->show_at_home == 1) ? 'show it' : 'don\'t show it ';

    //     return response(['status'=>'success','message'=>"The $product_name has been $status at the flash sale in the home page"]);

       
    // }


}
