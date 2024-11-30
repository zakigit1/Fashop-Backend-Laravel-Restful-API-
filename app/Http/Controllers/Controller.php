<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Send a JSON response with the given data, status, and message.
     *
     * @param mixed $data
     * @param string $status
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function sendResponse(mixed $data = null, string $status , string $message = '', int $statusCode , string $data_name = 'data'): JsonResponse
    {
        if($data == null){ 
            return response()->json([
                'status' => $status,
                'statusCode' => $statusCode,
                'message' => $message,
            ], $statusCode);
        }
        return response()->json([
            'status' => $status,
            'statusCode' => $statusCode,
            'message' => $message,
            "$data_name" => $data,
           
        ], $statusCode);
    }

    /**
     * Send a successful JSON response with the given data and message.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success(mixed $data, string $message = 'okay', int $statusCode = 200, string $data_name = 'data'): JsonResponse
    {
        return $this->sendResponse($data, 'success', $message, $statusCode,$data_name);
    }

    /**
     * Send an error JSON response with the given message and status code.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = 500): JsonResponse
    {
        return $this->sendResponse(null, 'error', $message, $statusCode);
    }


    public function paginationResponse($data, $data_name, string $message, int $statusCode = 200, $custumData = null){

        if($custumData == null){

            return response()->json([
                'status'=>'success',
                'statusCode'=>$statusCode,
                'message'=>$message,
                'pagination'=> [
                    'currentPage' => $data->currentPage(),
                    'lastPage' => $data->lastPage(),
                    'totalPages' => $data->lastPage(),// we add 's'  
                    'perPage' => $data->perPage(),
                    'hasNext' => $data->hasMorePages(),
                    'hasPrevious' => $data->currentPage() > 1,
                ],

                // 'pagination2' => [
                //     'currentPage' => $data->currentPage(),
                //     'totalPages' => $data->lastPage(),
                //     'perPage' => $data->perPage(),
                //     'totalData' => $data->total(),
                //     'DataOnPage' => [
                //         'from' => $data->firstItem(),
                //         'to' => $data->lastItem(),
                //     ],
                //     'navigation' => [
                //         'hasNextPage' => $data->hasMorePages(),
                //         'hasPreviousPage' => $data->previousPageUrl() !== null,
                //         'nextPageUrl' => $data->nextPageUrl(),
                //         'previousPagePrl' => $data->previousPageUrl(),
                //     ],
                // ],


                "$data_name" => $data->items(),
   
            ]);

        }else{
            return response()->json([
                'status'=>'success',
                'statusCode'=>$statusCode,
                'message'=>$message,
                'pagination'=> [
                    'currentPage' => $data->currentPage(),
                    'totalPage' => $data->lastPage(),
                    'perPage' => $data->perPage(),
                    'lastPage' => $data->lastPage(),
                    'hasNext' => $data->hasMorePages(),
                    'hasPrevious' => $data->currentPage() > 1,
                ],
                // "$data_name" => $data->items(),
                "$data_name" => $custumData,
            ]);
        }

    }

}


