<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * List all admin users.
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        // List all users except super_admin (their access is role-based)
        $users = User::with(['department', 'roles'])
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'super_admin'))
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::where('name', '!=', 'super_admin')->pluck('name', 'name');
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['dept_admin', 'staff'])],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'department_id' => $validated['department_id'],
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show user details.
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['department', 'roles']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit user form.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::where('name', '!=', 'super_admin')->pluck('name', 'name');
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['dept_admin', 'staff'])],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
