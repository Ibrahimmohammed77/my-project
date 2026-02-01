<?php

use Illuminate\Support\Facades\Route;

// Studio Owner Routes
Route::middleware('can:is-studio-owner')->as('studio.')->prefix('studio')->group(function () {
    Route::resource('albums', \App\Http\Controllers\Studio\AlbumController::class);
    Route::resource('customers', \App\Http\Controllers\Studio\CustomerController::class);

    // Cards Management
    Route::resource('cards', \App\Http\Controllers\Studio\CardController::class)
        ->parameters(['cards' => 'card:card_id']);
    Route::post('cards/{card:card_id}/link-albums', [\App\Http\Controllers\Studio\CardController::class, 'linkAlbums'])
        ->name('cards.link-albums');
    Route::post('cards/{card:card_id}/assign-to-library', [\App\Http\Controllers\Studio\CardController::class, 'assignToLibrary'])
        ->name('cards.assign-to-library');

    // Studio Profile
    Route::get('profile', [\App\Http\Controllers\Studio\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\Studio\ProfileController::class, 'update'])->name('profile.update');

    // Storage Management
    Route::prefix('storage')->as('storage.')->group(function () {
        Route::get('libraries', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'index'])->name('index');
        Route::post('libraries', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'store'])->name('store');
        Route::put('libraries/{library}', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'update'])->name('update');
        Route::delete('libraries/{library}', [\App\Http\Controllers\Studio\StorageLibraryController::class, 'destroy'])->name('destroy');
    });

    // Photo Review
    Route::prefix('photo-review')->as('photo-review.')->group(function () {
        Route::get('pending', [\App\Http\Controllers\Studio\PhotoReviewController::class, 'pending'])->name('pending');
        Route::post('{photo}/review', [\App\Http\Controllers\Studio\PhotoReviewController::class, 'review'])->name('review');
    });
});
