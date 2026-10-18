{{-- Text field with label, hint and error, adapted from jhs_portal's x-input. --}}
@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'required' => false,
    'hint' => null,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $describedBy = trim(($hint ? "{$id}-hint " : '') . ($hasError ? "{$id}-error" : ''));
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1 block text-sm font-semibold text-ink">
            {{ $label }}
            @unless ($required)<span class="font-normal text-muted">(optional)</span>@endunless
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border bg-white px-3 py-2.5 text-base shadow-sm focus:outline-none focus:ring-2 ' . ($hasError ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-primary-600 focus:ring-primary-100')]) }}
    />

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1 text-sm text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
