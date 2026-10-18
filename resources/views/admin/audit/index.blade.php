@extends('layouts.admin')

@section('title', 'Audit log')
@section('subtitle', 'Who changed what, and when')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Description contains…">
        </div>
        <div class="col-6 col-md-2">
            <label for="category" class="form-label small fw-semibold">Category</label>
            <select id="category" name="category" class="form-select">
                <option value="">All</option>
                @foreach ($categories as $category)
                    <option @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="from" class="form-label small fw-semibold">From</label>
            <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
        </div>
        <div class="col-6 col-md-2">
            <label for="to" class="form-label small fw-semibold">To</label>
            <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
        </div>
        <div class="col-6 col-md-2 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table align-middle table-stack small">
        <thead><tr><th>When</th><th>Who</th><th>Category</th><th>What</th><th>Changes</th><th>IP</th></tr></thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td data-label="When" class="text-nowrap">{{ $log->created_at->format('j M Y, H:i') }}</td>
                    <td data-label="Who">{{ $log->user?->name ?? 'System / guest' }}</td>
                    <td data-label="Category"><span class="badge text-bg-light border">{{ $log->category }}</span></td>
                    <td data-label="What">{{ $log->description }}</td>
                    <td data-label="Changes">
                        @if ($log->old_values || $log->new_values)
                            <details>
                                <summary>View</summary>
                                <pre class="mb-0 small">{{ json_encode(['before' => $log->old_values, 'after' => $log->new_values], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif
                    </td>
                    <td data-label="IP">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No entries.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
