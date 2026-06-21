<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ProvidesDepartmentOptions;
use App\Http\Controllers\Controller;
use App\Models\FacultyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacultyMemberOrderController extends Controller
{
    use ProvidesDepartmentOptions;

    /**
     * Show manage faculty order page with optional member list.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', FacultyMember::class);

        $faculties = $this->facultiesForSelect();
        $departmentsByFaculty = $this->departmentsByFacultyForJs(auth()->user());

        $selectedFacultyId = $request->input('faculty_id');
        $selectedDepartmentId = $request->input('department_id');
        $members = collect();

        if ($selectedFacultyId && $selectedDepartmentId) {
            $members = FacultyMember::facultyType()
                ->where('faculty_id', $selectedFacultyId)
                ->where('department_id', $selectedDepartmentId)
                ->with('department')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('admin.faculty-members.order', compact(
            'faculties',
            'departmentsByFaculty',
            'selectedFacultyId',
            'selectedDepartmentId',
            'members'
        ));
    }

    /**
     * Save drag-and-drop sort order for a faculty/department group.
     */
    public function update(Request $request): RedirectResponse
    {
        if (! auth()->user()->can('faculty_members.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'faculty_id' => ['required', 'exists:faculties,id'],
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($request) {
                    $belongs = \App\Models\Department::where('id', $value)
                        ->where('faculty_id', $request->input('faculty_id'))
                        ->exists();
                    if (! $belongs) {
                        $fail('The selected department does not belong to the selected faculty.');
                    }
                },
            ],
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['integer', 'exists:faculty_members,id'],
        ]);

        $memberIds = FacultyMember::facultyType()
            ->where('faculty_id', $validated['faculty_id'])
            ->where('department_id', $validated['department_id'])
            ->whereIn('id', $validated['order'])
            ->pluck('id');

        if ($memberIds->count() !== count($validated['order'])) {
            return back()->with('error', 'Invalid member selection for this faculty and department.');
        }

        foreach ($validated['order'] as $index => $memberId) {
            FacultyMember::where('id', $memberId)->update(['sort_order' => $index]);
        }

        return redirect()
            ->route('admin.faculty-members.order.index', [
                'faculty_id' => $validated['faculty_id'],
                'department_id' => $validated['department_id'],
            ])
            ->with('success', 'Faculty order saved successfully.');
    }
}
