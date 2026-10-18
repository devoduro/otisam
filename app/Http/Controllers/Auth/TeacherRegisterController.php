<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:'.Phone::GHANA_MOBILE, Rule::unique('users', 'phone')->whereNotNull('phone_verified_at')],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'staff_number' => ['nullable', 'string', 'max:30', Rule::unique('teacher_profiles', 'staff_number')],
            'current_rank' => ['nullable', 'string', 'max:80'],
            'school_name' => ['required', 'string', 'max:150'],
            'region' => ['required', Rule::in(config('platform.regions'))],
            'district' => ['nullable', 'string', 'max:80'],
            'subjects_taught' => ['required', 'string', 'max:255'],
            'declaration' => ['accepted'],
        ], [
            'phone.regex' => 'Enter a Ghana mobile number, e.g. 024 123 4567.',
            'phone.unique' => 'An account already uses this number. Sign in or reset your password.',
            'declaration.accepted' => 'Please confirm that the information you gave is accurate.',
        ]);

        $user = $registration->registerTeacher($data);

        Auth::login($user);
        $request->session()->regenerate();

        $sent = $otp->send($user->phone, PhoneOtpService::PURPOSE_VERIFY, $user->id);

        return redirect()->route('verify.phone')->with($sent['sent'] ? 'status' : 'error', $sent['sent']
            ? 'We sent a 6-digit code to '.Phone::mask($user->phone).'.'
            : 'We could not send the code just now. Tap "Send a new code" to try again.');
    }
}
