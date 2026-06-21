@push('scripts')
<script>
(function () {
    const departmentsByFaculty = @json($departmentsByFaculty ?? []);
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department_id');

    if (!facultySelect || !departmentSelect) {
        return;
    }

    const selectedFacultyId = @json((string) ($selectedFacultyId ?? ''));
    const selectedDepartmentId = @json((string) ($selectedDepartmentId ?? ''));
    const facultyRequired = facultySelect.hasAttribute('required');

    function populateDepartments(facultyId, keepDepartmentId) {
        departmentSelect.innerHTML = '<option value="">Select department</option>';

        const departments = departmentsByFaculty[facultyId] || departmentsByFaculty[String(facultyId)];

        if (!facultyId || !departments || departments.length === 0) {
            departmentSelect.disabled = true;
            return;
        }

        departmentSelect.disabled = false;

        departments.forEach(function (department) {
            const option = document.createElement('option');
            option.value = department.id;
            option.textContent = department.name;

            if (String(department.id) === String(keepDepartmentId)) {
                option.selected = true;
            }

            departmentSelect.appendChild(option);
        });
    }

    facultySelect.addEventListener('change', function () {
        populateDepartments(facultySelect.value, '');
    });

    const initialFacultyId = selectedFacultyId || facultySelect.value;
    populateDepartments(initialFacultyId, selectedDepartmentId);

    if (!facultyRequired && !initialFacultyId) {
        departmentSelect.disabled = true;
    }
})();
</script>
@endpush
