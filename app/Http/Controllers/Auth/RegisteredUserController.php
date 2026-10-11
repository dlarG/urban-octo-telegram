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
        // Normalize phone-like fields
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

            // 👇 Required for BOTH roles now
            'valid_id'        => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_permit' => [Rule::requiredIf($isLandlord), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'gcash_number.required' => 'Add a GCash or Maya number.',
            'gcash_number.regex'    => 'Use a mobile number like 09xxxxxxxxx.',
            'maya_number.regex'     => 'Use a mobile number like 09xxxxxxxxx.',
            'valid_id.required'     => 'Please upload your valid ID.',
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
                    'registration_ip' => $request->ip(),
                ]);

                // Hash the ID once, regardless of role
                $hash = hash_file('sha256', $request->file('valid_id')->getRealPath());

                // Dedupe: if this exact file already exists on another account, reject
                $collision = RenterProfile::where('valid_id_hash', $hash)->exists();
                if ($collision) {
                    throw new \DomainException('This ID is already registered on another account. Contact support if this is a mistake.');
                }

                if ($isLandlord) {
                    $validId = $request->file('valid_id')->store('landlords/valid-ids', self::DOCUMENT_DISK);
                    $storedFiles[] = $validId;

                    $permit = $request->file('business_permit')->store('landlords/business-permits', self::DOCUMENT_DISK);
                    $storedFiles[] = $permit;

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
                    $validId = $request->file('valid_id')->store('renters/valid-ids', self::DOCUMENT_DISK);
                    $storedFiles[] = $validId;

                    RenterProfile::create([
                        'user_id'               => $user->id,
                        'renter_type'           => RenterType::from($data['renter_type']),
                        'valid_id_path'         => $validId,
                        'valid_id_hash'         => $hash,
                        'valid_id_submitted_at' => now(),
                    ]);
                }

                $trust->initializeFor($user);

                return $user;
            });
        } catch (\DomainException $e) {
            Storage::disk(self::DOCUMENT_DISK)->delete($storedFiles);
            return back()
                ->withInput()
                ->withErrors(['valid_id' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Storage::disk(self::DOCUMENT_DISK)->delete($storedFiles);
            throw $e;
        }

        event(new Registered($user));
        Auth::login($user);

        return match ($user->role) {
            UserRole::Landlord => redirect()->route('landlord.dashboard'),
            UserRole::Renter   => redirect()->route('renter.dashboard'),
            default            => redirect()->route('dashboard'),
        };
    }
}