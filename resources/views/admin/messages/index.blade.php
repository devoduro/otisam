@extends('layouts.admin')

@section('title', 'Message log')
@section('subtitle', 'SMS and email sent by the platform')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Recipient or event…">
        </div>
        <div class="col-6 col-md-3">
            <label for="channel" class="form-label small fw-semibold">Channel</label>
            <select id="channel" name="channel" class="form-select">
                <option value="">SMS and email</option>
                <option value="sms" @selected(($filters['channel'] ?? '') === 'sms')>SMS</option>
                <option value="mail" @selected(($filters['channel'] ?? '') === 'mail')>Email</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="status" class="form-label small fw-semibold">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">Any</option>
                @foreach (\App\Http\Controllers\Admin\MessageLogController::STATUSES as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table align-middle table-stack small mb-0">
        <thead><tr><th>When</th><th>Channel</th><th>To</th><th>Event</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse ($messages as $message)
                <tr>
                    <td data-label="When" class="text-nowrap">{{ $message->created_at->format('j M Y, H:i') }}</td>
                    <td data-label="Channel">{{ $message->channel === 'sms' ? 'SMS' : 'Email' }}</td>
                    <td data-label="To">{{ $message->recipient }}</td>
                    <td data-label="Event">{{ $message->event_key }}</td>
                    <td data-label="Status"><span class="badge {{ $message->status === 'failed' ? 'text-bg-danger' : 'text-bg-light border' }}">{{ ucfirst($message->status) }}</span></td>
                    <td class="text-end"><a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No messages.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $messages->links() }}</div>
@endsection
