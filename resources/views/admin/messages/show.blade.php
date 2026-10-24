@extends('layouts.admin')

@section('title', ($message->channel === 'sms' ? 'SMS' : 'Email').' to '.$message->recipient)
@section('subtitle', $message->created_at->format('l, j F Y, H:i'))

@section('actions')
    <a href="{{ route('admin.messages.index', ['channel' => $message->channel]) }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Back to the log</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel">
            @if ($message->subject)<h2 class="h6 fw-bold">{{ $message->subject }}</h2>@endif
            <p class="mb-0" style="white-space: pre-line">{{ $message->body }}</p>
        </div>
        @if ($message->last_error)
            <div class="alert alert-danger mt-3 mb-0"><strong>Last error:</strong> {{ $message->last_error }}</div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card-panel">
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">Status</dt><dd class="col-7">{{ ucfirst($message->status) }}</dd>
                <dt class="col-5 text-muted">Event</dt><dd class="col-7">{{ $message->event_key }}</dd>
                <dt class="col-5 text-muted">Category</dt><dd class="col-7">{{ $message->category }}</dd>
                <dt class="col-5 text-muted">Account</dt><dd class="col-7">{{ $message->user?->name ?? '—' }}</dd>
                <dt class="col-5 text-muted">Provider</dt><dd class="col-7">{{ $message->provider ?? '—' }}</dd>
                <dt class="col-5 text-muted">Attempts</dt><dd class="col-7">{{ $message->attempts }}</dd>
                <dt class="col-5 text-muted">Sent</dt><dd class="col-7">{{ $message->sent_at?->format('j M Y, H:i') ?? '—' }}</dd>
                <dt class="col-5 text-muted">Delivered</dt><dd class="col-7">{{ $message->delivered_at?->format('j M Y, H:i') ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
