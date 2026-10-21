{{-- One labelled textarea of the lesson plan form. --}}
<div>
    <label for="{{ $name }}" class="{{ $label }}">{{ $title }} @unless ($required){!! $optional !!}@endunless</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" maxlength="{{ $name === 'main_activities' ? 10000 : 5000 }}" @required($required) class="{{ $control }}" placeholder="{{ $placeholder }}" @if ($hint) aria-describedby="{{ $name }}-hint" @endif>{{ old($name, $plan->{$name}) }}</textarea>
    @if ($hint)<p id="{{ $name }}-hint" class="mt-1 text-xs text-gray-500">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
