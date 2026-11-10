<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RegistrationFieldSeeder::class,
        ]);

        // Sample faculty and department for testing org structure
        $faculty = Faculty::firstOrCreate(
            ['slug' => 'sciences'],
            [
                'name' => 'Faculty of Sciences',
                'is_active' => true,
            ]
        );

        $department = Department::firstOrCreate(
            ['slug' => 'computer-science'],
            [
                'name' => 'Computer Science',
                'faculty_id' => $faculty->id,
                'is_active' => true,
            ]
        );

        // Web manager account (super_admin, no department)
        User::where('email', 'admin@uaar.edu.pk')
            ->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))
            ->update(['email' => 'webmaster@uaar.edu.pk']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'webmaster@uaar.edu.pk'],
            [
                'name' => 'Web Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
    }
}
