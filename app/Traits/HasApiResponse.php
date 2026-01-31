<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait HasApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse($data = [], ?string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Don't add data key if data is empty
        if (!empty($data) || (is_array($data) && count($data) > 0)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a standardized paginated response.
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $dataKey = 'items', ?string $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                $dataKey => $paginator->items(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message, int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
