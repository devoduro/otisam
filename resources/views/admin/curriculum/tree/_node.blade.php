{{-- One tree item and, recursively, its children. Expects $node, $version, $subject, $canManage. --}}
<li id="node-{{ $node->id }}" class="curriculum-node">
    <div class="d-flex flex-wrap align-items-start gap-2 py-2 border-bottom">
        <div class="flex-grow-1" style="min-width:0">
            <span class="badge text-bg-light border me-1">{{ $node->typeLabel() }}</span>
            @if ($node->code)<span class="text-muted small me-1">{{ $node->code }}</span>@endif
            <span class="fw-semibold">{{ $node->title }}</span>
            @if ($node->classLevel)<span class="badge text-bg-primary ms-1">{{ $node->classLevel->name }}</span>@endif
            @if ($node->is_official)
                <span class="badge text-bg-success ms-1" title="{{ $node->source_ref }}">official</span>
            @else
                <span class="badge text-bg-secondary ms-1">supplementary</span>
            @endif
            @if ($node->statement)<div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($node->statement, 220) }}</div>@endif
        </div>
        @if ($canManage)
            <div class="d-flex gap-1 flex-shrink-0">
                <form method="POST" action="{{ route('admin.curriculum.tree.shift', [$version, $subject, $node]) }}">
                    @csrf <input type="hidden" name="direction" value="up">
                    <button class="btn btn-sm btn-light" type="submit" aria-label="Move {{ $node->title }} up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></button>
                </form>
                <form method="POST" action="{{ route('admin.curriculum.tree.shift', [$version, $subject, $node]) }}">
                    @csrf <input type="hidden" name="direction" value="down">
                    <button class="btn btn-sm btn-light" type="submit" aria-label="Move {{ $node->title }} down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></button>
                </form>
                <a href="{{ route('admin.curriculum.tree.edit', [$version, $subject, $node]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.curriculum.tree.destroy', [$version, $subject, $node]) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $node->title }}"
                            data-confirm="Delete &quot;{{ $node->title }}&quot;{{ $node->tree_children->isNotEmpty() ? ' and everything under it' : '' }}?"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                </form>
            </div>
        @endif
    </div>
    @if ($node->tree_children->isNotEmpty())
        <ul class="list-unstyled ms-3 ms-md-4 ps-2 border-start">
            @foreach ($node->tree_children as $child)
                @include('admin.curriculum.tree._node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
