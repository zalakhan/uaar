<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    /**
     * Return tenders grouped by category.
     */
    public function index(Request $request): JsonResponse
    {
        $tenders = Tender::query()
            ->when(
                $request->filled('due_date_from') && $request->filled('due_date_to'),
                function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->whereIn('category', ['Purchase', 'Auction'])
                                ->whereDate('due_date', '>=', $request->input('due_date_from'))
                                ->whereDate('due_date', '<=', $request->input('due_date_to'));
                        })->orWhere(function ($query) {
                            $query->whereNotIn('category', ['Purchase', 'Auction'])
                                ->whereYear('due_date', now()->year);
                        });
                    });
                },
                fn ($query) => $query->whereYear('due_date', now()->year)
            )
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
