<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ProvidesDepartmentOptions;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GalleryController extends Controller
{
    use ProvidesDepartmentOptions;

    /**
     * List photo galleries (scoped by department when applicable).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Gallery::class);

        $galleries = Gallery::with('department')
            ->forUser(auth()->user(), true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('department', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show create gallery form.
     */
    public function create(): View
    {
        $this->authorize('create', Gallery::class);

        return view('admin.galleries.create', $this->formData());
    }

    /**
     * Store a new gallery.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Gallery::class);

        $validated = $this->validateGallery($request);
        $validated['status'] = $request->boolean('status');

        Gallery::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Gallery created successfully.');
    }

    /**
     * Show gallery details.
     */
    public function show(Gallery $gallery): View
    {
        $this->authorize('view', $gallery);

        $gallery->load(['department', 'photos']);

        return view('admin.galleries.show', compact('gallery'));
    }

    /**
     * Show edit gallery form.
     */
    public function edit(Gallery $gallery): View
    {
        $this->authorize('update', $gallery);

        return view('admin.galleries.edit', array_merge(
            compact('gallery'),
            $this->formData($gallery)
        ));
    }

    /**
     * Update an existing gallery.
     */
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $this->authorize('update', $gallery);

        $validated = $this->validateGallery($request, $gallery);
        $validated['status'] = $request->boolean('status');

        $gallery->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'],
            'slug' => $this->uniqueSlug($validated['name'], $gallery->id),
        ]);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Gallery updated successfully.');
    }

    /**
     * Delete a gallery and its photos.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);

        foreach ($gallery->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        $gallery->delete();

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Gallery deleted successfully.');
    }

    /**
     * Shared form data for create/edit views.
     */
    private function formData(?Gallery $gallery = null): array
    {
        $user = auth()->user();
        $departments = Department::where('is_active', true)->orderBy('name');

        if (! $user->isSuperAdmin()) {
            $departments->where('id', $user->department_id);
        }

        return [
            'departments' => $departments->pluck('name', 'id'),
            'defaultDepartmentId' => $gallery?->department_id ?? $user->department_id,
            'defaultStatus' => old('status', $gallery?->status ?? true),
        ];
    }

    /**
     * Validate gallery input with department scoping.
     */
    private function validateGallery(Request $request, ?Gallery $gallery = null): array
    {
        $user = auth()->user();

        $departmentRules = ['nullable', 'exists:departments,id'];

        if (! $user->isSuperAdmin()) {
            $departmentRules[] = Rule::in([$user->department_id]);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'department_id' => $departmentRules,
            'status' => ['required', 'in:0,1'],
        ]);
    }

    /**
     * Generate a unique slug from the gallery name.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            Gallery::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
