@extends('layouts.landlord')

@section('content')
    <div class="max-w-2xl">
        <h1 class="text-2xl font-semibold">Landlord profile</h1>

        @php
            use App\Enums\LandlordApprovalStatus;
            $status    = $profile->approval_status;
            $submitted = $profile->hasSubmittedDocuments();
        @endphp

        @if (session('status'))
            <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        @if ($status === LandlordApprovalStatus::Pending && $submitted)
            <div class="mt-4 rounded-md bg-blue-50 text-blue-800 px-4 py-3 text-sm">
                Documents under review.
            </div>
        @elseif ($status === LandlordApprovalStatus::Accepted)
            <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">
                Verified. You can list properties.
            </div>
        @elseif ($status === LandlordApprovalStatus::Rejected)
            <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
                <div><strong>Rejected:</strong> {{ $profile->rejection_reason }}</div>
                <div class="mt-1">Contact support to re-submit documents.</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 text-red-800 px-4 py-3 text-sm">
                <ul class="space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.profile.update') }}"
              enctype="multipart/form-data" class="mt-6 bg-white rounded-lg border p-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Business name</label>
                <input name="business_name" type="text" required
                       value="{{ old('business_name', $profile->business_name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">GCash number</label>
                    <input name="gcash_number" type="text"
                           value="{{ old('gcash_number', $profile->gcash_number) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maya number</label>
                    <input name="maya_number" type="text"
                           value="{{ old('maya_number', $profile->maya_number) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Valid ID --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Valid ID @if (! $submitted) <span class="text-red-500">*</span> @endif
                </label>

                @if ($submitted)
                    <div class="mt-1 flex items-center gap-3 rounded-md border bg-gray-50 px-3 py-2 text-sm">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">Uploaded and locked</span>
                        <a href="{{ Storage::url($profile->valid_id_path) }}" target="_blank"
                           class="ml-auto text-indigo-600 hover:underline text-sm">View</a>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        For security, documents can't be changed after submission.
                    </p>
                @else
                    <input name="valid_id" type="file" required
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-1 block w-full text-sm">
                    <p class="text-xs text-gray-500 mt-1">
                        You'll only be able to upload this once. Make sure it's clear and readable.
                    </p>
                @endif
            </div>

            {{-- Business permit --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Business permit @if (! $submitted) <span class="text-red-500">*</span> @endif
                </label>

                @if ($submitted)
                    <div class="mt-1 flex items-center gap-3 rounded-md border bg-gray-50 px-3 py-2 text-sm">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">Uploaded and locked</span>
                        <a href="{{ Storage::url($profile->business_permit_path) }}" target="_blank"
                           class="ml-auto text-indigo-600 hover:underline text-sm">View</a>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        For security, documents can't be changed after submission.
                    </p>
                @else
                    <input name="business_permit" type="file" required
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-1 block w-full text-sm">
                    <p class="text-xs text-gray-500 mt-1">
                        You'll only be able to upload this once. Make sure it's clear and readable.
                    </p>
                @endif
            </div>

            <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                {{ $submitted ? 'Save changes' : 'Submit documents' }}
            </button>
        </form>
    </div>
@endsection