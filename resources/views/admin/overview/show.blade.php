@extends('layouts.admin')

@section('title', $title)
@section('subtitle', 'Overview')

@section('actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Dashboard</a>
    @if ($fullHref)<a href="{{ $fullHref }}" class="btn btn-primary">{{ $fullLabel }} <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>@endif
@endsection

@section('content')
<div class="d-grid gap-4">
    <div class="j-tiles j-tiles-3">
        @foreach ($tiles as $i => [$label, $value])
            <div class="j-tile j-{{ $i === 0 ? $colour : 'slate' }}">
                <span class="ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="bd">
                    <span style="min-width:0"><span class="t">{{ $label }}</span></span>
                    <span class="text-end"><span class="v d-block">{{ number_format($value) }}</span></span>
                </span>
            </div>
        @endforeach
    </div>

    <div class="j-cols j-cols-2">
        <div class="j-card">
            @foreach ($sections as [$heading, $items])
                @include('admin.partials.breakdown', ['heading' => $heading, 'items' => $items])
            @endforeach
        </div>

        <div class="j-card j-card-flush">
            <div class="j-card-head j-pad"><h4>{{ $listTitle }}</h4>@if ($fullHref)<a href="{{ $fullHref }}">View all</a>@endif</div>
            <div class="j-list">
                @forelse ($rows as $row)
                    @if ($row['href'])<a href="{{ $row['href'] }}" class="j-row">@else<div class="j-row">@endif
                        <span class="j-avatar" style="background: {{ ['blue' => '#42a5dc', 'purple' => '#967adc', 'slate' => '#5b6f8f', 'orange' => '#f5922f'][$colour] ?? '#42a5dc' }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                        <span class="j-row-main"><span class="j-row-title">{{ $row['title'] }}</span><span class="j-row-sub">{{ $row['sub'] }}</span></span>
                        @if ($row['href'])<i class="fa-solid fa-chevron-right j-chev" aria-hidden="true"></i>@endif
                    @if ($row['href'])</a>@else</div>@endif
                @empty
                    <p class="j-empty">Nothing yet.</p>
                @endforelse
            </div>
            @unless ($fullHref)
                <p class="j-note px-3 py-2 border-top">Names and numbers are shortened for your role.</p>
            @endunless
        </div>
    </div>
</div>
@endsection
