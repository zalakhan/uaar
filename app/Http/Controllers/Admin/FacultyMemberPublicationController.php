<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacultyMember;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyMemberPublicationController extends Controller
{
    /**
     * Manage publications for a faculty member on one page.
     */
    public function index(Request $request, FacultyMember $facultyMember): View
    {
        $this->authorize('view', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $facultyMember->load(['designation', 'department']);

        $editingPublication = null;
        if ($request->filled('edit')) {
            $this->authorize('update', $facultyMember);
            $editingPublication = $facultyMember->publications()->findOrFail($request->input('edit'));
        }

        $publications = $facultyMember->publications()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.faculty-members.publications.index', compact(
            'facultyMember',
            'publications',
            'editingPublication'
        ));
    }

    /**
     * Store a publication for a faculty member.
     */
    public function store(Request $request, FacultyMember $facultyMember): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $validated = $this->validateEntry($request);

        $facultyMember->publications()->create($validated);

        return redirect()
            ->route('admin.faculty-members.publications.index', $facultyMember)
            ->with('success', 'Publication added successfully.');
    }

    /**
     * Update a publication for a faculty member.
     */
    public function update(Request $request, FacultyMember $facultyMember, Publication $publication): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $publication);

        $validated = $this->validateEntry($request);

        $publication->update($validated);

        return redirect()
            ->route('admin.faculty-members.publications.index', $facultyMember)
            ->with('success', 'Publication updated successfully.');
    }

    /**
     * Delete a publication for a faculty member.
     */
    public function destroy(FacultyMember $facultyMember, Publication $publication): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $publication);

        $publication->delete();

        return redirect()
            ->route('admin.faculty-members.publications.index', $facultyMember)
            ->with('success', 'Publication deleted successfully.');
    }

    /**
     * Validate publication input.
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ]);
    }

    /**
     * Only faculty-type members can have publications.
     */
    private function ensureFacultyType(FacultyMember $facultyMember): void
    {
        if ($facultyMember->member_type !== 'faculty') {
            abort(404);
        }
    }

    /**
     * Ensure the publication belongs to the faculty member.
     */
    private function ensureBelongsToMember(FacultyMember $facultyMember, Publication $publication): void
    {
        if ($publication->faculty_member_id !== $facultyMember->id) {
            abort(404);
        }
    }
}
