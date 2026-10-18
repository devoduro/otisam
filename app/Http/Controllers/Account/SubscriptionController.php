<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Subscription;
use App\Services\Access\Entitlements;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request, Entitlements $entitlements): View
    {
        $user = $request->user();

        return view('account.subscription', [
            'current' => $entitlements->currentSubscription($user),
            'subscriptions' => Subscription::where('user_id', $user->id)->with('plan')->orderByDesc('starts_at')->get(),
            'payments' => Payment::where('user_id', $user->id)->with(['plan', 'receipt'])->latest()->limit(50)->get(),
        ]);
    }

    public function receipt(Request $request, Payment $payment): View
    {
        abort_unless($payment->user_id === $request->user()->id && $payment->receipt, 404);

        return view('account.receipt', ['payment' => $payment->load(['plan', 'receipt', 'subscription', 'user'])]);
    }
}
