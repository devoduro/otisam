<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PhoneOtpService;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PhoneVerificationController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->phone_verified_at || $user->isStaff()) {
            return redirect()->route($user->homeRoute());
        }

        return view('auth.verify-phone', ['maskedPhone' => Phone::mask($user->phone)]);
    }

    public function resend(Request $request, PhoneOtpService $otp): RedirectResponse
    {
        $user = $request->user();
        $result = $otp->send($user->phone, PhoneOtpService::PURPOSE_VERIFY, $user->id);

        return back()->with($result['sent'] ? 'status' : 'error', match (true) {
            $result['sent'] => 'A new code is on its way to '.Phone::mask($user->phone).'.',
            $result['error'] === 'too_many' => 'Please wait '.$result['retry_after'].' seconds before asking for another code.',
            default => 'We could not send the code just now. Please try again shortly.',
        });
    }

    public function verify(Request $request, PhoneOtpService $otp): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:10']]);
        $user = $request->user();

        if (! $otp->verify($user->phone, PhoneOtpService::PURPOSE_VERIFY, $data['code'])) {
            throw ValidationException::withMessages(['code' => 'That code is wrong or has expired. Check the SMS or send a new code.']);
        }

        $user->forceFill(['phone_verified_at' => now()])->save();

        return redirect()->route($user->homeRoute())->with('status', 'Your phone number is confirmed. Welcome!');
    }
}
