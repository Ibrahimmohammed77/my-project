<?php

use Illuminate\Support\Facades\Route;

// School Owner Routes
Route::middleware('can:is-school-owner')->as('school.')->prefix('school')->group(function () {
    Route::resource('albums', \App\Http\Controllers\School\AlbumController::class);

    // Cards Management
    Route::resource('cards', \App\Http\Controllers\School\CardController::class)
        ->parameters(['cards' => 'card:card_id']);
    Route::post('cards/{card:card_id}/link-albums', [\App\Http\Controllers\School\CardController::class, 'linkAlbums'])
        ->name('cards.link-albums');

    // Students Management
    Route::prefix('students')->as('students.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\StudentController::class, 'index'])->name('index');
        Route::get('/{student}', [\App\Http\Controllers\School\StudentController::class, 'show'])->name('show');
    });

    // School Profile
    Route::get('profile', [\App\Http\Controllers\School\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\School\ProfileController::class, 'update'])->name('profile.update');
});
