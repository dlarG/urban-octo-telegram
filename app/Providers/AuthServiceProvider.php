<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\BoardingHouse::class      => \App\Policies\BoardingHousePolicy::class,
        \App\Models\Room::class               => \App\Policies\RoomPolicy::class,
        \App\Models\RentalApplication::class  => \App\Policies\RentalApplicationPolicy::class,
        \App\Models\Tenancy::class            => \App\Policies\TenancyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
