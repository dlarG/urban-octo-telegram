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
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, TrustScoreService $trust): RedirectResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'        => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'     => ['required', 'confirmed', Password::defaults()],
            'role'         => ['required', 'in:renter,landlord'],
            'renter_type'  => ['required_if:role,renter', 'nullable', 'in:student,worker,tourist,other'],
            'terms'        => ['accepted'],
        ]);

        $user = DB::transaction(function () use ($data, $trust, $request) {
            $user = User::create([
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'],
                'password'        => Hash::make($data['password']),
                'role'            => UserRole::from($data['role']),
                'is_active'       => true,
                'registration_ip' => $request->ip(),
            ]);

            if ($user->role === UserRole::Renter) {
                RenterProfile::create([
                    'user_id'     => $user->id,
                    'renter_type' => RenterType::from($data['renter_type']),
                ]);
            } else {
                // Landlord profile requires documents — created in onboarding step,
                // not here. But we still create the trust score row (rule: every user
                // gets a ledger row; whether it's ever used is another question).
                // Note: landlords won't accumulate trust events in v1, but the
                // invariant is preserved to avoid "missing row = integrity alarm."
                LandlordProfile::create([
                    'user_id'         => $user->id,
                    'approval_status' => \App\Enums\LandlordApprovalStatus::Pending,
                ]);
            }

            // 🔒 INVARIANT: trust score row exists the instant the user does.
            $trust->initializeFor($user);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        // Route to correct onboarding step
        return match ($user->role) {
            UserRole::Landlord => redirect()->route('landlord.onboarding'),
            UserRole::Renter   => redirect()->route('renter.dashboard'),
            default            => redirect()->route('dashboard'),
        };
    }
}