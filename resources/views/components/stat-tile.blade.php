@props([
    'color' => 'blue',     // blue | green | orange | red | slate | purple
    'icon' => 'fa-chart-simple',
    'title',
    'value',
    'sub' => null,         // small caption under the title (e.g. "This year")
    'badge' => null,       // small pill on the right (e.g. "86% of billed")
    'href' => null,
])
@php
    // Flat, two-tone tiles: a darker icon block beside a solid colour body.
    $palette = [
        'blue'   => ['body' => 'bg-tile-blue', 'icon' => 'bg-tile-blue-dark'],
        'green'  => ['body' => 'bg-tile-green', 'icon' => 'bg-tile-green-dark'],
        'orange' => ['body' => 'bg-tile-orange', 'icon' => 'bg-tile-orange-dark'],
        'red'    => ['body' => 'bg-tile-red', 'icon' => 'bg-tile-red-dark'],
        'slate'  => ['body' => 'bg-tile-slate', 'icon' => 'bg-tile-slate-dark'],
        'purple' => ['body' => 'bg-tile-purple', 'icon' => 'bg-tile-purple-dark'],
    ][$color] ?? ['body' => 'bg-tile-blue', 'icon' => 'bg-tile-blue-dark'];
    $tag = $href ? 'a' : 'div';
    // Long values (e.g. "GH₵ 108,360.00") get a smaller size so they never clip in a 4-across row.
    $valueLength = mb_strlen(trim(strip_tags((string) $value)));
    $valueSize = $valueLength > 12 ? 'text-base xl:text-lg' : ($valueLength > 9 ? 'text-lg xl:text-xl' : 'text-2xl');
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group flex overflow-hidden rounded-md text-white shadow-sm transition ' . ($href ? 'hover:shadow-md hover:-translate-y-0.5' : '')]) }}>
    <span class="flex w-16 shrink-0 items-center justify-center {{ $palette['icon'] }}">
        <i class="fas {{ $icon }} text-2xl"></i>
    </span>
    <span class="flex min-w-0 flex-1 flex-wrap items-center justify-between gap-x-3 gap-y-1 px-4 py-3 {{ $palette['body'] }}">
        <span class="min-w-0 flex-1 basis-28">
            <span class="block text-base font-semibold leading-tight">{{ $title }}</span>
            @if($sub)<span class="block truncate text-xs opacity-90">{{ $sub }}</span>@endif
        </span>
        <span class="text-right">
            <span class="block whitespace-nowrap {{ $valueSize }} font-bold leading-tight tracking-tight">{{ $value }}</span>
            @if($badge)<span class="mt-0.5 inline-block whitespace-nowrap rounded-full bg-white/90 px-2 py-0.5 text-[11px] font-semibold text-gray-700">{{ $badge }}</span>@endif
        </span>
    </span>
</{{ $tag }}>
