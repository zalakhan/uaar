<?php

use App\Http\Controllers\Admin\CampusPublicationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\FacultyMemberAwardController;
use App\Http\Controllers\Admin\FacultyMemberBookController;
use App\Http\Controllers\Admin\FacultyMemberController;
use App\Http\Controllers\Admin\FacultyMemberOrderController;
use App\Http\Controllers\Admin\FacultyMemberPublicationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryPhotoController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\StaffMemberController;
use App\Http\Controllers\Admin\StaffMemberOrderController;
use App\Http\Controllers\Admin\TenderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserPermissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

    Route::get('faculty-members/{faculty_member}/books', [FacultyMemberBookController::class, 'index'])
        ->name('faculty-members.books.index');
    Route::post('faculty-members/{faculty_member}/books', [FacultyMemberBookController::class, 'store'])
        ->name('faculty-members.books.store');
    Route::put('faculty-members/{faculty_member}/books/{book}', [FacultyMemberBookController::class, 'update'])
        ->name('faculty-members.books.update');
    Route::delete('faculty-members/{faculty_member}/books/{book}', [FacultyMemberBookController::class, 'destroy'])
        ->name('faculty-members.books.destroy');

    Route::get('staff-members/manage-order', [StaffMemberOrderController::class, 'index'])
        ->name('staff-members.order.index');
    Route::put('staff-members/manage-order', [StaffMemberOrderController::class, 'update'])
        ->name('staff-members.order.update');

    Route::resource('staff-members', StaffMemberController::class);

    Route::resource('news', NewsController::class);

    Route::resource('galleries', GalleryController::class);
    Route::get('galleries/{gallery}/photos', [GalleryPhotoController::class, 'index'])
        ->name('galleries.photos.index');
    Route::post('galleries/{gallery}/photos', [GalleryPhotoController::class, 'store'])
        ->name('galleries.photos.store');
    Route::put('galleries/{gallery}/photos/order', [GalleryPhotoController::class, 'updateOrder'])
        ->name('galleries.photos.order.update');
    Route::delete('galleries/{gallery}/photos/{photo}', [GalleryPhotoController::class, 'destroy'])
        ->name('galleries.photos.destroy');

    Route::resource('tenders', TenderController::class);
    Route::resource('campus-publications', CampusPublicationController::class);
    Route::resource('jobs', JobController::class);

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
Route::get('/media/staff/{filename}', function ($filename) {
    $path = storage_path('app/public/staff-members/' . basename($filename));

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});
Route::get('/media/publications/{filename}', function (string $filename) {
    if (! preg_match('/^[A-Za-z0-9]{40}\.pdf$/', $filename)) {
        abort(404);
    }

    $path = Storage::disk('local')->path('publications/'.$filename);

    if (! is_file($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
    ]);
})->where('filename', '[A-Za-z0-9]{40}\.pdf');
  

require __DIR__.'/auth.php';
