<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Plan;
use App\Services\Access\Entitlements;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request, Entitlements $entitlements): View
    {
        return view('public.plans', [
            'plans' => Plan::active()->where('price_minor', '>', 0)->ordered()->get(),
            'current' => $entitlements->currentSubscription($request->user()),
        ]);
    }
}
