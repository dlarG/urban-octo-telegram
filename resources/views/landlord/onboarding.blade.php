@extends('layouts.landlord')

@section('content')
    <div class="max-w-2xl">
        <h1 class="text-2xl font-semibold">Complete your landlord profile</h1>
        <p class="text-gray-600 mt-1">
            We need to verify your identity before you can list properties. This only takes a minute.
        </p>

        @if ($profile->onboarding_completed_at)
            <div class="mt-4 rounded-md bg-green-50 text-green-800 px-4 py-3 text-sm">
                Submitted on {{ $profile->onboarding_completed_at->format('M d, Y') }}.
                @if ($profile->approval_status === \App\Enums\LandlordApprovalStatus::Pending)
                    An admin is currently reviewing your documents.
                @elseif ($profile->approval_status === \App\Enums\LandlordApprovalStatus::Accepted)
                    Your account is <strong>verified</strong>. You can list properties.
                @elseif ($profile->approval_status === \App\Enums\LandlordApprovalStatus::Rejected)
                    Your submission was rejected.
                    @if ($profile->rejection_reason)
                        <div class="mt-1 italic">"{{ $profile->rejection_reason }}"</div>
                    @endif
                    You may update and resubmit.
                @endif
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

        <form method="POST" action="{{ route('landlord.onboarding.store') }}"
              enctype="multipart/form-data" class="mt-6 space-y-5 bg-white rounded-lg border p-5">
            @csrf

            <div>
                <label for="business_name" class="block text-sm font-medium text-gray-700">Business name</label>
                <input id="business_name" name="business_name" type="text" required
                       value="{{ old('business_name', $profile->business_name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="gcash_number" class="block text-sm font-medium text-gray-700">GCash number</label>
                    <input id="gcash_number" name="gcash_number" type="text"
                           value="{{ old('gcash_number', $profile->gcash_number) }}"
                           placeholder="09xxxxxxxxx"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="maya_number" class="block text-sm font-medium text-gray-700">Maya number</label>
                    <input id="maya_number" name="maya_number" type="text"
                           value="{{ old('maya_number', $profile->maya_number) }}"
                           placeholder="09xxxxxxxxx"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Valid ID --}}
            <div>
                <label for="valid_id" class="block text-sm font-medium text-gray-700">
                    Valid ID
                    @if (! $profile->valid_id_path) <span class="text-red-500">*</span> @endif
                </label>
                <input id="valid_id" name="valid_id" type="file"
                       accept="image/*,application/pdf"
                       class="mt-1 block w-full text-sm">
                @if ($profile->valid_id_path)
                    <div class="mt-2 text-xs text-gray-500">
                        Current file uploaded. Upload a new one to replace it.
                    </div>
                @endif
            </div>

            {{-- Business permit --}}
            <div>
                <label for="business_permit" class="block text-sm font-medium text-gray-700">
                    Business permit
                    @if (! $profile->business_permit_path) <span class="text-red-500">*</span> @endif
                </label>
                <input id="business_permit" name="business_permit" type="file"
                       accept="image/*,application/pdf"
                       class="mt-1 block w-full text-sm">
                @if ($profile->business_permit_path)
                    <div class="mt-2 text-xs text-gray-500">
                        Current file uploaded. Upload a new one to replace it.
                    </div>
                @endif
            </div>

            <button type="submit"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600
                           px-4 py-2.5 text-sm font-medium text-white shadow-sm
                           hover:bg-indigo-700">
                {{ $profile->onboarding_completed_at ? 'Update submission' : 'Submit for review' }}
            </button>
        </form>
    </div>
@endsection