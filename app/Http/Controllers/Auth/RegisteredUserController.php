<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LandlordApprovalStatus;
use App\Enums\RenterType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\LandlordProfile;
use App\Models\RenterProfile;
use App\Models\User;
use App\Services\TrustScoreService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /** Private disk: valid IDs and permits must never be publicly reachable. */
    private const DOCUMENT_DISK = 'local';

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, TrustScoreService $trust): RedirectResponse
    {
        // "0912 345 6789" / "0912-345-6789" -> "09123456789"
        foreach (['gcash_number', 'maya_number'] as $key) {
            if ($request->filled($key)) {
                $request->merge([$key => preg_replace('/[\s-]+/', '', $request->input($key))]);
            }
        }

        $isLandlord = $request->input('role') === 'landlord';
        $mobile     = 'regex:/^(09|\+639)\d{9}$/';

        $data = $request->validate([
            // Account (every role)
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => ['required', 'in:renter,landlord'],
            'terms'    => ['accepted'],

            // Renter path
            'renter_type' => ['required_if:role,renter', 'nullable', 'in:student,worker,tourist,other'],

            // Landlord path
            'business_name' => [Rule::requiredIf($isLandlord), 'nullable', 'string', 'max:255'],
            'gcash_number'  => [
                Rule::requiredIf($isLandlord && ! $request->filled('maya_number')),
                'nullable', $mobile,
            ],
            'maya_number'   => ['nullable', $mobile],
            'valid_id'        => [Rule::requiredIf($isLandlord), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_permit' => [Rule::requiredIf($isLandlord), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'gcash_number.required' => 'Add a GCash or Maya number.',
            'gcash_number.regex'    => 'Use a mobile number like 09xxxxxxxxx.',
            'maya_number.regex'     => 'Use a mobile number like 09xxxxxxxxx.',
        ]);

        $storedFiles = [];

        try {
            $user = DB::transaction(function () use ($data, $trust, $request, $isLandlord, &$storedFiles) {
                $user = User::create([
                    'name'            => $data['name'],
                    'email'           => $data['email'],
                    'phone'           => $data['phone'],
                    'password'        => Hash::make($data['password']),
                    'role'            => UserRole::from($data['role']),
                    'is_active'       => true,
                    'registration_ip' => $request->ip(), // review signal only, never auto-block
                ]);

                if ($isLandlord) {
                    $validId = $request->file('valid_id')
                        ->store('landlords/valid-ids', self::DOCUMENT_DISK);
                    $storedFiles[] = $validId;

                    $permit = $request->file('business_permit')
                        ->store('landlords/business-permits', self::DOCUMENT_DISK);
                    $storedFiles[] = $permit;

                    // NOTE: adjust these column names to match your landlord_profiles migration.
                    LandlordProfile::create([
                        'user_id'                => $user->id,
                        'business_name'          => $data['business_name'],
                        'gcash_number'           => $data['gcash_number'] ?? null,
                        'maya_number'            => $data['maya_number'] ?? null,
                        'valid_id_path'          => $validId,
                        'business_permit_path'   => $permit,
                        'approval_status'        => LandlordApprovalStatus::Pending,
                        'documents_submitted_at' => now(),
                    ]);
                } else {
                    RenterProfile::create([
                        'user_id'     => $user->id,
                        'renter_type' => RenterType::from($data['renter_type']),
                    ]);
                }

                // INVARIANT: trust score row exists the instant the user does.
                $trust->initializeFor($user);

                return $user;
            });
        } catch (\Throwable $e) {
            // Don't leave orphaned ID documents on disk if the transaction failed
            Storage::disk(self::DOCUMENT_DISK)->delete($storedFiles);
            throw $e;
        }

        event(new Registered($user));
        Auth::login($user);

        // Landlord documents are now collected at registration, so point this at your
        // "awaiting approval" screen rather than a document-upload onboarding step.
        return match ($user->role) {
            UserRole::Landlord => redirect()->route('landlord.dashboard'),
            UserRole::Renter   => redirect()->route('renter.dashboard'),
            default            => redirect()->route('dashboard'),
        };
    }
}