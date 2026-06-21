<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions for the admin panel.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $actions = ['view', 'create', 'edit', 'delete'];

        // Build module permissions (modules × 4 actions)
        $modulePermissions = [];
        foreach (array_keys(config('modules')) as $module) {
            foreach ($actions as $action) {
                $modulePermissions[] = "{$module}.{$action}";
            }
        }

        // User management permissions (super_admin only, not part of the 44)
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        $allPermissions = array_merge($modulePermissions, $userPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Remove old permission names from previous seed format
        Permission::whereNotIn('name', $allPermissions)->delete();

        // super_admin gets every permission (all modules + user management)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // dept_admin and staff: role only, no module permissions
        // Permissions are assigned directly to each user via the Permission Management UI
        $deptAdmin = Role::firstOrCreate(['name' => 'dept_admin']);
        $deptAdmin->syncPermissions([]);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([]);
    }
}
