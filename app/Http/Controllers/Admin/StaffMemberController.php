<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ProvidesDepartmentOptions;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\StaffMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffMemberController extends Controller
{
    use ProvidesDepartmentOptions;

    /**
     * List staff members (scoped to user's department when not super admin).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StaffMember::class);

        $members = StaffMember::with(['department', 'faculty', 'designation'])
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

        return view('admin.staff-members.index', compact('members'));
    }

    /**
     * Show create staff member form.
     */
    public function create(): View
    {
        $this->authorize('create', StaffMember::class);

        return view('admin.staff-members.create', $this->formData());
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', StaffMember::class);

        $validated = $this->validateMember($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('staff-members', 'public');
        }

        $validated['sort_order'] = $this->nextSortOrder(
            $validated['faculty_id'] ?? null,
            $validated['department_id']
        );

        StaffMember::create($validated);

        return redirect()
            ->route('admin.staff-members.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Show staff member details.
     */
    public function show(StaffMember $staffMember): View
    {
        $this->authorize('view', $staffMember);

        $staffMember->load(['department', 'faculty', 'designation', 'additionalDesignation', 'additionalDepartment']);

        return view('admin.staff-members.show', ['member' => $staffMember]);
    }

    /**
     * Show edit staff member form.
     */
    public function edit(StaffMember $staffMember): View
    {
        $this->authorize('update', $staffMember);

        return view('admin.staff-members.edit', array_merge(
            ['member' => $staffMember],
            $this->formData($staffMember)
        ));
    }

    /**
     * Update an existing staff member.
     */
    public function update(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('update', $staffMember);

        $validated = $this->validateMember($request, $staffMember);

        if ($request->hasFile('photo')) {
            if ($staffMember->photo) {
                Storage::disk('public')->delete($staffMember->photo);
            }
            $validated['photo'] = $request->file('photo')->store('staff-members', 'public');
        }

        $staffMember->update($validated);

        return redirect()
            ->route('admin.staff-members.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete a staff member.
     */
    public function destroy(StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('delete', $staffMember);

        if ($staffMember->photo) {
            Storage::disk('public')->delete($staffMember->photo);
        }

        $staffMember->delete();

        return redirect()
            ->route('admin.staff-members.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Dropdown data for create/edit forms.
     */
    private function formData(?StaffMember $member = null): array
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
     * Validate staff member input with department scoping.
     */
    private function validateMember(Request $request, ?StaffMember $member = null): array
    {
        $user = auth()->user();

        $departmentRule = ['required', 'exists:departments,id'];

        if (! $user->isSuperAdmin()) {
            $departmentRule[] = Rule::in([$user->department_id]);
        }

        $validated = $request->validate([
            'department_id' => $departmentRule,
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'name' => ['required', 'string', 'max:255'],
            'designation_id' => ['nullable', 'exists:designations,designation_id'],
            'additional_designation_id' => ['nullable', 'exists:designations,designation_id'],
            'additional_department_id' => ['nullable', 'exists:departments,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'total_experience' => ['nullable', 'integer', 'min:0', 'max:99'],
            'total_publication' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_hec'] = $request->boolean('is_hec');
        $validated['is_studyleave'] = $request->boolean('is_studyleave');
        $validated['is_onleave'] = $request->boolean('is_onleave');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    /**
     * Next sort_order for a new member in the given faculty and department.
     */
    private function nextSortOrder(?int $facultyId, int $departmentId): int
    {
        $query = StaffMember::where('department_id', $departmentId);

        if ($facultyId) {
            $query->where('faculty_id', $facultyId);
        } else {
            $query->whereNull('faculty_id');
        }

        $max = $query->max('sort_order');

        return ($max ?? -1) + 1;
    }
}
