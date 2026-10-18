<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PhoneOtpService;
use App\Services\Auth\RegistrationService;
use App\Support\CurriculumOptions;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StudentRegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register-student');
    }

    public function store(Request $request, RegistrationService $registration, PhoneOtpService $otp): RedirectResponse
    {
        $request->merge(['phone' => Phone::clean($request->input('phone')), 'guardian_phone' => Phone::clean($request->input('guardian_phone'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:'.Phone::GHANA_MOBILE, Rule::unique('users', 'phone')->whereNotNull('phone_verified_at')],
            'email' => ['nullable', 'email:rfc', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'class_level_id' => ['required', 'integer', Rule::in(CurriculumOptions::classLevels()->modelKeys())],
            // The programme must belong to the same education level as the chosen class.
            'programme_id' => ['nullable', 'integer', Rule::in(CurriculumOptions::programmes()
                ->where('education_level_id', CurriculumOptions::classLevels()->find($request->integer('class_level_id'))?->education_level_id)
                ->modelKeys())],
            'school_name' => ['nullable', 'string', 'max:150'],
            'region' => ['nullable', Rule::in(config('platform.regions'))],
            'is_adult' => ['nullable', 'boolean'],
            'guardian_name' => ['nullable', 'string', 'max:120'],
            'guardian_phone' => ['nullable', 'regex:'.Phone::GHANA_MOBILE, 'different:phone'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => 'Enter a Ghana mobile number, e.g. 024 123 4567.',
            'phone.unique' => 'An account already uses this number. Sign in or reset your password.',
            'guardian_phone.regex' => "Enter the guardian's Ghana mobile number, e.g. 024 123 4567.",
            'class_level_id.in' => 'Choose your class from the list.',
            'programme_id.in' => 'Choose a programme offered for your class.',
            'terms.accepted' => 'Please accept the terms and privacy policy to continue.',
        ]);

        $user = $registration->registerStudent($data);

        Auth::login($user);
        $request->session()->regenerate();

        $sent = $otp->send($user->phone, PhoneOtpService::PURPOSE_VERIFY, $user->id);

        return redirect()->route('verify.phone')->with($sent['sent'] ? 'status' : 'error', $sent['sent']
            ? 'We sent a 6-digit code to '.Phone::mask($user->phone).'.'
            : 'We could not send the code just now. Tap "Send a new code" to try again.');
    }
}
