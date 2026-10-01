<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewspaperRequest;
use App\Http\Requests\UpdateNewspaperRequest;
use App\Models\Newspaper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewspaperController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Newspaper::class);

        $newspapers = Newspaper::query()
            ->when($request->filled('search'), fn ($q) => $q->where('newspaper_name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('newspaper_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.newspapers.index', compact('newspapers'));
    }

    public function create(): View
    {
        $this->authorize('create', Newspaper::class);

        return view('admin.newspapers.create');
    }

    public function store(StoreNewspaperRequest $request): RedirectResponse
    {
        $this->authorize('create', Newspaper::class);

        Newspaper::create($request->validated());

        return redirect()->route('admin.newspapers.index')->with('success', 'Newspaper created successfully.');
    }

    public function edit(Newspaper $newspaper): View
    {
        $this->authorize('update', $newspaper);

        return view('admin.newspapers.edit', compact('newspaper'));
    }

    public function update(UpdateNewspaperRequest $request, Newspaper $newspaper): RedirectResponse
    {
        $this->authorize('update', $newspaper);

        $newspaper->update($request->validated());

        return redirect()->route('admin.newspapers.index')->with('success', 'Newspaper updated successfully.');
    }

    public function destroy(Newspaper $newspaper): RedirectResponse
    {
        $this->authorize('delete', $newspaper);

        if ($newspaper->newsPrints()->exists()) {
            return redirect()
                ->route('admin.newspapers.index')
                ->with('error', 'Cannot delete a newspaper that is used by print clippings.');
        }

        $newspaper->delete();

        return redirect()->route('admin.newspapers.index')->with('success', 'Newspaper deleted successfully.');
    }
}
