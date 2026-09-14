<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresCampusPublicationFiles;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampusPublicationRequest;
use App\Http\Requests\UpdateCampusPublicationRequest;
use App\Models\CampusPublication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampusPublicationController extends Controller
{
    use StoresCampusPublicationFiles;

    /**
     * List all campus publications.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CampusPublication::class);

        $publications = CampusPublication::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                        ->orWhere('duration', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.campus-publications.index', compact('publications'));
    }

    /**
     * Show create publication form.
     */
    public function create(): View
    {
        $this->authorize('create', CampusPublication::class);

        return view('admin.campus-publications.create');
    }

    /**
     * Store a new campus publication.
     */
    public function store(StoreCampusPublicationRequest $request): RedirectResponse
    {
        $this->authorize('create', CampusPublication::class);

        $validated = $request->validated();
        $fileData = $this->storeCampusPublicationFile($request->file('file'));

        CampusPublication::create([
            'type' => $validated['type'],
            'duration' => $validated['duration'],
            'year' => $validated['year'],
            'file_path' => $fileData['file_path'],
            'original_filename' => $fileData['original_filename'],
        ]);

        return redirect()
            ->route('admin.campus-publications.index')
            ->with('success', 'Campus publication created successfully.');
    }

    /**
     * Show publication details.
     */
    public function show(CampusPublication $campusPublication): View
    {
        $this->authorize('view', $campusPublication);

        return view('admin.campus-publications.show', [
            'publication' => $campusPublication,
        ]);
    }

    /**
     * Show edit publication form.
     */
    public function edit(CampusPublication $campusPublication): View
    {
        $this->authorize('update', $campusPublication);

        return view('admin.campus-publications.edit', [
            'publication' => $campusPublication,
        ]);
    }

    /**
     * Update an existing campus publication.
     */
    public function update(UpdateCampusPublicationRequest $request, CampusPublication $campusPublication): RedirectResponse
    {
        $this->authorize('update', $campusPublication);

        $validated = $request->validated();

        $data = [
            'type' => $validated['type'],
            'duration' => $validated['duration'],
            'year' => $validated['year'],
        ];

        if ($request->hasFile('file')) {
            $this->deleteCampusPublicationFile($campusPublication->file_path);
            $fileData = $this->storeCampusPublicationFile($request->file('file'));
            $data['file_path'] = $fileData['file_path'];
            $data['original_filename'] = $fileData['original_filename'];
        }

        $campusPublication->update($data);

        return redirect()
            ->route('admin.campus-publications.index')
            ->with('success', 'Campus publication updated successfully.');
    }

    /**
     * Delete a campus publication.
     */
    public function destroy(CampusPublication $campusPublication): RedirectResponse
    {
        $this->authorize('delete', $campusPublication);

        $this->deleteCampusPublicationFile($campusPublication->file_path);
        $campusPublication->delete();

        return redirect()
            ->route('admin.campus-publications.index')
            ->with('success', 'Campus publication deleted successfully.');
    }
}
