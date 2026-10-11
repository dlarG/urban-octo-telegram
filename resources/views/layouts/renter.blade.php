@extends('layouts.dashboard')

@section('sidebar')
    <x-side-link :href="route('renter.dashboard')" :active="request()->routeIs('renter.dashboard')" icon="grid">
        Dashboard
    </x-side-link>
    <x-side-link :href="route('renter.search')" :active="request()->routeIs('renter.search')" icon="search">
        Find a place
    </x-side-link>
    <x-side-link :href="route('renter.favorites')" :active="request()->routeIs('renter.favorites')" icon="heart">
        Favorites
    </x-side-link>
    <x-side-link :href="route('renter.applications')" :active="request()->routeIs('renter.applications')" icon="file">
        My applications
    </x-side-link>
    <x-side-link :href="route('renter.trust')" :active="request()->routeIs('renter.trust')" icon="shield">
        Trust score
    </x-side-link>
    <x-side-link :href="route('renter.profile')" :active="request()->routeIs('renter.profile')" icon="user-circle">
        Profile
    </x-side-link>
@endsection

{{-- Navbar: search --}}
@section('topbar')
    <form action="{{ route('renter.search') }}" method="GET" role="search" class="hidden max-w-md md:block">
        <label for="navSearch" class="sr-only">Search boarding houses</label>
        <div class="flex h-10 items-center gap-2 rounded-full border border-line bg-mist px-4 transition-colors focus-within:border-[#177E89] focus-within:bg-white">
            <svg viewBox="0 0 24 24" class="ico text-muted" style="width:1.1rem;height:1.1rem"><use href="#l-search"/></svg>
            <input id="navSearch" name="q" type="search" value="{{ request('q') }}"
                   placeholder="Search boarding houses in Sogod"
                   class="w-full border-0 bg-transparent p-0 text-sm text-[#12242A] placeholder:text-[#566A70] focus:outline-none focus:ring-0">
        </div>
    </form>
@endsection

{{-- Navbar: quick links --}}
@section('topbar_actions')
    <a href="{{ route('renter.search') }}" class="grid h-10 w-10 place-items-center rounded-full text-bay hover:bg-mist md:hidden" aria-label="Find a place">
        <svg viewBox="0 0 24 24" class="ico"><use href="#l-search"/></svg>
    </a>
    <a href="{{ route('renter.favorites') }}" class="grid h-10 w-10 place-items-center rounded-full text-bay hover:bg-mist" aria-label="Favorites">
        <svg viewBox="0 0 24 24" class="ico"><use href="#l-heart"/></svg>
    </a>
@endsection

@section('user_menu')
    <a href="{{ route('renter.profile') }}" role="menuitem" class="menu-item">
        <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-user-circle"/></svg>
        Profile
    </a>
    <a href="{{ route('renter.trust') }}" role="menuitem" class="menu-item">
        <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-shield"/></svg>
        Trust score
    </a>
    <a href="{{ route('renter.applications') }}" role="menuitem" class="menu-item">
        <svg viewBox="0 0 24 24" class="ico text-sea"><use href="#l-file"/></svg>
        My applications
    </a>
@endsection