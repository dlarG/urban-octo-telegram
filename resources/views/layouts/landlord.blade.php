@extends('layouts.dashboard')

@section('sidebar')
    <x-side-link :href="route('landlord.dashboard')" :active="request()->routeIs('landlord.dashboard')" icon="grid">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('landlord.properties.index')" :active="request()->routeIs('landlord.properties.*')" icon="building">
        My properties
    </x-side-link>
    <x-side-link :href="route('landlord.applications')" :active="request()->routeIs('landlord.applications')" icon="inbox">
        Applications
    </x-side-link>
    <x-side-link :href="route('landlord.profile')" :active="request()->routeIs('landlord.profile')" icon="user-circle">
        Profile
    </x-side-link>
@endsection