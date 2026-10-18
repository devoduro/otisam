{{-- Flash messages in jhs_portal's style: coloured left border, icon, dismiss button. --}}
@foreach ([
    'success' => ['bg-green-50 border-green-500 text-green-700', 'fa-check-circle', 'status'],
    'status' => ['bg-primary-50 border-primary-500 text-primary-800', 'fa-info-circle', 'status'],
    'warning' => ['bg-yellow-50 border-yellow-500 text-yellow-700', 'fa-exclamation-triangle', 'status'],
    'error' => ['bg-red-50 border-red-500 text-red-700', 'fa-exclamation-circle', 'alert'],
] as $key => [$tone, $icon, $role])
    @if (is_string(session($key)))
        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start justify-between gap-3 border-l-4 p-3 text-sm {{ $tone }}" role="{{ $role }}">
            <p><i class="fas {{ $icon }} mr-2" aria-hidden="true"></i>{{ session($key) }}</p>
            <button type="button" @click="show = false" class="opacity-70 hover:opacity-100" aria-label="Dismiss"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
    @endif
@endforeach
