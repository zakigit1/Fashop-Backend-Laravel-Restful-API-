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
    public function sendResponse(mixed $data, string $status = 'success', string $message = '', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'status' => $status,
            'message' => $message,
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
    public function success(mixed $data, string $message = 'okay', int $statusCode = 200): JsonResponse
    {
        return $this->sendResponse($data, 'success', $message, $statusCode);
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
}


