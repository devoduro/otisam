{{-- Shared fields for adding/editing a curriculum item. Expects $node (may be new), $parentOptions, $classLevels, $prefix. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $prefix }}parent_id" class="form-label small fw-semibold">Put it under</label>
        <select id="{{ $prefix }}parent_id" name="parent_id" class="form-select">
            <option value="">Top level of {{ $subject->name }}</option>
            @foreach ($parentOptions as $option)
                <option value="{{ $option['id'] }}" @selected((string) old('parent_id', $node->parent_id) === (string) $option['id'])>{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label for="{{ $prefix }}node_type" class="form-label small fw-semibold">Type</label>
        <select id="{{ $prefix }}node_type" name="node_type" class="form-select" required>
            @foreach (\App\Models\Curriculum\CurriculumNode::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('node_type', $node->node_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label for="{{ $prefix }}code" class="form-label small fw-semibold">Code <span class="text-muted fw-normal">(optional)</span></label>
        <input id="{{ $prefix }}code" name="code" value="{{ old('code', $node->code) }}" class="form-control" maxlength="40" placeholder="e.g. B1.2.1">
    </div>
    <div class="col-md-9">
        <label for="{{ $prefix }}title" class="form-label small fw-semibold">Title</label>
        <input id="{{ $prefix }}title" name="title" value="{{ old('title', $node->title) }}" class="form-control" required maxlength="255">
    </div>
    <div class="col-md-3">
        <label for="{{ $prefix }}class_level_id" class="form-label small fw-semibold">Class <span class="text-muted fw-normal">(optional)</span></label>
        <select id="{{ $prefix }}class_level_id" name="class_level_id" class="form-select">
            <option value="">Any</option>
            @foreach ($classLevels as $class)
                <option value="{{ $class->id }}" @selected((string) old('class_level_id', $node->class_level_id) === (string) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label for="{{ $prefix }}statement" class="form-label small fw-semibold">Statement / description <span class="text-muted fw-normal">(optional; copy official wording exactly)</span></label>
        <textarea id="{{ $prefix }}statement" name="statement" rows="2" maxlength="5000" class="form-control">{{ old('statement', $node->statement) }}</textarea>
    </div>
    <div class="col-md-4 d-flex align-items-center">
        <input type="hidden" name="is_official" value="0">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_official" value="1" id="{{ $prefix }}is_official" @checked(old('is_official', $node->is_official))>
            <label class="form-check-label small" for="{{ $prefix }}is_official">Official GES/NaCCA wording</label>
        </div>
    </div>
    <div class="col-md-8">
        <label for="{{ $prefix }}source_ref" class="form-label small fw-semibold">Where in the official document</label>
        <input id="{{ $prefix }}source_ref" name="source_ref" value="{{ old('source_ref', $node->source_ref) }}" class="form-control" maxlength="255" placeholder="Page or section (required for official items)">
    </div>
</div>
