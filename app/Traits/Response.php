<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait Response{
    public static function successResponse($message = 'success', $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message], $code);
    }

    public static function successResponseWithData($data = [], $message = 'success', $code = 200, $token = null): JsonResponse
    {
        if( $token ){

            return response()->json(['success' => true, 'message' => $message, 'data' => $data, 'token' => $token], $code);

        }

        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    public static function successResponseWithMetadata($data = [], $metadata = [], $message = 'success', $code = 200 ): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'metadata' => $metadata, 'data' => $data ], $code );

    }

    public static function errorResponse($message = 'Something bad happened', $code = 403): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
