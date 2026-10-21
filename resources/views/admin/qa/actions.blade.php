{{-- Moderation buttons for one Q&A post. Expects $post and $type (question|answer). --}}
<form method="POST" action="{{ route('admin.qa.restore', [$type, $post->id]) }}" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-sm btn-success">{{ $post->isVisible() ? 'Keep' : 'Restore' }}</button>
</form>
@if ($post->isVisible())
    <form method="POST" action="{{ route('admin.qa.hide', [$type, $post->id]) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-secondary">Hide</button>
    </form>
@endif
<form method="POST" action="{{ route('admin.qa.destroy', [$type, $post->id]) }}" class="d-inline" onsubmit="return confirm('Delete this {{ $type }} for good?{{ $type === 'question' ? ' Its answers go too.' : '' }}')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
</form>
