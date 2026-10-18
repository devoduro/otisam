{{-- Public tree item. Items with children collapse so long curricula stay readable on a phone. --}}
<li>
    @if ($node->tree_children->isNotEmpty())
        <details @if ($node->depth === 0) open @endif class="group">
            <summary class="flex cursor-pointer list-none items-start gap-2 rounded-lg px-2 py-2 hover:bg-primary-50">
                <span aria-hidden="true" class="mt-0.5 inline-block w-4 text-primary-600 transition group-open:rotate-90">▸</span>
                <span class="min-w-0">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $node->typeLabel() }}{{ $node->code ? ' ' . $node->code : '' }}</span>
                    <a href="{{ route('subjects.topic', [$subject, $node]) }}" class="block font-semibold text-gray-800 no-underline hover:text-primary-600">{{ $node->title }}</a>
                </span>
            </summary>
            <ul class="ml-4 border-l border-gray-200 pl-2">
                @foreach ($node->tree_children as $child)
                    @include('public.subjects._node', ['node' => $child])
                @endforeach
            </ul>
        </details>
    @else
        <a href="{{ route('subjects.topic', [$subject, $node]) }}" class="flex items-start gap-2 rounded-lg px-2 py-2 no-underline hover:bg-primary-50">
            <span aria-hidden="true" class="mt-0.5 inline-block w-4 text-gray-400">•</span>
            <span class="min-w-0">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $node->typeLabel() }}{{ $node->code ? ' ' . $node->code : '' }}</span>
                <span class="block text-gray-800">{{ $node->title }}</span>
            </span>
        </a>
    @endif
</li>
