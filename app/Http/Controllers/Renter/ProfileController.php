<?php
namespace App\Http\Controllers\Renter;

use App\Enums\RenterType;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Services\StorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected StorageService $storage) {}

    public function show(Request $request): View
    {
        $user    = $request->user();
        $profile = $user->renterProfile;

        // Defensive: ensure the profile row exists (older accounts might not have one)
        if (! $profile) {
            $profile = $user->renterProfile()->create([
                'renter_type' => RenterType::Other,
            ]);
        }

        $campuses = Campus::orderBy('name')->get();

        return view('renter.profile.show', compact('user', 'profile', 'campuses'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user    = $request->user();
        $profile = $user->renterProfile;

        $data = $request->validate([
            // Users
            'name'             => ['required', 'string', 'max:150'],
            'phone'            => ['required', 'string', 'max:20'],
            'profile_photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Renter profile — common
            'renter_type'      => ['required', 'in:student,worker,tourist,other'],
            'budget_min'       => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'budget_max'       => ['nullable', 'numeric', 'min:0', 'max:999999', 'gte:budget_min'],

            // Conditional — student
            'campus_id'        => ['nullable', 'integer', 'exists:campuses,id'],
            'major'            => ['nullable', 'string', 'max:100'],
            'year_level'       => ['nullable', 'string', 'max:50'],

            // Conditional — worker
            'occupation'       => ['nullable', 'string', 'max:100'],
            'employer'         => ['nullable', 'string', 'max:150'],

            // Conditional — tourist
            'stay_duration'    => ['nullable', 'string', 'max:50'],

            // Valid ID — only allowed if not yet submitted
            'valid_id'         => [$profile->valid_id_path ? 'prohibited' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],

            'trust_score_consent' => ['boolean'],
        ]);

        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                $this->storage->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $this->storage->put(
                $request->file('profile_photo'),
                'users/'.$user->id.'/avatar'
            );
        }

        // Handle valid ID (only once)
        if (! $profile->valid_id_path && $request->hasFile('valid_id')) {
            $data['valid_id_path']         = $this->storage->put(
                $request->file('valid_id'),
                'renters/'.$user->id.'/valid-id'
            );
            $data['valid_id_submitted_at'] = now();
        }

        // Split between User and RenterProfile
        $userFields = ['name', 'phone', 'profile_photo_path'];
        foreach ($userFields as $f) {
            if (array_key_exists($f, $data)) {
                $user->{$f} = $data[$f];
            }
        }
        $user->save();

        unset($data['valid_id']); // not a DB column

        $profile->fill($data)->save();

        return redirect()
            ->route('renter.profile')
            ->with('status', $profile->valid_id_path
                ? 'Profile saved.'
                : 'Profile updated. Upload a valid ID to unlock applications.');
    }
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path) {
            $this->storage->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $this->storage->put(
            $request->file('profile_photo'),
            'users/'.$user->id.'/avatar'
        );
        $user->save();

        return redirect()->route('renter.profile')->with('status', 'Profile photo updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Password updated.');
    }
}