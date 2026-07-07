<?php

use App\Http\Controllers\Api\FacultyMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('faculty-members', [FacultyMemberController::class, 'index']);
    Route::get('faculty-members/{id}', [FacultyMemberController::class, 'show']);
});
