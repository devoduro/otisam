<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:40'],
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $logs = AuditLog::with('user')
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category', $c))
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($filters['from'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($filters['to'] ?? null, fn ($q, $d) => $q->where('created_at', '<', \Illuminate\Support\Carbon::parse($d)->addDay()))
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $categories = AuditLog::query()->distinct()->orderBy('category')->pluck('category');

        return view('admin.audit.index', compact('logs', 'filters', 'categories'));
    }
}
