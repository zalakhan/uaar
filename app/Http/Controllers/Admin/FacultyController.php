<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacultyController extends Controller
{
    /**
     * List all faculties.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Faculty::class);

        $faculties = Faculty::withCount('departments')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show create faculty form.
     */
    public function create(): View
    {
        $this->authorize('create', Faculty::class);

        return view('admin.faculties.create');
    }

    /**
     * Store a new faculty.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Faculty::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        Faculty::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.faculties.index')
            ->with('success', 'Faculty created successfully.');
    }

    /**
     * Show faculty details.
     */
    public function show(Faculty $faculty): View
    {
        $this->authorize('view', $faculty);

        $faculty->load('departments');

        return view('admin.faculties.show', compact('faculty'));
    }

    /**
     * Show edit faculty form.
     */
    public function edit(Faculty $faculty): View
    {
        $this->authorize('update', $faculty);

        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Update an existing faculty.
     */
    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $this->authorize('update', $faculty);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $faculty->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $faculty->id),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.faculties.index')
            ->with('success', 'Faculty updated successfully.');
    }

    /**
     * Delete a faculty.
     */
    public function destroy(Faculty $faculty): RedirectResponse
    {
        $this->authorize('delete', $faculty);

        $faculty->delete();

        return redirect()
            ->route('admin.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    }

    /**
     * Generate a unique slug from the faculty name.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            Faculty::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
