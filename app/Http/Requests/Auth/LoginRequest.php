<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        // uncomment this code when device fingerprint feature need to implement
        // $register_device_key = session('register_device_key');

        // if($register_device_key == null){
        //    Auth::logout();
        //     throw ValidationException::withMessages([
        //         'email' => 'Invalid device token .',
        //     ]);
        // }

        // dd($register_device_key);
        RateLimiter::clear($this->throttleKey());
        $domainTenant = getDomainTenantId();

        $user = Auth::user();
        if ($domainTenant != $user->tenant_id) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Invalid tenant account.',
            ]);
        }

        $roleUsers = DB::table('role_user')
            ->where('user_id', $user->id)
            ->get()->pluck('role_id')->toArray();
        if ($roleUsers) {
            getRolesPermissions($roleUsers, $user->id);
        } else {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => "Access denied you don't have permission.",
            ]);
        }

    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
