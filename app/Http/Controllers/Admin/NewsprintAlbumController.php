<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsprintAlbumRequest;
use App\Http\Requests\UpdateNewsprintAlbumRequest;
use App\Models\NewsprintAlbum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsprintAlbumController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsprintAlbum::class);

        $albums = NewsprintAlbum::query()
            ->withCount('newsPrints')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->input('search').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status') === '1'))
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.newsprint-albums.index', compact('albums'));
    }

    public function create(): View
    {
        $this->authorize('create', NewsprintAlbum::class);

        return view('admin.newsprint-albums.create');
    }

    public function store(StoreNewsprintAlbumRequest $request): RedirectResponse
    {
        $this->authorize('create', NewsprintAlbum::class);

        NewsprintAlbum::create($request->validated());

        return redirect()->route('admin.newsprint-albums.index')->with('success', 'Album created successfully.');
    }

    public function show(NewsprintAlbum $newsprintAlbum): View
    {
        $this->authorize('view', $newsprintAlbum);

        $newsprintAlbum->load(['newsPrints.newspaper']);

        $newspapers = \App\Models\Newspaper::orderBy('newspaper_name')->pluck('newspaper_name', 'id');

        return view('admin.newsprint-albums.show', [
            'album' => $newsprintAlbum,
            'newspapers' => $newspapers,
        ]);
    }

    public function edit(NewsprintAlbum $newsprintAlbum): View
    {
        $this->authorize('update', $newsprintAlbum);

        return view('admin.newsprint-albums.edit', ['album' => $newsprintAlbum]);
    }

    public function update(UpdateNewsprintAlbumRequest $request, NewsprintAlbum $newsprintAlbum): RedirectResponse
    {
        $this->authorize('update', $newsprintAlbum);

        $newsprintAlbum->update($request->validated());

        return redirect()->route('admin.newsprint-albums.index')->with('success', 'Album updated successfully.');
    }

    public function destroy(NewsprintAlbum $newsprintAlbum): RedirectResponse
    {
        $this->authorize('delete', $newsprintAlbum);

        $newsprintAlbum->deleteStoredFiles();
        $newsprintAlbum->delete();

        return redirect()->route('admin.newsprint-albums.index')->with('success', 'Album and its clippings deleted successfully.');
    }
}
