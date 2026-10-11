@auth
    @if (auth()->user()->isRenter() && ! auth()->user()->hasCompletedRenterProfile())
        <div class="mb-4 rounded-md bg-yellow-50 text-yellow-800 px-4 py-3 text-sm flex items-center gap-3">
            <svg viewBox="0 0 24 24" class="ico" style="width:1.1rem;height:1.1rem"><use href="#l-alert"/></svg>
            <div class="flex-1">
                <strong>Finish your profile</strong> to unlock applications. You need a valid ID on file.
            </div>
            <a href="{{ route('renter.profile') }}" class="underline font-medium whitespace-nowrap">Go to profile</a>
        </div>
    @endif
@endauth