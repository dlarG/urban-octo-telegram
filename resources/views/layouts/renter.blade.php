@extends('layouts.dashboard')

@section('sidebar')
    <x-side-link :href="route('renter.dashboard')" :active="request()->routeIs('renter.dashboard')">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('renter.search')" :active="request()->routeIs('renter.search')">
        Find a place
    </x-side-link>
    <x-side-link :href="route('renter.favorites')" :active="request()->routeIs('renter.favorites')">
        Favorites
    </x-side-link>
    <x-side-link :href="route('renter.applications')" :active="request()->routeIs('renter.applications')">
        My applications
    </x-side-link>
    <x-side-link :href="route('renter.profile')" :active="request()->routeIs('renter.profile')">
        Profile
    </x-side-link>
    <x-side-link :href="route('renter.trust')" :active="request()->routeIs('renter.trust')">
        Trust score
    </x-side-link>
    <x-side-logout />
@endsection