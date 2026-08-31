<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class GalleryController extends Controller
{
    /**
     * Return active galleries (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $galleries = Gallery::where('status', true)
            ->when($request->has('department_id'), function ($query) use ($request) {
                if ($request->input('department_id') === 'main') {
                    $query->whereNull('department_id');
                } else {
                    $query->where('department_id', $request->input('department_id'));
                }
            })
            ->with('thumbnail')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => GalleryResource::collection($galleries->items())->resolve(),
            'meta' => [
                'current_page' => $galleries->currentPage(),
                'last_page' => $galleries->lastPage(),
                'per_page' => $galleries->perPage(),
                'total' => $galleries->total(),
            ],
        ]);
    }

    /**
     * Return a single active gallery with its photos.
     */
    public function show(string $id): JsonResponse
    {
        $decoded = Hashids::decode($id);

        if ($decoded === [] || ! isset($decoded[0])) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery not found.',
            ], 404);
        }

        $gallery = Gallery::where('status', true)
            ->with('photos')
            ->find($decoded[0]);

        if (! $gallery) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new GalleryResource($gallery),
        ]);
    }
}
