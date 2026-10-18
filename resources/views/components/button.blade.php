{{-- Adapted from jhs_portal's x-button; 44px minimum touch target. --}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'href' => null,
    'full' => false,
])

@php
    $classes = 'inline-flex min-h-11 items-center justify-center rounded-lg px-5 py-2.5 text-base font-semibold no-underline transition-colors '
        . ($full ? 'w-full ' : '')
        . match ($variant) {
            'primary' => 'bg-primary-900 text-white hover:bg-primary-600',
            'accent' => 'bg-accent-500 text-ink hover:bg-accent-400',
            'outline' => 'border border-primary-900 text-primary-900 hover:bg-primary-50',
            'ghost' => 'text-primary-900 hover:bg-primary-50',
            default => 'bg-primary-900 text-white hover:bg-primary-600',
        };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
