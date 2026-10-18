<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\TakedownRequest;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TakedownController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->validate(['status' => ['nullable', Rule::in(array_keys(TakedownRequest::STATUSES))]])['status'] ?? 'open';

        $requests = TakedownRequest::with(['resource', 'handler'])->where('status', $status)->latest()->paginate(25)->withQueryString();

        return view('admin.library.takedowns', compact('requests', 'status'));
    }

    public function update(Request $request, TakedownRequest $takedown): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['resolved', 'rejected'])],
            'resolution_note' => ['required', 'string', 'max:2000'],
            'unpublish' => ['boolean'],
        ]);

        if ($request->boolean('unpublish') && $takedown->resource && $takedown->resource->status === 'published') {
            $takedown->resource->forceFill(['status' => 'archived', 'publish_at' => null])->save();
            Audit::record('library', 'resource.archived', "Archived after takedown request: {$takedown->resource->title}", $takedown->resource);
        }

        $takedown->forceFill([
            'status' => $data['status'],
            'resolution_note' => $data['resolution_note'],
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ])->save();

        Audit::record('library', "takedown.{$data['status']}", "Takedown request #{$takedown->id} {$data['status']}", $takedown);

        return back()->with('success', "Request #{$takedown->id} marked {$data['status']}. Reply to {$takedown->email} with the outcome.");
    }
}
