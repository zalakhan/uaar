<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\FacultyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyMemberBookController extends Controller
{
    /**
     * Manage books for a faculty member on one page.
     */
    public function index(Request $request, FacultyMember $facultyMember): View
    {
        $this->authorize('view', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $facultyMember->load(['designation', 'department']);

        $editingBook = null;
        if ($request->filled('edit')) {
            $this->authorize('update', $facultyMember);
            $editingBook = $facultyMember->books()->findOrFail($request->input('edit'));
        }

        $books = $facultyMember->books()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.faculty-members.books.index', compact(
            'facultyMember',
            'books',
            'editingBook'
        ));
    }

    /**
     * Store a book for a faculty member.
     */
    public function store(Request $request, FacultyMember $facultyMember): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);

        $validated = $this->validateEntry($request);

        $facultyMember->books()->create($validated);

        return redirect()
            ->route('admin.faculty-members.books.index', $facultyMember)
            ->with('success', 'Book added successfully.');
    }

    /**
     * Update a book for a faculty member.
     */
    public function update(Request $request, FacultyMember $facultyMember, Book $book): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $book);

        $validated = $this->validateEntry($request);

        $book->update($validated);

        return redirect()
            ->route('admin.faculty-members.books.index', $facultyMember)
            ->with('success', 'Book updated successfully.');
    }

    /**
     * Delete a book for a faculty member.
     */
    public function destroy(FacultyMember $facultyMember, Book $book): RedirectResponse
    {
        $this->authorize('update', $facultyMember);
        $this->ensureFacultyType($facultyMember);
        $this->ensureBelongsToMember($facultyMember, $book);

        $book->delete();

        return redirect()
            ->route('admin.faculty-members.books.index', $facultyMember)
            ->with('success', 'Book deleted successfully.');
    }

    /**
     * Validate book input.
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
        ]);
    }

    /**
     * Only faculty-type members can have books.
     */
    private function ensureFacultyType(FacultyMember $facultyMember): void
    {
        if ($facultyMember->member_type !== 'faculty') {
            abort(404);
        }
    }

    /**
     * Ensure the book belongs to the faculty member.
     */
    private function ensureBelongsToMember(FacultyMember $facultyMember, Book $book): void
    {
        if ($book->faculty_member_id !== $facultyMember->id) {
            abort(404);
        }
    }
}
