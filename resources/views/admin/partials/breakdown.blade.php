{{-- Horizontal bar list. $items: label => count; $heading optional; $empty: text when there is nothing to show. --}}
@php
    $items = collect($items ?? [])->filter(fn ($v) => $v > 0);
    $max = max(1, (int) $items->max());
@endphp
<div class="dash-breakdown">
    @if (! empty($heading))<h3 class="dash-breakdown-title">{{ $heading }}</h3>@endif
    @forelse ($items as $label => $count)
        <div class="dash-bar">
            <div class="dash-bar-head"><span>{{ $label }}</span><strong>{{ number_format($count) }}</strong></div>
            <div class="dash-bar-track"><span style="width: {{ max(3, round($count / $max * 100)) }}%"></span></div>
        </div>
    @empty
        <p class="text-muted small mb-0">{{ $empty ?? 'Nothing recorded yet.' }}</p>
    @endforelse
</div>
