<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use App\Services\Auth\PhoneOtpService;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Password reset by SMS code. The response is the same whether or not the number has an
 * account, so the form cannot be used to find out who is registered.
 */
class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request, PhoneOtpService $otp): RedirectResponse
    {
        $request->merge(['phone' => Phone::clean($request->input('phone'))]);
        $data = $request->validate(['phone' => ['required', 'regex:'.Phone::GHANA_MOBILE]], [
            'phone.regex' => 'Enter a Ghana mobile number, e.g. 024 123 4567.',
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if ($user && $user->isActive()) {
            $result = $otp->send($user->phone, PhoneOtpService::PURPOSE_RESET, $user->id);

            if (! $result['sent'] && $result['error'] === 'too_many') {
                throw ValidationException::withMessages(['phone' => 'Please wait '.$result['retry_after'].' seconds before asking for another code.']);
            }
        }

        $request->session()->put('password_reset_phone', $data['phone']);

        return redirect()->route('password.reset')->with('status', 'If '.Phone::mask($data['phone']).' has an account, a reset code has been sent to it.');
    }

    public function edit(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('password_reset_phone')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['maskedPhone' => Phone::mask($request->session()->get('password_reset_phone'))]);
    }

    public function update(Request $request, PhoneOtpService $otp): RedirectResponse
    {
        $phone = $request->session()->get('password_reset_phone');
        if (! $phone) {
            return redirect()->route('password.request');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:10'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::where('phone', $phone)->first();

        if (! $user || ! $otp->verify($phone, PhoneOtpService::PURPOSE_RESET, $data['code'])) {
            throw ValidationException::withMessages(['code' => 'That code is wrong or has expired. Request a new one.']);
        }

        DB::transaction(function () use ($user, $data) {
            $user->forceFill([
                'password' => $data['password'],
                'remember_token' => Str::random(60),
                'must_change_password' => false,
                // Receiving the code proves the number belongs to them.
                'phone_verified_at' => $user->phone_verified_at ?? now(),
            ])->save();

            // Sign the account out everywhere else.
            DB::table('sessions')->where('user_id', $user->id)->delete();

            Audit::record('security', 'password.reset', "Password reset by SMS code: {$user->name}", $user);
        });

        $request->session()->forget('password_reset_phone');

        return redirect()->route('login')->with('status', 'Your password has been changed. Please sign in.');
    }
}
