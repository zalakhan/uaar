<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\FacultyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyMemberAwardController extends Controller
{
    /**
     * Manage awards for a faculty member on one page.
     */
    public function index(Request $request, FacultyMember $facultyMember): View
    {
        $this->authorize('view', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $facultyMember->load(['designation', 'department']);

        $editingAward = null;
        if ($request->filled('edit')) {
            $this->authorize('update', $facultyMember);
            $editingAward = $facultyMember->awards()->findOrFail($request->input('edit'));
        }

        $awards = $facultyMember->awards()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.faculty-members.awards.index', compact(
            'facultyMember',
            'awards',
            'editingAward'
        ));
    }

    /**
     * Store an award for a faculty member.
     */
    public function store(Request $request, FacultyMember $facultyMember): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $validated = $this->validateEntry($request);

        $facultyMember->awards()->create($validated);

        return redirect()
            ->route('admin.faculty-members.awards.index', $facultyMember)
            ->with('success', 'Award added successfully.');
    }

    /**
     * Update an award for a faculty member.
     */
    public function update(Request $request, FacultyMember $facultyMember, Award $award): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $award);

        $validated = $this->validateEntry($request);

        $award->update($validated);

        return redirect()
            ->route('admin.faculty-members.awards.index', $facultyMember)
            ->with('success', 'Award updated successfully.');
    }

    /**
     * Delete an award for a faculty member.
     */
    public function destroy(FacultyMember $facultyMember, Award $award): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $award);

        $award->delete();

        return redirect()
            ->route('admin.faculty-members.awards.index', $facultyMember)
            ->with('success', 'Award deleted successfully.');
    }

    /**
     * Validate award input.
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ]);
    }

    /**
     * Only faculty-type members can have awards.
     */
    private function ensureFacultyType(FacultyMember $facultyMember): void
    {
        if ($facultyMember->member_type !== 'faculty') {
            abort(404);
        }
    }

    /**
     * Ensure the award belongs to the faculty member.
     */
    private function ensureBelongsToMember(FacultyMember $facultyMember, Award $award): void
    {
        if ($award->faculty_member_id !== $facultyMember->id) {
            abort(404);
        }
    }
}
