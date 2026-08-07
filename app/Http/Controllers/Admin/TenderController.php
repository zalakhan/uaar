<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Concerns\ValidatesTenderFields;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenderController extends Controller
{
    use ValidatesTenderFields;

    /**
     * List all tenders.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tender::class);

        $tenders = Tender::when($request->filled('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('tender_no', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        })
            ->orderByDesc('uploaded_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tenders.index', compact('tenders'));
    }

    /**
     * Show create tender form.
     */
    public function create(): View
    {
        $this->authorize('create', Tender::class);

        return view('admin.tenders.create');
    }

    /**
     * Store a new tender.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Tender::class);

        $validated = $this->normalizeTenderData(
            $request->validate($this->tenderRules($request))
        );

        $validated['tender_file'] = $request->file('tender_file')->store('tenders', 'public');

        Tender::create($validated);

        return redirect()
            ->route('admin.tenders.index')
            ->with('success', 'Tender created successfully.');
    }

    /**
     * Show tender details.
     */
    public function show(Tender $tender): View
    {
        $this->authorize('view', $tender);

        return view('admin.tenders.show', compact('tender'));
    }

    /**
     * Show edit tender form.
     */
    public function edit(Tender $tender): View
    {
        $this->authorize('update', $tender);

        return view('admin.tenders.edit', compact('tender'));
    }

    /**
     * Update an existing tender.
     */
    public function update(Request $request, Tender $tender): RedirectResponse
    {
        $this->authorize('update', $tender);

        $validated = $this->normalizeTenderData(
            $request->validate($this->tenderRules($request, isUpdate: true))
        );

        if ($request->hasFile('tender_file')) {
            Storage::disk('public')->delete($tender->tender_file);
            $validated['tender_file'] = $request->file('tender_file')->store('tenders', 'public');
        } else {
            unset($validated['tender_file']);
        }

        $tender->update($validated);

        return redirect()
            ->route('admin.tenders.index')
            ->with('success', 'Tender updated successfully.');
    }

    /**
     * Delete a tender.
     */
    public function destroy(Tender $tender): RedirectResponse
    {
        $this->authorize('delete', $tender);

        Storage::disk('public')->delete($tender->tender_file);
        $tender->delete();

        return redirect()
            ->route('admin.tenders.index')
            ->with('success', 'Tender deleted successfully.');
    }
}
