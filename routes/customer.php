<?php

use Illuminate\Support\Facades\Route;

// Customer Routes
Route::middleware('can:is-customer')->group(function () {
    // Photo Upload
    Route::post('customer/photos/upload', [\App\Http\Controllers\Customer\PhotoController::class, 'store'])
        ->name('customer.photos.store');

    // Future customer routes can be added here
    // Route::resource('my-albums', \App\Http\Controllers\Customer\AlbumController::class);
    // Route::resource('my-cards', \App\Http\Controllers\Customer\CardController::class);
});
