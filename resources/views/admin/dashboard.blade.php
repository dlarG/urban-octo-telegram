@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-semibold">Hi, {{ auth()->user()->name }} 👋</h1>
    <p class="text-gray-600 mt-1">Welcome track all activities in your end.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        {{-- Stat tiles get wired in Phase 9 --}}
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Properties</div>
            <div class="text-2xl font-semibold">—</div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Tenants</div>
            <div class="text-2xl font-semibold">—</div>
        </div>
        <div class="bg-white rounded-lg border p-4">
            <div class="text-sm text-gray-500">Net Income</div>
            <div class="text-2xl font-semibold">—</div>
        </div>
    </div>
@endsection