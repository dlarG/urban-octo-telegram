@extends('layouts.dashboard')

@section('sidebar')
    <x-side-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('admin.landlords')" :active="request()->routeIs('admin.landlords')">
        Landlords
    </x-side-link>
    <x-side-link :href="route('admin.properties')" :active="request()->routeIs('admin.properties')">
        Properties
    </x-side-link>
    <x-side-link :href="route('admin.renters')" :active="request()->routeIs('admin.renters')">
        Renters
    </x-side-link>
    <x-side-link :href="route('admin.disputes')" :active="request()->routeIs('admin.disputes')">
        Disputes
    </x-side-link>
@endsection