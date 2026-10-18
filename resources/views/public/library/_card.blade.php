{{-- One library item in a list. Expects $resource. --}}
<li>
    <a href="{{ route('library.show', $resource) }}" class="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-4 no-underline hover:border-primary-600 hover:bg-primary-50">
        <span class="flex flex-wrap items-center gap-2 text-xs font-semibold">
            <span class="rounded-full px-2 py-0.5 {{ $resource->isFree() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">{{ $resource->isFree() ? 'Free' : 'Premium' }}</span>
            <span class="text-muted">{{ $resource->category->name }}</span>
        </span>
        <span class="mt-2 font-bold text-ink">{{ $resource->title }}</span>
        <span class="mt-1 text-sm text-muted">
            {{ collect([$resource->subject?->name, $resource->examLabel(), $resource->classLevel?->name])->filter()->join(' · ') }}
        </span>
        @if (($resource->solutions_count ?? 0) > 0)
            <span class="mt-2 text-xs font-semibold text-primary-600">Solution available</span>
        @endif
    </a>
</li>
