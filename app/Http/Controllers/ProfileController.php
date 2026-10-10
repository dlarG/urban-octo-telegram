<?php
namespace App\Http\Controllers\Landlord;

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
        $submitted = $profile->hasSubmittedDocuments();

        $rules = [
            'business_name' => ['required', 'string', 'max:150'],
            'gcash_number'  => ['nullable', 'string', 'max:20'],
            'maya_number'   => ['nullable', 'string', 'max:20'],
        ];

        if (! $submitted) {
            $rules['valid_id']        = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['business_permit'] = ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        } else {
            // Block any attempt to re-upload after submission
            $rules['valid_id']        = ['prohibited'];
            $rules['business_permit'] = ['prohibited'];
        }

        $data = $request->validate($rules);

        if (! $submitted) {
            $data['valid_id_path']         = $this->storage->put($request->file('valid_id'), 'landlords/valid-ids');
            $data['business_permit_path']  = $this->storage->put($request->file('business_permit'), 'landlords/business-permits');
            $data['documents_submitted_at'] = now();
        }

        unset($data['valid_id'], $data['business_permit']);

        $profile->fill($data)->save();

        return redirect()->route('landlord.profile')
            ->with('status', $submitted ? 'Profile updated.' : 'Documents submitted. Under review.');
    }
}