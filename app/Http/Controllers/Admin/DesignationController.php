<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DesignationController extends Controller
{
    /**
     * List all designations.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Designation::class);

        $designations = Designation::when($request->filled('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.designations.index', compact('designations'));
    }

    /**
     * Show create designation form.
     */
    public function create(): View
    {
        $this->authorize('create', Designation::class);

        return view('admin.designations.create');
    }

    /**
     * Store a new designation.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Designation::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:designations,name'],
        ]);

        Designation::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation created successfully.');
    }

    /**
     * Show designation details.
     */
    public function show(Designation $designation): View
    {
        $this->authorize('view', $designation);

        return view('admin.designations.show', compact('designation'));
    }

    /**
     * Show edit designation form.
     */
    public function edit(Designation $designation): View
    {
        $this->authorize('update', $designation);

        return view('admin.designations.edit', compact('designation'));
    }

    /**
     * Update an existing designation.
     */
    public function update(Request $request, Designation $designation): RedirectResponse
    {
        $this->authorize('update', $designation);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('designations', 'name')->ignore($designation->designation_id, 'designation_id'),
            ],
        ]);

        $designation->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $designation->designation_id),
        ]);

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    /**
     * Delete a designation.
     */
    public function destroy(Designation $designation): RedirectResponse
    {
        $this->authorize('delete', $designation);

        $designation->delete();

        return redirect()
            ->route('admin.designations.index')
            ->with('success', 'Designation deleted successfully.');
    }

    /**
     * Generate a unique slug from the designation name.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            Designation::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('designation_id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
