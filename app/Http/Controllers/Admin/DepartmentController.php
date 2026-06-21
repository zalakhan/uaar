<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * List all departments.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Department::class);

        $departments = Department::with('faculty')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show create department form.
     */
    public function create(): View
    {
        $this->authorize('create', Department::class);

        $faculties = Faculty::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.departments.create', compact('faculties'));
    }

    /**
     * Store a new department.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Department::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'is_active' => ['boolean'],
        ]);

        Department::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'faculty_id' => $validated['faculty_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show department details.
     */
    public function show(Department $department): View
    {
        $this->authorize('view', $department);

        $department->load('faculty');

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show edit department form.
     */
    public function edit(Department $department): View
    {
        $this->authorize('update', $department);

        $faculties = Faculty::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.departments.edit', compact('department', 'faculties'));
    }

    /**
     * Update an existing department.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'is_active' => ['boolean'],
        ]);

        $department->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $department->id),
            'faculty_id' => $validated['faculty_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete a department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('delete', $department);

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Generate a unique slug from the department name.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            Department::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
