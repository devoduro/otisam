{{-- Text field with label, hint and error, adapted from jhs_portal's x-input and sign-in fields. --}}
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
    $isPassword = $type === 'password';
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">
            {{ $label }}
            @unless ($required)<span class="font-normal text-gray-500">(optional)</span>@endunless
        </label>
    @endif

    <div class="relative">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            @unless ($isPassword) value="{{ old($name, $value) }}" @endunless
            @if ($required) required @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge(['class' => 'block w-full rounded-xl border-[1.5px] bg-white px-3.5 py-3 text-[15px] text-[#241038] placeholder-[#a9a3b8] focus:outline-none focus:ring-[3px] '.($isPassword ? 'pr-11 ' : '').($hasError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-[#e2e0ea] focus:border-gold-500 focus:ring-gold-500/30')]) }}
        />
        @if ($isPassword)
            <button type="button" data-toggle-password="{{ $id }}" aria-label="Show password"
                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 hover:text-gray-600"><i class="fas fa-eye" aria-hidden="true"></i></button>
        @endif
    </div>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
