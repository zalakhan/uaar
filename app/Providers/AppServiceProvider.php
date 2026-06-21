<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\FacultyMember;
use App\Models\StaffMember;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\FacultyMemberPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\StaffMemberPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(FacultyMember::class, FacultyMemberPolicy::class);
        Gate::policy(StaffMember::class, StaffMemberPolicy::class);
        Paginator::useBootstrapFive();
    }
}
