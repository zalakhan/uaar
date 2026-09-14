<?php

namespace App\Providers;

use App\Models\CampusPublication;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Faculty;
use App\Models\FacultyMember;
use App\Models\Gallery;
use App\Models\Job;
use App\Models\News;
use App\Models\StaffMember;
use App\Models\Tender;
use App\Models\User;
use App\Policies\CampusPublicationPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DesignationPolicy;
use App\Policies\FacultyMemberPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\GalleryPolicy;
use App\Policies\JobPolicy;
use App\Policies\NewsPolicy;
use App\Policies\StaffMemberPolicy;
use App\Policies\TenderPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        Gate::policy(Designation::class, DesignationPolicy::class);
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(FacultyMember::class, FacultyMemberPolicy::class);
        Gate::policy(StaffMember::class, StaffMemberPolicy::class);
        Gate::policy(News::class, NewsPolicy::class);
        Gate::policy(Gallery::class, GalleryPolicy::class);
        Gate::policy(Tender::class, TenderPolicy::class);
        Gate::policy(CampusPublication::class, CampusPublicationPolicy::class);
        Gate::policy(Job::class, JobPolicy::class);
        Paginator::useBootstrapFive();

        // Ensure generated asset URLs match the configured application URL (needed for XAMPP subdirectories).
        if ($appUrl = config('app.url')) {
            URL::forceRootUrl($appUrl);
        }

        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}