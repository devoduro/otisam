@extends('layouts.admin')

@section('title', $resource->exists ? \Illuminate\Support\Str::limit($resource->title, 60) : 'Add resource')
@section('subtitle', $resource->exists ? ($resource->category?->name.' · added by '.($resource->author?->name ?? 'unknown')) : 'Saved as a draft until you publish it')

@section('content')
@php
    $canEdit = auth()->user()->can('resources.manage');
    $canPublish = auth()->user()->can('resources.publish');
    $main = $resource->mainFile;
    $maxMb = config('platform.library.max_upload_mb');
    $allowed = implode(', ', array_keys(config('platform.library.allowed')));
@endphp

<div class="row g-3">
    <div class="col-xl-8">
        <form method="POST" enctype="multipart/form-data" action="{{ $resource->exists ? route('admin.library.resources.update', $resource) : route('admin.library.resources.store') }}" id="resource-form">
            @csrf
            @if ($resource->exists) @method('PUT') @endif
            <fieldset @disabled(! $canEdit)>
                <div class="card-panel mb-3">
                    @include('admin.library.resources._fields')
                </div>

                <div class="card-panel mb-3">
                    <h2 class="h6 fw-bold mb-2">{{ $main ? 'Replace the document' : 'Document' }}</h2>
                    <label for="file" class="form-label small">{{ $allowed }} · max {{ $maxMb }} MB. PDFs get an automatic preview.</label>
                    <input id="file" type="file" name="file" class="form-control" accept=".{{ str_replace(', ', ',.', $allowed) }}">
                    @if ($main)<div class="form-text">The current file is kept in the history when you replace it.</div>@endif
                </div>

                @if ($resource->exists && $resource->subject_id)
                    <div class="card-panel mb-3">
                        <h2 class="h6 fw-bold mb-1">Curriculum topics</h2>
                        <p class="small text-muted">Tick the {{ $resource->subject->name }} topics this resource covers. Learners will find it on those topic pages.</p>
                        <input type="hidden" name="curriculum_nodes_present" value="1">
                        @php $tagged = old('curriculum_nodes', $resource->curriculumNodes->pluck('id')->all()); @endphp
                        @forelse ($curriculumNodes as $versionName => $nodes)
                            <details class="mb-2" @if (array_intersect($tagged, collect($nodes)->pluck('id')->all())) open @endif>
                                <summary class="small fw-semibold">{{ $versionName }} ({{ count($nodes) }} items)</summary>
                                <div class="mt-2" style="max-height:320px; overflow:auto">
                                    @foreach ($nodes as $node)
                                        <div class="form-check" style="margin-left: {{ $node->depth * 1.25 }}rem">
                                            <input class="form-check-input" type="checkbox" name="curriculum_nodes[]" value="{{ $node->id }}" id="cn-{{ $node->id }}" @checked(in_array($node->id, $tagged))>
                                            <label class="form-check-label small" for="cn-{{ $node->id }}">{{ $node->code ? $node->code.' ' : '' }}{{ $node->title }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @empty
                            <p class="small mb-0">This subject has no curriculum topics yet.</p>
                        @endforelse
                    </div>
                @endif

                @if ($canEdit)
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-premium" type="submit">{{ $resource->exists ? 'Save changes' : 'Save draft' }}</button>
                        <a href="{{ route('admin.library.resources.index') }}" class="btn btn-light">Back to list</a>
                    </div>
                @endif
            </fieldset>
        </form>
    </div>

    @if ($resource->exists)
        <div class="col-xl-4">
            {{-- Workflow --}}
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Status</h2>
                <p class="mb-2">@include('admin.library.resources._status', ['resource' => $resource])
                    @if ($resource->published_at)<span class="small text-muted">first published {{ $resource->published_at->format('j M Y') }}</span>@endif
                </p>
                <p class="small text-muted mb-2">{{ number_format($resource->view_count) }} views · {{ number_format($resource->download_count) }} downloads</p>
                @if ($resource->isVisible())
                    <a href="{{ route('library.show', $resource) }}" target="_blank" rel="noopener" class="small d-block mb-3">Open public page</a>
                @endif

                <div class="d-flex flex-wrap gap-2">
                    @if ($resource->status === 'draft' && $canEdit)
                        <form method="POST" action="{{ route('admin.library.resources.status', $resource) }}">@csrf<input type="hidden" name="status" value="in_review"><button class="btn btn-sm btn-outline-warning" type="submit">Submit for review</button></form>
                    @endif
                    @if ($canPublish && $resource->status !== 'archived')
                        @if ($resource->status === 'published')
                            <form method="POST" action="{{ route('admin.library.resources.status', $resource) }}">@csrf<input type="hidden" name="status" value="draft"><button class="btn btn-sm btn-outline-secondary" type="submit" data-confirm="Unpublish? It will disappear from the public library.">Unpublish</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.library.resources.status', $resource) }}">@csrf<input type="hidden" name="status" value="archived"><button class="btn btn-sm btn-outline-dark" type="submit" data-confirm="Archive this resource?">Archive</button></form>
                    @elseif ($canPublish)
                        <form method="POST" action="{{ route('admin.library.resources.status', $resource) }}">@csrf<input type="hidden" name="status" value="draft"><button class="btn btn-sm btn-outline-secondary" type="submit">Restore to draft</button></form>
                    @endif
                </div>

                @if ($canPublish && $resource->status !== 'published')
                    <form method="POST" action="{{ route('admin.library.resources.status', $resource) }}" class="mt-3 border-top pt-3">
                        @csrf
                        <input type="hidden" name="status" value="published">
                        <label for="publish_at" class="form-label small fw-semibold">Publish <span class="fw-normal text-muted">(leave empty for now)</span></label>
                        <div class="d-flex gap-2">
                            <input id="publish_at" type="datetime-local" name="publish_at" class="form-control form-control-sm">
                            <button class="btn btn-sm btn-premium" type="submit">Publish</button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Files --}}
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Files</h2>
                @if ($main)
                    <p class="small mb-1"><i class="fa-regular fa-file" aria-hidden="true"></i> {{ $main->original_name }}</p>
                    <p class="small text-muted mb-2">{{ strtoupper($main->extension) }} · {{ $main->humanSize() }}{{ $main->page_count ? ' · '.$main->page_count.' pages' : '' }} · version {{ $main->version }}</p>
                    <a class="small d-block mb-2" href="{{ route('library.view', $resource) }}" target="_blank" rel="noopener">Open full document</a>
                    <p class="small mb-1"><strong>Preview:</strong>
                        @switch($main->preview_status)
                            @case('ready') <span class="text-success">ready ({{ $resource->previewFile?->page_count }} page(s))</span> @break
                            @case('manual') <span class="text-success">uploaded by staff</span> @break
                            @case('pending') <span class="text-warning">being generated (runs from the queue)</span> @break
                            @case('failed') <span class="text-danger">failed</span> @break
                            @default <span class="text-muted">none</span>
                        @endswitch
                    </p>
                    @if ($main->preview_error)<p class="small text-muted">{{ $main->preview_error }}</p>@endif
                    @if ($resource->previewFile)
                        <a class="small d-block mb-2" href="{{ route('library.preview', $resource) }}" target="_blank" rel="noopener">Open preview</a>
                    @endif
                    @if ($canEdit)
                        <label for="preview_file" class="form-label small fw-semibold mt-2">Upload your own preview (PDF)</label>
                        <input id="preview_file" type="file" name="preview_file" form="resource-form" accept=".pdf" class="form-control form-control-sm">
                        <div class="form-text">Saved with “Save changes”. Use this when automatic preview fails.</div>
                    @endif
                @else
                    <p class="small text-danger mb-0">No document uploaded yet.</p>
                @endif
            </div>

            {{-- Links --}}
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Solutions, marking schemes &amp; related</h2>
                @forelse ($resource->linkedResources as $linked)
                    <div class="d-flex justify-content-between align-items-center gap-2 small border-bottom py-1">
                        <span><span class="badge text-bg-light border">{{ \App\Models\Library\Resource::RELATIONS[$linked->pivot->relation] ?? $linked->pivot->relation }}</span>
                            <a href="{{ route('admin.library.resources.edit', $linked) }}">{{ \Illuminate\Support\Str::limit($linked->title, 50) }}</a></span>
                        @if ($canEdit)
                            <form method="POST" action="{{ route('admin.library.resources.links.destroy', [$resource, $linked, $linked->pivot->relation]) }}">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-link text-danger p-0" type="submit" aria-label="Remove link">Remove</button></form>
                        @endif
                    </div>
                @empty
                    <p class="small text-muted">Nothing linked yet.</p>
                @endforelse
                @foreach ($resource->linkedFrom as $parent)
                    <p class="small mt-2 mb-0">This is the {{ strtolower(\App\Models\Library\Resource::RELATIONS[$parent->pivot->relation] ?? $parent->pivot->relation) }} of <a href="{{ route('admin.library.resources.edit', $parent) }}">{{ \Illuminate\Support\Str::limit($parent->title, 50) }}</a>.</p>
                @endforeach

                @if ($canEdit && $linkCandidates->isNotEmpty())
                    <form method="POST" action="{{ route('admin.library.resources.links.store', $resource) }}" class="mt-3 border-top pt-3">
                        @csrf
                        <label for="linked_resource_id" class="form-label small fw-semibold">Link another resource{{ $resource->subject ? ' ('.$resource->subject->name.')' : '' }}</label>
                        <select id="linked_resource_id" name="linked_resource_id" class="form-select form-select-sm mb-2" required>
                            <option value="">Choose…</option>
                            @foreach ($linkCandidates as $c)<option value="{{ $c->id }}">{{ \Illuminate\Support\Str::limit($c->title, 60) }} ({{ $c->category->name }})</option>@endforeach
                        </select>
                        <label for="relation" class="visually-hidden">Link type</label>
                        <div class="d-flex gap-2">
                            <select id="relation" name="relation" class="form-select form-select-sm">
                                @foreach (\App\Models\Library\Resource::RELATIONS as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary" type="submit">Link</button>
                        </div>
                    </form>
                @endif
            </div>

            @if ($canEdit)
                <div class="card-panel">
                    <h2 class="h6 fw-bold mb-2">Delete</h2>
                    <form method="POST" action="{{ route('admin.library.resources.destroy', $resource) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this resource? It disappears everywhere; files are kept so it can be restored.">Delete resource</button>
                    </form>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
