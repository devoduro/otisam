@extends('layouts.admin')

@section('title', 'Import topics: ' . $subject->name)
@section('subtitle', $version->name)

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <form method="POST" action="{{ route('admin.curriculum.tree.import.preview', [$version, $subject]) }}" enctype="multipart/form-data" class="card-panel">
            @csrf
            <h2 class="h6 fw-bold mb-2">1. Upload a CSV file</h2>
            <p class="small text-muted">Nothing is saved at this step. You will see every problem first, and confirm before anything is added.</p>
            <label for="file" class="form-label small fw-semibold">CSV file (max 2 MB)</label>
            <input id="file" type="file" name="file" accept=".csv,text/csv" class="form-control mb-3" required>
            <button class="btn btn-premium" type="submit">Check file</button>
        </form>

        <div class="card-panel mt-3 small">
            <h2 class="h6 fw-bold">File format</h2>
            <p class="mb-2"><a href="{{ route('admin.curriculum.tree.template') }}">Download the template</a>. Columns:</p>
            <ul class="mb-2 ps-3">
                <li><code>code</code>: optional, unique in this subject. Needed if other rows go under this one.</li>
                <li><code>parent_code</code>: the code of the item it belongs under. Empty means top level. The parent must appear earlier in the file or already be in the tree.</li>
                <li><code>type</code>: {{ implode(', ', array_keys(\App\Models\Curriculum\CurriculumNode::TYPES)) }}</li>
                <li><code>title</code> (required), <code>statement</code></li>
                <li><code>class</code>: e.g. {{ $version->educationLevel->classLevels->pluck('name')->join(', ') ?: 'a class name' }}</li>
                <li><code>official</code>: yes/no. <code>source_ref</code>: page or section of the official document.</li>
            </ul>
            <p class="mb-0 text-muted">Tip: “Export CSV” on the tree page produces a file in exactly this format.</p>
        </div>
    </div>

    <div class="col-lg-7">
        @if ($preview)
            @php $errorCount = count($preview['errors']); @endphp
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-2">2. Check the results</h2>
                @if ($errorCount)
                    <div class="alert alert-danger small">
                        <strong>{{ $errorCount }} problem{{ $errorCount === 1 ? '' : 's' }} found. Nothing can be imported until the file is fixed.</strong>
                    </div>
                    <ul class="small mb-3">
                        @foreach ($preview['errors'] as $line => $message)
                            <li>Line {{ $line }}: {{ $message }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-success small mb-3">{{ count($preview['rows']) }} item(s) ready to import.</div>
                @endif

                @if ($preview['warnings'])
                    <div class="alert alert-warning small">
                        <div class="fw-bold mb-1">Warnings (import still allowed)</div>
                        <ul class="mb-0">
                            @foreach ($preview['warnings'] as $line => $message)
                                <li>Line {{ $line }}: {{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($preview['rows'])
                    <div class="table-responsive mb-3" style="max-height:360px">
                        <table class="table table-sm small align-middle">
                            <thead><tr><th>Line</th><th>Code</th><th>Under</th><th>Type</th><th>Title</th><th>Official</th></tr></thead>
                            <tbody>
                                @foreach ($preview['rows'] as $line => $row)
                                    <tr class="{{ isset($preview['errors'][$line]) ? 'table-danger' : '' }}">
                                        <td>{{ $line }}</td><td>{{ $row['code'] }}</td><td>{{ $row['parent_code'] }}</td>
                                        <td>{{ \App\Models\Curriculum\CurriculumNode::TYPES[$row['node_type']] ?? $row['node_type'] }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($row['title'], 60) }}</td><td>{{ $row['is_official'] ? 'yes' : 'no' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @unless ($errorCount)
                    <form method="POST" action="{{ route('admin.curriculum.tree.import.commit', [$version, $subject]) }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <button class="btn btn-premium" type="submit">Import {{ count($preview['rows']) }} item(s)</button>
                        <a href="{{ route('admin.curriculum.tree.show', [$version, $subject]) }}" class="btn btn-light">Cancel</a>
                    </form>
                @endunless
            </div>
        @endif
    </div>
</div>
@endsection
