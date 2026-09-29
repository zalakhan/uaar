<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyMemberResource;
use App\Models\FacultyMember;
use Illuminate\Http\JsonResponse;
use Vinkla\Hashids\Facades\Hashids;

class FacultyMemberController extends Controller
{
    /**
     * Return all active faculty members (no pagination).
     */
    public function index(): JsonResponse
    {
        $members = FacultyMember::facultyType()
            ->where('is_active', true)
            ->with([
                'designation',
                'additionalDesignation',
                'department',
                'additionalDepartment',
                'faculty',
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FacultyMemberResource::collection($members),
        ]);
    }

    /**
     * Return a single active faculty member with publications, awards, and books.
     */
    public function show(string $id): JsonResponse
    {
        $decoded = Hashids::decode($id);

        if ($decoded === [] || ! isset($decoded[0])) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty member not found.',
            ], 404);
        }

        $member = FacultyMember::facultyType()
            ->where('is_active', true)
            ->with([
                'designation',
                'additionalDesignation',
                'department',
                'additionalDepartment',
                'faculty',
                'publications' => fn ($query) => $query->orderByDesc('year')->orderByDesc('id'),
                'awards' => fn ($query) => $query->orderByDesc('year')->orderByDesc('id'),
                'books' => fn ($query) => $query->orderByDesc('year')->orderByDesc('id'),
            ])
            ->find($decoded[0]);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FacultyMemberResource($member),
        ]);
    }
}
