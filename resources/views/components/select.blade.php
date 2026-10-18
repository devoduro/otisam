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
        <label for="{{ $id }}" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">
            {{ $label }}
            @unless ($required)<span class="font-normal text-gray-500">(optional)</span>@endunless
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}" @if ($required) required @endif @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border-[1.5px] bg-white px-3.5 py-3 text-[15px] text-[#241038] focus:outline-none focus:ring-[3px] '.($hasError ? 'border-red-400 focus:ring-red-100' : 'border-[#e2e0ea] focus:border-gold-500 focus:ring-gold-500/30')]) }}>
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
