<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\ReferralController;
use App\Services\Auth\PhoneOtpService;
use App\Services\Auth\RegistrationService;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TeacherRegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register-teacher');
    }

    public function store(Request $request, RegistrationService $registration, PhoneOtpService $otp): RedirectResponse
    {
        $request->merge(['phone' => Phone::clean($request->input('phone'))]);

        // Quick sign-up: phone and password only (region/district optional). Name, school and
        // subjects are added on the teacher profile before an administrator verifies the account.
        $data = $request->validate([
            'phone' => ['required', 'regex:'.Phone::GHANA_MOBILE, Rule::unique('users', 'phone')->whereNotNull('phone_verified_at')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'region' => ['nullable', Rule::in(config('platform.regions'))],
            'district' => ['nullable', 'string', 'max:80'],
        ], [
            'phone.regex' => 'Enter a Ghana mobile number, e.g. 024 123 4567.',
            'phone.unique' => 'An account already uses this number. Sign in or reset your password.',
        ]);

        $user = $registration->registerTeacher($data);
        ReferralController::credit($request, $user);

        Auth::login($user);
        $request->session()->regenerate();

        $sent = $otp->send($user->phone, PhoneOtpService::PURPOSE_VERIFY, $user->id);

        return redirect()->route('verify.phone')->with($sent['sent'] ? 'status' : 'error', $sent['sent']
            ? 'We sent a 6-digit code to '.Phone::mask($user->phone).'.'
            : 'We could not send the code just now. Tap "Send a new code" to try again.');
    }
}
