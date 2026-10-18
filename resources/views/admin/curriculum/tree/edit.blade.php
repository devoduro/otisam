@extends('layouts.admin')

@section('title', 'Edit: ' . \Illuminate\Support\Str::limit($node->title, 60))
@section('subtitle', $subject->name . ' · ' . $version->name)

@section('content')
<form method="POST" action="{{ route('admin.curriculum.tree.update', [$version, $subject, $node]) }}" class="card-panel" style="max-width:900px">
    @csrf
    @method('PUT')
    @include('admin.curriculum.tree._fields', ['prefix' => ''])
    <p class="small text-muted mt-3 mb-0">Moving an item to another place moves everything under it as well.</p>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-premium" type="submit">Save</button>
        <a href="{{ route('admin.curriculum.tree.show', [$version, $subject]) }}#node-{{ $node->id }}" class="btn btn-light">Cancel</a>
    </div>
</form>
@endsection
