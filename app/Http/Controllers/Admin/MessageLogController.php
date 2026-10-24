<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** SMS and email delivery log. */
class MessageLogController extends Controller
{
    public const STATUSES = ['queued', 'sent', 'delivered', 'failed'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'channel' => ['nullable', 'in:'.NotificationMessage::CHANNEL_SMS.','.NotificationMessage::CHANNEL_MAIL],
            'status' => ['nullable', 'in:'.implode(',', self::STATUSES)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $messages = NotificationMessage::with('user')
            ->when($filters['channel'] ?? null, fn ($q, $c) => $q->where('channel', $c))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('recipient', 'like', "%{$s}%")->orWhere('event_key', 'like', "%{$s}%")))
            ->latest('id')
            ->paginate(30)->withQueryString();

        return view('admin.messages.index', ['messages' => $messages, 'filters' => $filters]);
    }

    public function show(NotificationMessage $message): View
    {
        return view('admin.messages.show', ['message' => $message->load('user')]);
    }
}
