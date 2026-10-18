@php
    $scheduled = $resource->status === 'published' && $resource->publish_at?->isFuture();
    $tone = $scheduled ? 'info' : (['draft' => 'secondary', 'in_review' => 'warning', 'published' => 'success', 'archived' => 'dark'][$resource->status] ?? 'light');
@endphp
<span class="badge text-bg-{{ $tone }}">{{ $scheduled ? 'Scheduled '.$resource->publish_at->format('j M, H:i') : \App\Models\Library\Resource::STATUSES[$resource->status] }}</span>
