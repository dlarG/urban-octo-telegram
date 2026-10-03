@extends('layouts.auth', ['title' => 'Log in'])

@section('content')
    <h2 class="text-2xl font-semibold text-gray-900">Welcome back</h2>
    <p class="text-sm text-gray-500 mt-1">Log in to your RentStreet account.</p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" required autofocus
                   value="{{ old('email') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                          focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                          focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full inline-flex justify-center rounded-md bg-indigo-600
                       px-4 py-2.5 text-sm font-medium text-white shadow-sm
                       hover:bg-indigo-700 focus:outline-none focus:ring-2
                       focus:ring-indigo-500 focus:ring-offset-2">
            Log in
        </button>
    </form>

    <p class="mt-6 text-sm text-gray-600 text-center">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">
            Create one
        </a>
    </p>
@endsection