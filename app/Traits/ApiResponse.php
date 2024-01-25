<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * @param string $message
     * @param $data
     * @param $error
     * @param int $status
     * @return JsonResponse
     */
    public function SendResponse(bool $success, string $message, $data, $error, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data'    => $data,
            'error'   => $error,
        ];

        return response()->json($response, $status);
    }

    /**
     * @param string $message
     * @param $data
     * @param int $status
     * @param $error
     * @return JsonResponse
     */
    public function SendSuccessResponse(string $message, $data, int $status = Response::HTTP_OK, $error = null,): JsonResponse
    {
        return $this->SendResponse(true, $message, $data, $error, $status);
    }

    /**
     * @param string $message
     * @param $error
     * @param int $status
     * @param $data
     * @return JsonResponse
     */
    public function SendErrorResponse(string $message, $error, int $status = Response::HTTP_INTERNAL_SERVER_ERROR, $data = null): JsonResponse
    {
        return $this->SendResponse(false, $message, $data, $error, $status);
    }
}
