<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function redirectSuccess(string $message, $route)
    {
        return redirect()->route($route)->with('toast', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function redirectError(string $message, $route)
    {
        return redirect()->route($route)->with('toast', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    protected function apiSuccessResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    protected function apiErrorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ], $code);
    }
}
