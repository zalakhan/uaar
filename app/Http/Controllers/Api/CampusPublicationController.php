<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusPublicationResource;
use App\Models\CampusPublication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampusPublicationController extends Controller
{
    /**
     * Return all campus publications grouped by type.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['sometimes', Rule::in(CampusPublication::types())],
            'year' => ['sometimes', 'integer'],
        ]);

        $publications = CampusPublication::query()
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('year'), fn ($query) => $query->where('year', (int) $request->input('year')))
            ->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        $grouped = $publications->groupBy('type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'publications' => CampusPublicationResource::collection($items)->resolve(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }
}
