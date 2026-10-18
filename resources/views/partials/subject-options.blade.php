{{-- <option>s for a subject <select>, grouped by level. Expects $levels (with subjects) and $selected (id or null). --}}
@foreach ($levels as $l)
    @continue($l->subjects->isEmpty())
    <optgroup label="{{ $l->name }}">
        @foreach ($l->subjects as $s)<option value="{{ $s->id }}" @selected((int) $selected === $s->id)>{{ $s->name }}</option>@endforeach
    </optgroup>
@endforeach
