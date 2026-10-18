@extends('layouts.admin')

@section('title', 'Bulk upload')
@section('subtitle', 'Up to 20 files with the same details; each becomes a draft named after its file')

@section('content')
<form method="POST" action="{{ route('admin.library.resources.bulk.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-panel mb-3">
        <label for="files" class="form-label fw-semibold">Files</label>
        <input id="files" type="file" name="files[]" multiple required class="form-control">
        <div class="form-text">{{ implode(', ', array_keys(config('platform.library.allowed'))) }} · max {{ config('platform.library.max_upload_mb') }} MB each. Files that fail the safety checks are skipped and listed afterwards.</div>
    </div>
    <div class="card-panel mb-3">
        <h2 class="h6 fw-bold mb-3">Details applied to every file</h2>
        @include('admin.library.resources._fields', ['bulk' => true])
    </div>
    <button class="btn btn-premium" type="submit">Upload as drafts</button>
</form>
@endsection
