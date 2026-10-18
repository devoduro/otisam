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
    // A plain list (['Ashanti', 'Volta']) submits the label; an id => label map submits the id.
    $isList = array_is_list($options);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-800">
            {{ $label }}
            @unless ($required)<span class="font-normal text-gray-500">(optional)</span>@endunless
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}" @if ($required) required @endif @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-md border bg-white px-3.5 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 '.($hasError ? 'border-red-400 focus:ring-red-100' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-100')]) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            @php $optionValue = $isList ? $optionLabel : $optionValue; @endphp
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p id="{{ $id }}-error" class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
