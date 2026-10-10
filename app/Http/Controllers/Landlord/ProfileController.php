<?php
namespace App\Http\Controllers\Landlord;

use App\Enums\LandlordApprovalStatus;
use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected StorageService $storage) {}

    public function show(Request $request): View
    {
        $profile = $request->user()->landlordProfile;
        return view('landlord.profile', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->landlordProfile;

        $data = $request->validate([
            'business_name'   => ['required', 'string', 'max:150'],
            'gcash_number'    => ['nullable', 'string', 'max:20'],
            'maya_number'     => ['nullable', 'string', 'max:20'],
            'valid_id'        => [$profile->valid_id_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_permit' => [$profile->business_permit_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('valid_id')) {
            $this->storage->delete($profile->valid_id_path);
            $data['valid_id_path'] = $this->storage->put($request->file('valid_id'), 'landlords/valid-ids');
        }

        if ($request->hasFile('business_permit')) {
            $this->storage->delete($profile->business_permit_path);
            $data['business_permit_path'] = $this->storage->put($request->file('business_permit'), 'landlords/business-permits');
        }

        // If previously rejected, resubmitting flips back to pending
        $extra = [];
        if ($profile->approval_status === LandlordApprovalStatus::Rejected) {
            $extra = [
                'approval_status'        => LandlordApprovalStatus::Pending,
                'rejection_reason'       => null,
                'rejected_at'            => null,
                'documents_submitted_at' => now(),
            ];
        }

        $profile->fill(array_merge($data, $extra))->save();

        return redirect()->route('landlord.profile')
            ->with('status', 'Profile updated.');
    }
}