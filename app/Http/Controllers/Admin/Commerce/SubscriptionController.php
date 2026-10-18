<?php

namespace App\Http\Controllers\Admin\Commerce;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\Commerce\Plan;
use App\Models\Commerce\Subscription;
use App\Models\User;
use App\Services\Commerce\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate(['state' => ['nullable', 'in:active,expired,revoked']])['state'] ?? 'active';

        $subscriptions = Subscription::with(['user', 'plan', 'grantedBy'])
            ->when($filter === 'active', fn ($q) => $q->current())
            ->when($filter === 'expired', fn ($q) => $q->where('status', 'active')->where('ends_at', '<=', now())->where(fn ($w) => $w->whereNull('grace_ends_at')->orWhere('grace_ends_at', '<=', now())))
            ->when($filter === 'revoked', fn ($q) => $q->whereIn('status', ['revoked', 'refunded']))
            ->orderByDesc('ends_at')
            ->paginate(30)->withQueryString();

        return view('admin.commerce.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'filter' => $filter,
            'activeCount' => Subscription::current()->distinct('user_id')->count('user_id'),
            'plans' => Plan::ordered()->get(),
        ]);
    }

    public function grant(Request $request, SubscriptionService $service): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'months' => ['required', 'integer', 'min:1', 'max:24'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $user = User::findByLogin($data['login']);
        if (! $user || $user->account_type === AccountType::Staff) {
            throw ValidationException::withMessages(['login' => 'No student or teacher account has that phone number or email.']);
        }

        $subscription = DB::transaction(fn () => $service->grantManually($user, (int) $data['months'], ! empty($data['plan_id']) ? Plan::find($data['plan_id']) : null, $data['reason'], $request->user()));

        return back()->with('success', "{$user->name} has premium access from {$subscription->starts_at->format('j M Y')} to {$subscription->ends_at->format('j M Y')}.");
    }

    public function revoke(Request $request, Subscription $subscription, SubscriptionService $service): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);
        abort_unless($subscription->status === 'active', 422);

        $service->revoke($subscription, $data['reason'], $request->user());

        return back()->with('success', 'Subscription revoked.');
    }
}
