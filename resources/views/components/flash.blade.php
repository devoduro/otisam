@foreach (['success' => 'border-green-600 bg-green-50 text-green-900', 'status' => 'border-primary-600 bg-primary-50 text-primary-900', 'error' => 'border-red-600 bg-red-50 text-red-900'] as $key => $tone)
    @if (is_string(session($key)))
        <div class="mb-4 rounded-lg border-l-4 px-4 py-3 text-sm {{ $tone }}" role="{{ $key === 'error' ? 'alert' : 'status' }}">{{ session($key) }}</div>
    @endif
@endforeach
