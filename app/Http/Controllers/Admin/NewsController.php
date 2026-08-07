<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\Department;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * List all news articles.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', News::class);

        $newsItems = News::with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                if ($request->input('department') === News::MAIN_WEBSITE_SCOPE) {
                    $query->whereNull('department_id');
                } else {
                    $query->where('department_id', $request->input('department'));
                }
            })
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $departments = Department::orderBy('name')->pluck('name', 'id');

        return view('admin.news.index', compact('newsItems', 'departments'));
    }

    /**
     * Show create news form.
     */
    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('admin.news.create', $this->formData());
    }

    /**
     * Store a new news article.
     */
    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $this->authorize('create', News::class);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        News::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'published_date' => $validated['published_date'],
            'image' => $validated['image'] ?? null,
            'slug' => $this->uniqueSlug($validated['title']),
            'status' => $validated['status'],
            'department_id' => $validated['department_id'],
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show news article details.
     */
    public function show(News $news): View
    {
        $this->authorize('view', $news);

        $news->load('department');

        return view('admin.news.show', compact('news'));
    }

    /**
     * Show edit news form.
     */
    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        return view('admin.news.edit', array_merge(
            compact('news'),
            $this->formData($news)
        ));
    }

    /**
     * Update an existing news article.
     */
    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $this->authorize('update', $news);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'published_date' => $validated['published_date'],
            'image' => $validated['image'] ?? $news->image,
            'slug' => $this->uniqueSlug($validated['title'], $news->id),
            'status' => $validated['status'],
            'department_id' => $validated['department_id'],
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Delete a news article.
     */
    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    /**
     * Generate a unique slug from the title.
     */
    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Shared form data for create/edit views.
     *
     * @return array<string, mixed>
     */
    private function formData(?News $news = null): array
    {
        return [
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
            'selectedDepartmentScope' => old(
                'department_scope',
                $news?->departmentScopeValue() ?? News::MAIN_WEBSITE_SCOPE
            ),
        ];
    }
}
