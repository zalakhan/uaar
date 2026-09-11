<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;

class TenderController extends Controller
{
    /**
     * Return tenders for the current year, grouped by category.
     */
    public function index(): JsonResponse
    {
        $tenders = Tender::whereYear('due_date', now()->year)
            ->orderByDesc('uploaded_date')
            ->orderByDesc('id')
            ->get();

        $grouped = $tenders->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => $category,
                'tenders' => TenderResource::collection($items)->resolve(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }
}
