<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ProvidesDepartmentOptions;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobController extends Controller
{
    use ProvidesDepartmentOptions;

    /**
     * List research jobs (scoped by department when applicable).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Job::class);

        $jobs = Job::with('department')
            ->forUser(auth()->user(), true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('department', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('last_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show create job form.
     */
    public function create(): View
    {
        $this->authorize('create', Job::class);

        return view('admin.jobs.create', $this->formData());
    }

    /**
     * Store a new research job.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Job::class);

        $validated = $this->validateJob($request);

        $validated['job_file'] = $request->file('job_file')->store('jobs', 'public');

        Job::create($validated);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Research job created successfully.');
    }

    /**
     * Show job details.
     */
    public function show(Job $job): View
    {
        $this->authorize('view', $job);

        $job->load('department');

        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show edit job form.
     */
    public function edit(Job $job): View
    {
        $this->authorize('update', $job);

        return view('admin.jobs.edit', array_merge(
            compact('job'),
            $this->formData($job)
        ));
    }

    /**
     * Update an existing research job.
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->authorize('update', $job);

        $validated = $this->validateJob($request, $job);

        if ($request->hasFile('job_file')) {
            Storage::disk('public')->delete($job->job_file);
            $validated['job_file'] = $request->file('job_file')->store('jobs', 'public');
        } else {
            unset($validated['job_file']);
        }

        $job->update($validated);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Research job updated successfully.');
    }

    /**
     * Delete a research job.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        Storage::disk('public')->delete($job->job_file);
        $job->delete();

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Research job deleted successfully.');
    }

    /**
     * Shared form data for create/edit views.
     */
    private function formData(?Job $job = null): array
    {
        $user = auth()->user();
        $departments = Department::where('is_active', true)->orderBy('name');

        if (! $user->isSuperAdmin()) {
            $departments->where('id', $user->department_id);
        }

        return [
            'departments' => $departments->pluck('name', 'id'),
            'defaultDepartmentId' => $job?->department_id,
        ];
    }

    /**
     * Validate job input with department scoping.
     */
    private function validateJob(Request $request, ?Job $job = null): array
    {
        $user = auth()->user();

        $departmentRules = ['nullable', 'exists:departments,id'];

        if (! $user->isSuperAdmin()) {
            $departmentRules[] = Rule::in([$user->department_id]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => $departmentRules,
            'last_date' => ['required', 'date'],
            'job_file' => [$job ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $validated['department_id'] = $validated['department_id'] ?? null;

        return $validated;
    }
}
