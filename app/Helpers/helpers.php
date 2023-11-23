<?php

/**
 * @param $data
 * @param $count
 * @param string $message
 * @param int $status
 * @return \Illuminate\Http\JsonResponse
 */
function successResponse($data = [], int $count = 0, string $message = '', int $status = 200): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'message' => $message,
        'success' => true,
        'data' => $data,
        'count' => $count,
        'status' => $status
    ], $status);
}

/**
 * @param $message
 * @param $status
 * @return \Illuminate\Http\JsonResponse
 */
function failResponse($message, $status = 500): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'message' => $message,
        'success' => false,
        'status' => $status,
    ], $status);
}