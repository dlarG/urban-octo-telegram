<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPropertyController;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/properties', [PublicPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{boarding_house}', [PublicPropertyController::class, 'show'])->name('properties.show');

require __DIR__.'/auth.php';

// Renter
Route::middleware(['auth', 'active', 'role.renter'])
    ->prefix('renter')->name('renter.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('renter.dashboard'))->name('dashboard');
        Route::get('/search', [\App\Http\Controllers\Renter\SearchController::class, 'index'])->name('search');
        Route::get('/profile',   fn() => view('renter.profile'))->name('profile');
        Route::get('/trust',     fn() => view('renter.trust'))->name('trust');
        
        Route::get('/applications', [\App\Http\Controllers\Renter\ApplicationController::class, 'index'])->name('applications');
        Route::post('/rooms/{room}/apply', [\App\Http\Controllers\Renter\ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('/applications/{application}', [\App\Http\Controllers\Renter\ApplicationController::class, 'withdraw'])->name('applications.withdraw');
        Route::get('/trust', [\App\Http\Controllers\Renter\TrustScoreController::class, 'show'])->name('trust');
        Route::post('/trust/events/{event}/dispute',
            [\App\Http\Controllers\Renter\DisputeController::class, 'store'])
            ->name('trust.dispute');
        Route::get('/properties/{boarding_house}',
            [\App\Http\Controllers\Renter\SearchController::class, 'show'])
            ->name('properties.show');

        Route::get('/favorites', [\App\Http\Controllers\Renter\FavoriteController::class, 'index'])->name('favorites');
        Route::post('/properties/{boarding_house}/favorite',
            [\App\Http\Controllers\Renter\FavoriteController::class, 'toggle'])
            ->name('favorites.toggle');
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

            //Applications
            Route::get('/applications',
                [\App\Http\Controllers\Landlord\ApplicationController::class, 'index'])
                ->name('applications');
            Route::get('/applications/{application}',
                [\App\Http\Controllers\Landlord\ApplicationController::class, 'show'])
                ->name('applications.show');
            Route::post('/applications/{application}/accept',
                [\App\Http\Controllers\Landlord\ApplicationController::class, 'accept'])
                ->name('applications.accept');
            Route::post('/applications/{application}/reject',
                [\App\Http\Controllers\Landlord\ApplicationController::class, 'reject'])
                ->name('applications.reject');

            // Tenancies
            Route::get('/tenancies',
                [\App\Http\Controllers\Landlord\TenancyController::class, 'index'])
                ->name('tenancies.index');
            Route::get('/tenancies/{tenancy}',
                [\App\Http\Controllers\Landlord\TenancyController::class, 'show'])
                ->name('tenancies.show');
            Route::post('/tenancies/{tenancy}/end',
                [\App\Http\Controllers\Landlord\TenancyController::class, 'end'])
                ->name('tenancies.end');
            
            Route::post('/tenancies/{tenancy}/payments',
                [\App\Http\Controllers\Landlord\TenancyController::class, 'recordPayment'])
                ->name('tenancies.payments.store');
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