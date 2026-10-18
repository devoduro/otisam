@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'placeholder' => 'Choose…',
])

@php
    $id = $id ?? $name;
    $current = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1 block text-sm font-semibold text-ink">
            {{ $label }}
            @unless ($required)<span class="font-normal text-muted">(optional)</span>@endunless
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}" @if ($required) required @endif @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border bg-white px-3 py-2.5 text-base shadow-sm focus:outline-none focus:ring-2 ' . ($hasError ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-primary-600 focus:ring-primary-100')]) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            @php $optionValue = is_int($optionValue) ? $optionLabel : $optionValue; @endphp
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p id="{{ $id }}-error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
