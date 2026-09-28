<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ProvidesDepartmentOptions;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\FacultyMember;
use App\Rules\NotEmptyHtml;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyMemberController extends Controller
{
    use ProvidesDepartmentOptions;

    /**
     * List faculty members (scoped to user's department when not super admin).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', FacultyMember::class);

        $members = FacultyMember::facultyType()
            ->with(['department', 'faculty', 'designation'])
            ->forUser(auth()->user(), true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('department', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faculty-members.index', compact('members'));
    }

    /**
     * Show create faculty member form.
     */
    public function create(): View
    {
        $this->authorize('create', FacultyMember::class);

        return view('admin.faculty-members.create', $this->formData());
    }

    /**
     * Store a new faculty member.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', FacultyMember::class);

        $validated = $this->validateMember($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('faculty-members', 'public');
        } else {
            $validated['photo'] = 'faculty-members/dummy.jpg';
        }

        $validated['member_type'] = 'faculty';
        $validated['sort_order'] = $this->nextSortOrder($validated['faculty_id'], $validated['department_id']);

        FacultyMember::create($validated);

        return redirect()
            ->route('admin.faculty-members.index')
            ->with('success', 'Faculty member created successfully.');
    }

    /**
     * Show faculty member details.
     */
    public function show(FacultyMember $facultyMember): View
    {
        $this->authorize('view', $facultyMember);

        $facultyMember->load(['department', 'faculty', 'designation', 'additionalDesignation', 'additionalDepartment']);

        return view('admin.faculty-members.show', ['member' => $facultyMember]);
    }

    /**
     * Show edit faculty member form.
     */
    public function edit(FacultyMember $facultyMember): View
    {
        $this->authorize('update', $facultyMember);

        return view('admin.faculty-members.edit', array_merge(
            ['member' => $facultyMember],
            $this->formData($facultyMember)
        ));
    }

    /**
     * Update an existing faculty member.
     */
    public function update(Request $request, FacultyMember $facultyMember): RedirectResponse
    {
        $this->authorize('update', $facultyMember);

        $validated = $this->validateMember($request, $facultyMember);

        if ($request->hasFile('photo')) {
            if ($facultyMember->photo && $facultyMember->photo !== 'faculty-members/dummy.jpg') {
                Storage::disk('public')->delete($facultyMember->photo);
            }
            $validated['photo'] = $request->file('photo')->store('faculty-members', 'public');
        } elseif (! $facultyMember->photo) {
            $validated['photo'] = 'faculty-members/dummy.jpg';
        }

        $facultyMember->update($validated);

        return redirect()
            ->route('admin.faculty-members.index')
            ->with('success', 'Faculty member updated successfully.');
    }

    /**
     * Delete a faculty member.
     */
    public function destroy(FacultyMember $facultyMember): RedirectResponse
    {
        $this->authorize('delete', $facultyMember);

        if ($facultyMember->photo) {
            Storage::disk('public')->delete($facultyMember->photo);
        }

        $facultyMember->delete();

        return redirect()
            ->route('admin.faculty-members.index')
            ->with('success', 'Faculty member deleted successfully.');
    }

    /**
     * Dropdown data for create/edit forms.
     */
    private function formData(?FacultyMember $member = null): array
    {
        $user = auth()->user();

        return [
            'faculties' => $this->facultiesForSelect(),
            'departmentsByFaculty' => $this->departmentsByFacultyForJs($user),
            'designations' => Designation::orderBy('name')->pluck('name', 'designation_id'),
            'departments' => Department::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'selectedFacultyId' => old('faculty_id', $member?->faculty_id ?? ''),
            'selectedDepartmentId' => old('department_id', $member?->department_id ?? ''),
        ];
    }

    /**
     * Next sort_order for a new member in the given faculty and department.
     */
    private function nextSortOrder(int $facultyId, int $departmentId): int
    {
        $max = FacultyMember::facultyType()
            ->where('faculty_id', $facultyId)
            ->where('department_id', $departmentId)
            ->max('sort_order');

        return ($max ?? -1) + 1;
    }

    /**
     * Validate faculty member input with department scoping.
     */
    private function validateMember(Request $request, ?FacultyMember $member = null): array
    {
        $user = auth()->user();

        $departmentRule = ['required', 'exists:departments,id'];

        // Non-super admins can only assign members to their own department
        if (! $user->isSuperAdmin()) {
            $departmentRule[] = Rule::in([$user->department_id]);
        }

        $validated = $request->validate([
            'department_id' => array_merge($departmentRule, [
                Rule::exists('departments', 'id')->where('faculty_id', $request->input('faculty_id')),
            ]),
            'faculty_id' => ['required', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
            'designation_id' => ['required', 'exists:designations,designation_id'],
            'additional_designation_id' => ['nullable', 'exists:designations,designation_id'],
            'additional_department_id' => ['nullable', 'exists:departments,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'qualification' => ['required', 'string', new NotEmptyHtml],
            'specialization' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'research_link' => ['nullable', 'string'],
            'research_group' => ['nullable', 'string'],
            'affiliation' => ['nullable', 'string'],
            'projects_ongoing' => ['nullable', 'integer', 'min:0'],
            'projects_completed' => ['nullable', 'integer', 'min:0'],
            'supervision_phd' => ['nullable', 'integer', 'min:0'],
            'supervision_mphil_ms_msc' => ['nullable', 'integer', 'min:0'],
            'patent' => ['nullable', 'string'],
            'consultancy_services' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'total_experience' => ['nullable', 'integer', 'min:0', 'max:99'],
            'total_publication' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_hec'] = $request->boolean('is_hec');
        $validated['is_studyleave'] = $request->boolean('is_studyleave');
        $validated['is_onleave'] = $request->boolean('is_onleave');
        $validated['is_active'] = $request->boolean('is_active');

        $validated['qualification'] = clean($validated['qualification'], 'news');

        return $validated;
    }
}
