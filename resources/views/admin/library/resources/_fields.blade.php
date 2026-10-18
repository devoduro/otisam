{{-- Resource details shared by the single and bulk forms. Expects $resource, $categories, $examTypes, $levels, $versions; $bulk (bool). --}}
@php $bulk = $bulk ?? false; @endphp
<div class="row g-3">
    @unless ($bulk)
        <div class="col-12">
            <label for="title" class="form-label fw-semibold">Title</label>
            <input id="title" name="title" value="{{ old('title', $resource->title) }}" class="form-control" required maxlength="200" placeholder="e.g. Core Mathematics Paper 2: worked solutions">
        </div>
    @endunless
    <div class="col-md-6">
        <label for="category_id" class="form-label fw-semibold">Category</label>
        <select id="category_id" name="category_id" class="form-select" required>
            <option value="">Choose…</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((int) old('category_id', $resource->category_id) === $c->id)>{{ $c->name }}{{ $c->is_active ? '' : ' (inactive)' }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="audience" class="form-label fw-semibold">For</label>
        <select id="audience" name="audience" class="form-select" required>
            @foreach (\App\Models\Library\ResourceCategory::AUDIENCES as $v => $l)
                <option value="{{ $v }}" @selected(old('audience', $resource->audience) === $v)>{{ $l }}</option>
            @endforeach
        </select>
        <div class="form-text">Teacher-only items can be opened in full only by approved teachers.</div>
    </div>
    <div class="col-md-6">
        <label for="subject_id" class="form-label fw-semibold">Subject</label>
        <select id="subject_id" name="subject_id" class="form-select">
            <option value="">None / general</option>
            @foreach ($levels as $level)
                <optgroup label="{{ $level->name }}">
                    @foreach ($level->subjects as $s)<option value="{{ $s->id }}" @selected((int) old('subject_id', $resource->subject_id) === $s->id)>{{ $s->name }}</option>@endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label for="class_level_id" class="form-label fw-semibold">Class <span class="text-muted fw-normal">(optional)</span></label>
        <select id="class_level_id" name="class_level_id" class="form-select">
            <option value="">Any</option>
            @foreach ($levels as $level)
                <optgroup label="{{ $level->code }}">
                    @foreach ($level->classLevels as $cl)<option value="{{ $cl->id }}" @selected((int) old('class_level_id', $resource->class_level_id) === $cl->id)>{{ $cl->name }}</option>@endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label for="programme_id" class="form-label fw-semibold">Programme <span class="text-muted fw-normal">(optional)</span></label>
        <select id="programme_id" name="programme_id" class="form-select">
            <option value="">Any</option>
            @foreach ($levels as $level)
                <optgroup label="{{ $level->code }}">
                    @foreach ($level->programmes as $p)<option value="{{ $p->id }}" @selected((int) old('programme_id', $resource->programme_id) === $p->id)>{{ $p->name }}</option>@endforeach
                </optgroup>
            @endforeach
        </select>
    </div>

    <div class="col-12"><hr class="my-1"><span class="small fw-bold text-uppercase text-muted">Examination details (for past papers, mocks and their solutions)</span></div>
    <div class="col-md-5">
        <label for="exam_type_id" class="form-label fw-semibold">Exam</label>
        <select id="exam_type_id" name="exam_type_id" class="form-select">
            <option value="">Not an exam paper</option>
            @foreach ($examTypes as $e)<option value="{{ $e->id }}" @selected((int) old('exam_type_id', $resource->exam_type_id) === $e->id)>{{ $e->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label for="exam_year" class="form-label fw-semibold">Year</label>
        <input id="exam_year" type="number" name="exam_year" value="{{ old('exam_year', $resource->exam_year) }}" class="form-control" min="1950" max="{{ now()->year + 1 }}">
    </div>
    <div class="col-6 col-md-4">
        <label for="paper_number" class="form-label fw-semibold">Paper</label>
        <input id="paper_number" name="paper_number" value="{{ old('paper_number', $resource->paper_number) }}" class="form-control" maxlength="20" placeholder="e.g. 1, 2, 2A">
    </div>

    <div class="col-12"><hr class="my-1"><span class="small fw-bold text-uppercase text-muted">Access</span></div>
    <div class="col-md-4">
        <label for="access_level" class="form-label fw-semibold">Access</label>
        <select id="access_level" name="access_level" class="form-select" required>
            @foreach (\App\Models\Library\Resource::ACCESS_LEVELS as $v => $l)<option value="{{ $v }}" @selected(old('access_level', $resource->access_level) === $v)>{{ $l }}</option>@endforeach
        </select>
        <div class="form-text">WAEC/BECE papers can only be free. Premium is for your own solutions and material.</div>
    </div>
    <div class="col-md-4">
        <label for="preview_pages" class="form-label fw-semibold">Preview pages</label>
        <input id="preview_pages" type="number" name="preview_pages" value="{{ old('preview_pages', $resource->preview_pages) }}" class="form-control" min="1" max="20" placeholder="Default: {{ $defaultPreviewPages }}">
        <div class="form-text">Never more than half the document.</div>
    </div>
    <div class="col-md-4">
        <label for="curriculum_version_id" class="form-label fw-semibold">Curriculum version <span class="text-muted fw-normal">(optional)</span></label>
        <select id="curriculum_version_id" name="curriculum_version_id" class="form-select">
            <option value="">Not specific</option>
            @foreach ($versions as $v)<option value="{{ $v->id }}" @selected((int) old('curriculum_version_id', $resource->curriculum_version_id) === $v->id)>{{ $v->educationLevel->code }}: {{ $v->name }}</option>@endforeach
        </select>
    </div>

    <div class="col-12"><hr class="my-1"><span class="small fw-bold text-uppercase text-muted">Copyright</span></div>
    <div class="col-md-4">
        <label for="copyright_holder" class="form-label fw-semibold">Copyright holder</label>
        <input id="copyright_holder" name="copyright_holder" value="{{ old('copyright_holder', $resource->copyright_holder) }}" class="form-control" maxlength="200" placeholder="e.g. {{ config('app.name') }}, WAEC, author name">
        <div class="form-text">Required before publishing.</div>
    </div>
    <div class="col-md-4">
        <label for="licence" class="form-label fw-semibold">Licence / basis <span class="text-muted fw-normal">(optional)</span></label>
        <input id="licence" name="licence" value="{{ old('licence', $resource->licence) }}" class="form-control" maxlength="120" placeholder="e.g. Own work, Written permission">
    </div>
    <div class="col-md-4">
        <label for="permission_reference" class="form-label fw-semibold">Permission reference <span class="text-muted fw-normal">(optional)</span></label>
        <input id="permission_reference" name="permission_reference" value="{{ old('permission_reference', $resource->permission_reference) }}" class="form-control" maxlength="255" placeholder="Letter/contract reference">
    </div>

    @unless ($bulk)
        <div class="col-12">
            <label for="description" class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="5000" class="form-control">{{ old('description', $resource->description) }}</textarea>
        </div>
    @endunless
</div>
