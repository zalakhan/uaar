<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffMemberResource;
use App\Models\StaffMember;
use Illuminate\Http\JsonResponse;
use Vinkla\Hashids\Facades\Hashids;

class StaffMemberController extends Controller
{
    /**
     * Return all active staff members (no pagination).
     */
    public function index(): JsonResponse
    {
        $members = StaffMember::where('is_active', true)
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
            'data' => StaffMemberResource::collection($members),
        ]);
    }

    /**
     * Return a single active staff member.
     */
    public function show(string $id): JsonResponse
    {
        $decoded = Hashids::decode($id);

        if ($decoded === [] || ! isset($decoded[0])) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found.',
            ], 404);
        }

        $member = StaffMember::where('is_active', true)
            ->with([
                'designation',
                'additionalDesignation',
                'department',
                'additionalDepartment',
                'faculty',
            ])
            ->find($decoded[0]);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new StaffMemberResource($member),
        ]);
    }
}
