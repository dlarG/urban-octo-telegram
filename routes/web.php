<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

// Renter
Route::middleware(['auth', 'active', 'role.renter'])
    ->prefix('renter')->name('renter.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('renter.dashboard'))->name('dashboard');
        Route::get('/search',    fn() => view('renter.search'))->name('search');
        Route::get('/favorites', fn() => view('renter.favorites'))->name('favorites');
        Route::get('/applications', fn() => view('renter.applications'))->name('applications');
        Route::get('/profile',   fn() => view('renter.profile'))->name('profile');
        Route::get('/trust',     fn() => view('renter.trust'))->name('trust');
        
    });

// Landlord
Route::middleware(['auth', 'active', 'role.landlord'])
    ->prefix('landlord')->name('landlord.')
    ->group(function () {

        // Always allowed
        Route::get('/dashboard',    fn() => view('landlord.dashboard'))->name('dashboard');
        Route::get('/profile',   [\App\Http\Controllers\Landlord\ProfileController::class, 'show'])->name('profile');
        Route::put('/profile',   [\App\Http\Controllers\Landlord\ProfileController::class, 'update'])->name('profile.update');

        // Gated by onboarding
        Route::middleware('landlord.onboarded')->group(function () {
            Route::get('/applications', fn() => view('landlord.applications'))->name('applications');

            // Properties
            Route::resource('properties', \App\Http\Controllers\Landlord\BoardingHouseController::class)
                ->parameters(['properties' => 'boarding_house']);

            // Rooms (nested under a property)
            Route::resource('properties/{boarding_house}/rooms', \App\Http\Controllers\Landlord\RoomController::class)
                ->parameters(['rooms' => 'room'])
                ->names('properties.rooms');

            // Images (attached to house or room)
            Route::post('properties/{boarding_house}/images',
                [\App\Http\Controllers\Landlord\PropertyImageController::class, 'storeForHouse'])
                ->name('properties.images.store');

            Route::post('properties/{boarding_house}/rooms/{room}/images',
                [\App\Http\Controllers\Landlord\PropertyImageController::class, 'storeForRoom'])
                ->name('properties.rooms.images.store');

            Route::delete('images/{image}',
                [\App\Http\Controllers\Landlord\PropertyImageController::class, 'destroy'])
                ->name('images.destroy');

            Route::patch('images/{image}/primary',
                [\App\Http\Controllers\Landlord\PropertyImageController::class, 'makePrimary'])
                ->name('images.primary');
        });
    });

// Admin
Route::middleware(['auth', 'active', 'role.admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard',  fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('/landlords',  fn() => view('admin.landlords'))->name('landlords');
        Route::get('/properties', fn() => view('admin.properties'))->name('properties');
        Route::get('/renters',    fn() => view('admin.renters'))->name('renters');
        Route::get('/disputes',   fn() => view('admin.disputes'))->name('disputes');
    });