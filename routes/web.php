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
        Route::get('/profile',      fn() => view('landlord.profile'))->name('profile');
        Route::get('/onboarding',   [\App\Http\Controllers\Landlord\OnboardingController::class, 'show'])->name('onboarding');
        Route::post('/onboarding',  [\App\Http\Controllers\Landlord\OnboardingController::class, 'store'])->name('onboarding.store');

        // Gated by onboarding
        Route::middleware('landlord.onboarded')->group(function () {
            Route::get('/applications', fn() => view('landlord.applications'))->name('applications');

            Route::resource('properties', \App\Http\Controllers\Landlord\BoardingHouseController::class)
                ->parameters(['properties' => 'boarding_house']);
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