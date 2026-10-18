@extends('layouts.admin')

@section('title', $subject->name)
@section('subtitle', $version->name . ' · ' . $total . ' item' . ($total === 1 ? '' : 's'))

@section('actions')
    <a href="{{ route('admin.curriculum.versions.show', $version) }}" class="btn btn-light rounded-pill px-3 d-none d-md-inline-block">All subjects</a>
    <a href="{{ route('admin.curriculum.tree.export', [$version, $subject]) }}" class="btn btn-outline-secondary rounded-pill px-3">Export CSV</a>
    @can('curriculum.manage')
        <a href="{{ route('admin.curriculum.tree.import', [$version, $subject]) }}" class="btn btn-outline-primary rounded-pill px-3">Import CSV</a>
    @endcan
@endsection

@section('content')
@php $canManage = auth()->user()->can('curriculum.manage'); @endphp

@unless ($version->isPublic())
    <div class="alert alert-warning small">This version is a <strong>draft</strong>: learners cannot see this tree until the version is made active.</div>
@endunless

<div class="row g-3">
    <div class="{{ $canManage ? 'col-xl-7' : 'col-12' }}">
        <div class="card-panel">
            @if ($roots->isEmpty())
                <div class="empty-state-container py-4">
                    <p class="empty-text mb-1">No topics yet for {{ $subject->name }} in this version.</p>
                    @if ($canManage)<p class="small text-muted mb-0">Add the first strand or topic with the form, or import a CSV file.</p>@endif
                </div>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach ($roots as $node)
                        @include('admin.curriculum.tree._node', ['node' => $node])
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if ($canManage)
        <div class="col-xl-5">
            <form method="POST" action="{{ route('admin.curriculum.tree.store', [$version, $subject]) }}" class="card-panel" style="position:sticky; top:calc(var(--head-h) + 1rem)">
                @csrf
                <h2 class="h6 fw-bold mb-3">Add an item</h2>
                @include('admin.curriculum.tree._fields', ['node' => new \App\Models\Curriculum\CurriculumNode(['node_type' => $roots->isEmpty() ? 'strand' : 'sub_strand']), 'prefix' => 'new-'])
                <button class="btn btn-premium mt-3" type="submit">Add</button>
            </form>
        </div>
    @endif
</div>
@endsection
