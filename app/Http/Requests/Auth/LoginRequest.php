<?php
namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $this->input('email'))->first();

        // Hard lockout (DB-backed) — outranks Laravel's rate limiter
        if ($user && $user->isLockedOut()) {
            throw ValidationException::withMessages([
                'email' => 'Account locked until '.$user->locked_until->format('H:i').'. Try again later.',
            ]);
        }

        // Soft-deleted / deactivated user
        if ($user && ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Increment DB lockout counter
            if ($user) {
                $maxAttempts = config('rentstreet.login_max_attempts', 3);
                $attempts    = $user->failed_login_attempts + 1;

                $updates = ['failed_login_attempts' => $attempts];
                if ($attempts >= $maxAttempts) {
                    $updates['locked_until'] = now()->addMinutes(
                        config('rentstreet.login_lockout_minutes', 15)
                    );
                }
                $user->forceFill($updates)->save();
            }

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}