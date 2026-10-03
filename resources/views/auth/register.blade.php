@extends('layouts.auth', ['title' => 'Sign up'])

@section('content')
    <h2 class="text-2xl font-semibold text-gray-900">Create your account</h2>
    <p class="text-sm text-gray-500 mt-1">Join RentStreet in under a minute.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4"
          x-data="{ role: '{{ old('role', 'renter') }}' }">
        @csrf

        {{-- Role selector --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">I am a…</label>
            <div class="grid grid-cols-2 gap-2">
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="renter" x-model="role"
                           class="peer sr-only">
                    <div class="rounded-md border px-4 py-3 text-center text-sm font-medium
                                peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                                peer-checked:text-indigo-700 hover:bg-gray-50">
                        Renter
                        <div class="text-xs text-gray-500 font-normal mt-0.5">Looking for a place</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="role" value="landlord" x-model="role"
                           class="peer sr-only">
                    <div class="rounded-md border px-4 py-3 text-center text-sm font-medium
                                peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                                peer-checked:text-indigo-700 hover:bg-gray-50">
                        Landlord
                        <div class="text-xs text-gray-500 font-normal mt-0.5">Listing a property</div>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
            <input id="name" name="name" type="text" required autofocus
                   value="{{ old('name') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                          focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required
                       value="{{ old('email') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input id="phone" name="phone" type="text" required
                       value="{{ old('phone') }}" placeholder="09xxxxxxxxx"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        {{-- Renter-only field --}}
        <div x-show="role === 'renter'" x-cloak>
            <label for="renter_type" class="block text-sm font-medium text-gray-700">I'm a…</label>
            <select id="renter_type" name="renter_type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500">
                <option value="student" @selected(old('renter_type') === 'student')>Student</option>
                <option value="worker"  @selected(old('renter_type') === 'worker')>Worker</option>
                <option value="tourist" @selected(old('renter_type') === 'tourist')>Tourist</option>
                <option value="other"   @selected(old('renter_type') === 'other')>Other</option>
            </select>
        </div>

        {{-- Landlord hint --}}
        <div x-show="role === 'landlord'" x-cloak
             class="rounded-md bg-indigo-50 text-indigo-800 px-4 py-3 text-sm">
            Next step: upload your valid ID and business permit. An admin will verify your account.
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirm password
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <label class="inline-flex items-start">
            <input type="checkbox" name="terms" value="1" required
                   class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-600">
                I agree to the
                <a href="#" class="text-indigo-600 hover:underline">Terms of Service</a>
                and
                <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>.
            </span>
        </label>

        <button type="submit"
                class="w-full inline-flex justify-center rounded-md bg-indigo-600
                       px-4 py-2.5 text-sm font-medium text-white shadow-sm
                       hover:bg-indigo-700 focus:outline-none focus:ring-2
                       focus:ring-indigo-500 focus:ring-offset-2">
            Create account
        </button>
    </form>

    <p class="mt-6 text-sm text-gray-600 text-center">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">
            Log in
        </a>
    </p>
@endsection