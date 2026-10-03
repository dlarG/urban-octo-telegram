<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(protected StorageService $storage) {}

    public function show(Request $request): View
    {
        $profile = $request->user()->landlordProfile;

        return view('landlord.onboarding', compact('profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user    = $request->user();
        $profile = $user->landlordProfile;

        $data = $request->validate([
            'business_name'    => ['required', 'string', 'max:150'],
            'gcash_number'     => ['nullable', 'string', 'max:20'],
            'maya_number'      => ['nullable', 'string', 'max:20'],
            'valid_id'         => [$profile->valid_id_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_permit'  => [$profile->business_permit_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        // Replace files if uploaded
        if ($request->hasFile('valid_id')) {
            $this->storage->delete($profile->valid_id_path);
            $profile->valid_id_path = $this->storage->put($request->file('valid_id'), 'landlord-ids');
        }

        if ($request->hasFile('business_permit')) {
            $this->storage->delete($profile->business_permit_path);
            $profile->business_permit_path = $this->storage->put($request->file('business_permit'), 'landlord-permits');
        }

        $profile->business_name         = $data['business_name'];
        $profile->gcash_number          = $data['gcash_number'] ?? null;
        $profile->maya_number           = $data['maya_number'] ?? null;
        $profile->onboarding_completed_at = now();
        $profile->save();

        return redirect()->route('landlord.dashboard')
            ->with('status', 'Profile submitted. An admin will review your documents shortly.');
    }
}