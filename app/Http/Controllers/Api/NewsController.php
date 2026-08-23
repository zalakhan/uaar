<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class NewsController extends Controller
{
    /**
     * Return active news articles (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $newsItems = News::where('status', true)
            ->when($request->has('department_id'), function ($query) use ($request) {
                if ($request->input('department_id') === News::MAIN_WEBSITE_SCOPE) {
                    $query->whereNull('department_id');
                } else {
                    $query->where('department_id', $request->input('department_id'));
                }
            })
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => NewsResource::collection($newsItems->items())->resolve(),
            'meta' => [
                'current_page' => $newsItems->currentPage(),
                'last_page' => $newsItems->lastPage(),
                'per_page' => $newsItems->perPage(),
                'total' => $newsItems->total(),
            ],
        ]);
    }

    /**
     * Return a single active news article.
     */
    public function show(string $id): JsonResponse
    {
        $decoded = Hashids::decode($id);

        if ($decoded === [] || ! isset($decoded[0])) {
            return response()->json([
                'success' => false,
                'message' => 'News article not found.',
            ], 404);
        }

        $news = News::where('status', true)->find($decoded[0]);

        if (! $news) {
            return response()->json([
                'success' => false,
                'message' => 'News article not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new NewsResource($news),
        ]);
    }
}
