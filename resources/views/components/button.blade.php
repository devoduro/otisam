{{-- Buttons in castvote's style (12px radius, navy primary, gold accent); 44px minimum touch target. --}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'href' => null,
    'full' => false,
    'icon' => null,
])

@php
    $classes = 'inline-flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl border-[1.5px] border-transparent px-5 py-2.5 text-[14.5px] font-bold no-underline transition active:translate-y-px focus:outline-none focus:ring-[3px] focus:ring-gold-500/40 disabled:opacity-60 '
        . ($full ? 'w-full ' : '')
        . match ($variant) {
            'accent' => 'bg-gold-500 text-primary-950 shadow-gold hover:bg-gold-700 hover:text-white',
            'outline' => 'border-[#e2e0ea] bg-white text-primary-700 hover:border-gold-500 hover:bg-gold-50 hover:text-gold-700',
            'ghost' => 'text-primary-600 hover:bg-[#f4f2f8] hover:text-primary-700',
            'light' => 'border-white/30 bg-white/10 text-white hover:bg-white/20 hover:text-white',
            'danger' => 'bg-red-600 text-white hover:bg-red-700 hover:text-white',
            default => 'bg-primary-600 text-white shadow-navy hover:bg-primary-700 hover:text-white',
        };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<i class="fas {{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<i class="fas {{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}</button>
@endif
