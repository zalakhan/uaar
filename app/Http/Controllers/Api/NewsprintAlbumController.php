<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsprintAlbumResource;
use App\Models\NewsprintAlbum;
use Illuminate\Http\JsonResponse;
use Vinkla\Hashids\Facades\Hashids;

class NewsprintAlbumController extends Controller
{
    public function index(): JsonResponse
    {
        $albums = NewsprintAlbum::where('status', true)
            ->with(['newsPrints.newspaper'])
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => NewsprintAlbumResource::collection($albums)->resolve(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $decoded = Hashids::decode($id);

        if ($decoded === [] || ! isset($decoded[0])) {
            return response()->json([
                'success' => false,
                'message' => 'Newsprint album not found.',
            ], 404);
        }

        $album = NewsprintAlbum::where('status', true)
            ->with(['newsPrints.newspaper'])
            ->find($decoded[0]);

        if (! $album) {
            return response()->json([
                'success' => false,
                'message' => 'Newsprint album not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new NewsprintAlbumResource($album),
        ]);
    }
}
