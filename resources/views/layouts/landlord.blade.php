@extends('layouts.dashboard')

@section('sidebar')
    <x-side-link :href="route('landlord.dashboard')" :active="request()->routeIs('landlord.dashboard')">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('landlord.properties.index')" :active="request()->routeIs('landlord.properties.*')">
        My properties
    </x-side-link>
    <x-side-link :href="route('landlord.applications')" :active="request()->routeIs('landlord.applications')">
        Applications
    </x-side-link>
    <x-side-link :href="route('landlord.profile')" :active="request()->routeIs('landlord.profile')">
        Profile
    </x-side-link>
@endsection