<?php

use App\Http\Controllers\Api\FacultyMemberController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\StaffMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('faculty-members', [FacultyMemberController::class, 'index']);
    Route::get('faculty-members/{id}', [FacultyMemberController::class, 'show']);

    Route::get('staff-members', [StaffMemberController::class, 'index']);
    Route::get('staff-members/{id}', [StaffMemberController::class, 'show']);

    Route::get('news', [NewsController::class, 'index']);
    Route::get('news/{id}', [NewsController::class, 'show']);

    Route::get('galleries', [GalleryController::class, 'index']);
    Route::get('galleries/{id}', [GalleryController::class, 'show']);
});
