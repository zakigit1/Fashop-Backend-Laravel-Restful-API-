<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlashSaleAddProductRequest;
use App\Http\Requests\FlashSaleEndDateRequest;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\FlashSaleProductView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FlashSaleController extends Controller
{   
    private const ITEMS_PER_PAGE = 20;

    public function getAvailableProducts(Request $request): JsonResponse
    {
        try {
            $products = FlashSaleProductView::orderBy('product_id', 'ASC')
                ->paginate(20, ['*'], 'product_page', $request->query('product_page', 1));

            return $this->paginationResponse(
                $products,
                'products',
                'Available products for flash sale retrieved successfully!',
                SUCCESS_CODE
            );
        } catch(\Exception $ex) {
            return $this->error($ex->getMessage(), ERROR_CODE);
        }
    }


    public function index(Request $request): JsonResponse
    {
        // dd($request->query());// request->query() you get it from url not body
        try{
            // Get all products with pagination:
            $products = FlashSaleProductView::orderBy('product_id', 'ASC')
                ->paginate(self::ITEMS_PER_PAGE, ['*'], 'product_page', $request->query('product_page', 1));
    

            $productsPagination = $this->paginationFunction($products,'products');

            // Get flash sale end date:
            $flash_end_date = FlashSale::first();
    
            // Get flash sale items:
            $flashSaleItems = FlashSaleItem::select('id','product_id','show_at_home','status')
            ->with([
                'product' => function($query) {
                    $query->select('id')->with(['translations' => function($q) {
                        $q->select('product_id', 'name', 'locale');
                    }]);
                },
            ])
            ->orderBy('id','asc')
            ->paginate(self::ITEMS_PER_PAGE, ['*'], 'flash_sale_page', $request->query('flash_sale_page', 1))
            ->through(function($item) {
                return [
                    'id' => $item->id,
                    'show_at_home' => $item->show_at_home,
                    'status' => $item->status,
                    'product' => [
                        'id' => $item->product->id,
                        'translations' => $item->product->translations->map(function($translation) {
                            return [
                                'name' => $translation->name,
                                'locale' => $translation->locale
                            ];
                            // return [
                            //     'product_'.$translation->locale => $translation->name,
                            // ];
                        })
                    ]
                ];
            });

            
            $flashSaleItemsPagination = $this->paginationFunction($flashSaleItems,'flashSaleItems');


            return $this->success([
                'flashSaleEndDate' => $flash_end_date,
                'productData' => $productsPagination,
                'flashSaleItemData' => $flashSaleItemsPagination
            ],'You get flash sale end date, products and flash sale items successfully!',SUCCESS_CODE,'flashsaleData');
            
        }catch(\Exception $ex){ 
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }



    // public function index2(): JsonResponse
    // {
    //     try{
    //         // Get all products:
    //         $products = Product::where('status',1)
    //             ->select('id')
    //             ->with(['translations' => function ($query) 
    //                 {
    //                     $query->select('product_id', 'name', 'locale');
    //                 }])
    //             ->active()
    //             ->orderBy('id','ASC')
    //             ->get()
    //             ->map(function ($product) {
    //                 return [
    //                     'id' => $product->id,
    //                     'translations' => $product->translations->map(function ($translation) {
    //                         return [
    //                             'name' => $translation->name,
    //                             'locale' => $translation->locale
    //                         ];
    //                     })
    //                 ];
    //             });

    //             // ida khrjlk error the currentPage
    //             // ->through(function ($product) {
    //             //     return [
    //             //         'id' => $product->id,
    //             //         'translations' => $product->translations->map(function ($translation) {
    //             //             return [
    //             //                 'name' => $translation->name,
    //             //                 'locale' => $translation->locale
    //             //             ];
    //             //         })
    //             //     ];
    //             // });


    //         // Get flash sale end date:
    //         $flash_end_date = FlashSale::first();


    //         // Get flash sale items:
    //         $flashSaleItems = FlashSaleItem::with([
    //             'products',
    //             'flashSale'
    //             ])
    //             // ->where('status',1)
    //             ->orderBy('id','asc')
    //             ->paginate(20);


    //         $flashSaleItemsPagination = [
    //             'pagination'=> [
    //                 'currentPage' => $flashSaleItems->currentPage(),
    //                 'totalPage' => $flashSaleItems->total(),
    //                 'perPage' => $flashSaleItems->perPage(),
    //                 'lastPage' => $flashSaleItems->lastPage(),
    //                 'hasNext' => $flashSaleItems->hasMorePages(),
    //                 'hasPrevious' => $flashSaleItems->currentPage() > 1,
    //             ],
    //             "flashSaleItems" => $flashSaleItems->items(),
    //         ];



    //         return $this->success([
    //             'products' =>$products,
    //             'flashSaleEndDate' => $flash_end_date,
    //             'flashSaleItems' => $flashSaleItemsPagination
    //         ],'You get everything you need (products , flash sale end date , flash sale items) successfully!',SUCCESS_CODE,'flashsaleData');
            
    //     }catch(\Exception $ex){ 
            
    //         return $this->error($ex->getMessage(),ERROR_CODE);
          
    //     }

    // }


    public function end_date(FlashSaleEndDateRequest $request): JsonResponse
    {
        // return $request->all();
        try{

            $checkFlashSale = FlashSale::first();  

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

            // return $this->success($flashSaleEndDate,'Updated Successfully!',SUCCESS_STORE_CODE,'flashSaleEndDate');

            if($checkFlashSale){
                return $this->success($flashSaleEndDate,'Flash Sale End Date Has Been Updated Successfully !',SUCCESS_CODE,'flashSaleEndDate');
            }else{
                return $this->success($flashSaleEndDate,'Flash Sale End Date Has Been Created Successfully !',SUCCESS_STORE_CODE,'flashSaleEndDate');
            }

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }
    }



    public function add_product(FlashSaleAddProductRequest $request)//: JsonResponse
    {
        // return $request->all();
        try{

            $flash_end_date = FlashSale::first();
            // return empty($flash_end_date) ? 'yes':'no';

            /** this operation to save just one product every storing data */
            // $flashSaleItem = new FlashSaleItem();
            // // $flashSaleItem->flash_sale_id = (is_null($flash_end_date) ? (int) 1 : (int) $flash_end_date->id) ;
            // $flashSaleItem->flash_sale_id = optional($flash_end_date)->id;
            // $flashSaleItem->product_id = $request->product_id;
            // $flashSaleItem->show_at_home = $request->show_at_home;
            // $flashSaleItem->status = $request->status;
            // $flashSaleItem->save();

            /** this operation to save just one products every storing data */
            $flashSaleItems = [];
            
            foreach ($request->products as $product_id) 
            {

                $flashSaleItem = new FlashSaleItem();
                $flashSaleItem->flash_sale_id = optional($flash_end_date)->id;
                $flashSaleItem->product_id = (int) $product_id;
                $flashSaleItem->show_at_home = (int) $request->show_at_home;
                $flashSaleItem->status = (int) $request->status;
                $flashSaleItem->save();
                
                $flashSaleItems[] = $flashSaleItem;
            }

            return $this->success($flashSaleItems,'The products were successfully added to the flash sale',SUCCESS_STORE_CODE,'flashSaleItems');

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



    public function changeStatus(Request $request)
    {

        try{

            $request->validate([
                'id' => 'integer|exists:flash_sale_items,id',
                'status' => 'required|boolean',
            ]);
    
    
            $flash_item = FlashSaleItem::find($request->id);
        
            if(!$flash_item){
                return $this->error('Flash Sale Item Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
    
    
            $flash_item->status = $request->status == 1 ? 1 : 0;
    
            $flash_item->save();

            return $this->success(null,'Status Updated Successfully!',SUCCESS_CODE,'flash_item');

        }catch (ValidationException $ex) {
    
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
       
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }


    public function changeShowAtHome(Request $request)
    {

        try{

            $request->validate([
                'id' => 'integer|exists:flash_sale_items,id',
                'show_at_home' => 'required|boolean',
            ]);
    
    
            $flash_item = FlashSaleItem::find($request->id);
        
            if(!$flash_item){
                return $this->error('Flash Sale Item Is Not Found!',NOT_FOUND_ERROR_CODE);
            }
    
    
            $flash_item->show_at_home = $request->show_at_home == 1 ? 1 : 0;
    
            $flash_item->save();

            return $this->success(null,'Show At Home Updated Successfully!',SUCCESS_CODE,'flash_item');

        }catch (ValidationException $ex) {
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }



    public function paginationFunction($data,$dataName)
    {
        return [
            'pagination'=> [
            'currentPage' => $data->currentPage(),
            'lastPage' => $data->lastPage(),
            'totalPages' => $data->lastPage(),
            'perPage' => $data->perPage(),
            'hasNext' => $data->hasMorePages(),
            'hasPrevious' => $data->currentPage() > 1,
            ],
            $dataName => $data->items(),
        ];
    }
}
