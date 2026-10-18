{{-- Buttons in jhs_portal's style (rounded-md, sky primary); 44px minimum touch target. --}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'href' => null,
    'full' => false,
    'icon' => null,
])

@php
    $classes = 'inline-flex min-h-11 items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold no-underline shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-70 '
        . ($full ? 'w-full ' : '')
        . match ($variant) {
            'accent' => 'bg-tile-green text-white hover:bg-tile-green-dark hover:text-white focus:ring-tile-green',
            'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-800 focus:ring-primary-300',
            'ghost' => 'text-primary-600 shadow-none hover:bg-primary-50',
            'light' => 'bg-white text-slate-800 hover:bg-gray-100 hover:text-slate-900 focus:ring-white',
            'danger' => 'bg-red-600 text-white hover:bg-red-700 hover:text-white focus:ring-red-300',
            default => 'bg-primary-600 text-white hover:bg-primary-700 hover:text-white focus:ring-primary-300',
        };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<i class="fas {{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<i class="fas {{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}</button>
@endif
