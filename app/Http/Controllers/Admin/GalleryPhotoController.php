<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\Gallery;

use App\Models\GalleryPhoto;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\View\View;



class GalleryPhotoController extends Controller

{

    /**

     * Manage photos for a gallery on one page.

     */

    public function index(Gallery $gallery): View

    {

        $this->authorize('view', $gallery);



        $gallery->load('department');



        $photos = $gallery->photos()

            ->orderBy('sort_order')

            ->orderBy('id')

            ->get();



        return view('admin.galleries.photos.index', compact('gallery', 'photos'));

    }



    /**

     * Upload one or more photos to a gallery.

     */

    public function store(Request $request, Gallery $gallery): RedirectResponse

    {

        $this->authorize('update', $gallery);



        $validated = $request->validate([

            'photos' => ['required', 'array', 'min:1'],

            'photos.*' => ['image', 'max:2048'],

        ]);



        $nextSortOrder = (int) $gallery->photos()->max('sort_order') + 1;



        foreach ($validated['photos'] as $file) {

            $gallery->photos()->create([

                'photo' => $file->store('galleries', 'public'),

                'sort_order' => $nextSortOrder++,

            ]);

        }



        return redirect()

            ->route('admin.galleries.photos.index', $gallery)

            ->with('success', 'Photo(s) uploaded successfully.');

    }



    /**

     * Save drag-and-drop photo order for a gallery.

     */

    public function updateOrder(Request $request, Gallery $gallery): JsonResponse

    {

        $this->authorize('update', $gallery);



        $validated = $request->validate([

            'order' => ['required', 'array', 'min:1'],

            'order.*' => ['integer', 'exists:gallery_photos,id'],

        ]);



        $photoIds = $gallery->photos()

            ->whereIn('id', $validated['order'])

            ->pluck('id');



        if ($photoIds->count() !== count($validated['order'])) {

            return response()->json(['message' => 'Invalid photo selection for this gallery.'], 422);

        }



        foreach ($validated['order'] as $index => $photoId) {

            GalleryPhoto::where('id', $photoId)->update(['sort_order' => $index]);

        }



        return response()->json(['message' => 'Photo order saved successfully.']);

    }



    /**

     * Delete a photo from a gallery.

     */

    public function destroy(Gallery $gallery, GalleryPhoto $photo): RedirectResponse

    {

        $this->authorize('update', $gallery);

        $this->ensureBelongsToGallery($gallery, $photo);



        Storage::disk('public')->delete($photo->photo);

        $photo->delete();



        return redirect()

            ->route('admin.galleries.photos.index', $gallery)

            ->with('success', 'Photo deleted successfully.');

    }



    /**

     * Ensure the photo belongs to the gallery.

     */

    private function ensureBelongsToGallery(Gallery $gallery, GalleryPhoto $photo): void

    {

        if ($photo->gallery_id !== $gallery->id) {

            abort(404);

        }

    }

}

