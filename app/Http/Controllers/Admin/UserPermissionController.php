<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPermissionController extends Controller
{
    /**
     * Only super_admin can manage direct user permissions.
     */
    private function ensureSuperAdmin(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage user permissions.');
        }
    }

    /**
     * Show permission grid for a user.
     */
    public function edit(User $user): View
    {
        $this->ensureSuperAdmin();

        // super_admin permissions come from their role, not this page
        if ($user->isSuperAdmin()) {
            abort(403, 'Super admin permissions are managed by role and cannot be edited here.');
        }

        $user->load(['roles', 'department']);

        // Direct permissions only (not inherited from role)
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        $modules = config('modules');
        $actions = ['view', 'create', 'edit', 'delete'];

        return view('admin.users.permissions', compact('user', 'modules', 'actions', 'directPermissions'));
    }

    /**
     * Save direct permissions for a user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if ($user->isSuperAdmin()) {
            abort(403, 'Super admin permissions are managed by role and cannot be edited here.');
        }

        // Collect all valid permission names from config
        $validPermissions = [];
        foreach (array_keys(config('modules')) as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $validPermissions[] = "{$module}.{$action}";
            }
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', $validPermissions)],
        ]);

        // syncPermissions sets direct permissions on the user (not via role)
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Permissions updated for {$user->name}.");
    }
}
