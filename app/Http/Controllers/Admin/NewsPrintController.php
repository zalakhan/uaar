<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsPrintRequest;
use App\Http\Requests\UpdateNewsPrintRequest;
use App\Models\NewsPrint;
use App\Models\NewsprintAlbum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class NewsPrintController extends Controller
{
    public function store(StoreNewsPrintRequest $request, NewsprintAlbum $newsprintAlbum): RedirectResponse
    {
        $this->authorize('update', $newsprintAlbum);

        foreach ($request->validated('clippings') as $index => $clipping) {
            $file = $request->file("clippings.{$index}.news_file");

            $newsprintAlbum->newsPrints()->create([
                'newspaper_id' => $clipping['newspaper_id'],
                'news_file' => $file->store('newsprint/'.$newsprintAlbum->id, 'public'),
            ]);
        }

        return redirect()
            ->route('admin.newsprint-albums.show', $newsprintAlbum)
            ->with('success', 'Clipping(s) uploaded successfully.');
    }

    public function update(UpdateNewsPrintRequest $request, NewsprintAlbum $newsprintAlbum, NewsPrint $newsPrint): RedirectResponse
    {
        $this->authorize('update', $newsprintAlbum);
        $this->ensureBelongsToAlbum($newsprintAlbum, $newsPrint);

        $data = ['newspaper_id' => $request->validated('newspaper_id')];

        if ($request->hasFile('news_file')) {
            Storage::disk('public')->delete($newsPrint->news_file);
            $data['news_file'] = $request->file('news_file')->store('newsprint/'.$newsprintAlbum->id, 'public');
        }

        $newsPrint->update($data);

        return redirect()
            ->route('admin.newsprint-albums.show', $newsprintAlbum)
            ->with('success', 'Clipping updated successfully.');
    }

    public function destroy(NewsprintAlbum $newsprintAlbum, NewsPrint $newsPrint): RedirectResponse
    {
        $this->authorize('update', $newsprintAlbum);
        $this->ensureBelongsToAlbum($newsprintAlbum, $newsPrint);

        Storage::disk('public')->delete($newsPrint->news_file);
        $newsPrint->delete();

        return redirect()
            ->route('admin.newsprint-albums.show', $newsprintAlbum)
            ->with('success', 'Clipping deleted successfully.');
    }

    private function ensureBelongsToAlbum(NewsprintAlbum $album, NewsPrint $newsPrint): void
    {
        if ($newsPrint->newsprint_album_id !== $album->id) {
            abort(404);
        }
    }
}
