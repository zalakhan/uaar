<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\FacultyMemberAwardController;
use App\Http\Controllers\Admin\FacultyMemberController;
use App\Http\Controllers\Admin\FacultyMemberOrderController;
use App\Http\Controllers\Admin\FacultyMemberPublicationController;
use App\Http\Controllers\Admin\StaffMemberController;
use App\Http\Controllers\Admin\StaffMemberOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserPermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('faculties', FacultyController::class);

    Route::get('faculty-members/manage-order', [FacultyMemberOrderController::class, 'index'])
        ->name('faculty-members.order.index');
    Route::put('faculty-members/manage-order', [FacultyMemberOrderController::class, 'update'])
        ->name('faculty-members.order.update');

    Route::resource('faculty-members', FacultyMemberController::class);

    Route::get('faculty-members/{faculty_member}/publications', [FacultyMemberPublicationController::class, 'index'])
        ->name('faculty-members.publications.index');
    Route::post('faculty-members/{faculty_member}/publications', [FacultyMemberPublicationController::class, 'store'])
        ->name('faculty-members.publications.store');
    Route::put('faculty-members/{faculty_member}/publications/{publication}', [FacultyMemberPublicationController::class, 'update'])
        ->name('faculty-members.publications.update');
    Route::delete('faculty-members/{faculty_member}/publications/{publication}', [FacultyMemberPublicationController::class, 'destroy'])
        ->name('faculty-members.publications.destroy');

    Route::get('faculty-members/{faculty_member}/awards', [FacultyMemberAwardController::class, 'index'])
        ->name('faculty-members.awards.index');
    Route::post('faculty-members/{faculty_member}/awards', [FacultyMemberAwardController::class, 'store'])
        ->name('faculty-members.awards.store');
    Route::put('faculty-members/{faculty_member}/awards/{award}', [FacultyMemberAwardController::class, 'update'])
        ->name('faculty-members.awards.update');
    Route::delete('faculty-members/{faculty_member}/awards/{award}', [FacultyMemberAwardController::class, 'destroy'])
        ->name('faculty-members.awards.destroy');

    Route::get('staff-members/manage-order', [StaffMemberOrderController::class, 'index'])
        ->name('staff-members.order.index');
    Route::put('staff-members/manage-order', [StaffMemberOrderController::class, 'update'])
        ->name('staff-members.order.update');

    Route::resource('staff-members', StaffMemberController::class);

    // Direct permission assignment per user (super_admin only)
    Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])
        ->name('users.permissions.edit');
    Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])
        ->name('users.permissions.update');

        
});
Route::get('/media/faculty/{filename}', function ($filename) {
    $path = storage_path('app/public/faculty-members/' . basename($filename));

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});    

require __DIR__.'/auth.php';
