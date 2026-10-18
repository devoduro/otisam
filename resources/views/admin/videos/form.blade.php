@extends('layouts.admin')

@section('title', $video->exists ? \Illuminate\Support\Str::limit($video->title, 60) : 'Add video lesson')

@section('content')
<div class="row g-3">
    <div class="col-xl-8">
        <form method="POST" action="{{ $video->exists ? route('admin.videos.update', $video) : route('admin.videos.store') }}" class="card-panel">
            @csrf
            @if ($video->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label fw-semibold">Title</label>
                    <input id="title" name="title" value="{{ old('title', $video->title) }}" class="form-control" required maxlength="200">
                </div>
                <div class="col-12">
                    <label for="youtube" class="form-label fw-semibold">YouTube link</label>
                    <input id="youtube" name="youtube" value="{{ old('youtube', $video->youtube_id ? 'https://www.youtube.com/watch?v='.$video->youtube_id : '') }}" class="form-control" required placeholder="https://www.youtube.com/watch?v=…">
                    <div class="form-text">The video is embedded from YouTube, never copied. Only add videos you own or may share. YouTube videos can be opened on YouTube by anyone with the link, so "premium" only hides the player here; set premium videos to Unlisted on YouTube.</div>
                </div>
                <div class="col-md-6">
                    <label for="subject_id" class="form-label fw-semibold">Subject</label>
                    <select id="subject_id" name="subject_id" class="form-select">
                        <option value="">General</option>
                        @foreach ($levels as $level)
                            <optgroup label="{{ $level->name }}">
                                @foreach ($level->subjects as $s)<option value="{{ $s->id }}" @selected((int) old('subject_id', $video->subject_id) === $s->id)>{{ $s->name }}</option>@endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="class_level_id" class="form-label fw-semibold">Class <span class="text-muted fw-normal">(optional)</span></label>
                    <select id="class_level_id" name="class_level_id" class="form-select">
                        <option value="">Any</option>
                        @foreach ($levels as $level)
                            <optgroup label="{{ $level->code }}">
                                @foreach ($level->classLevels as $c)<option value="{{ $c->id }}" @selected((int) old('class_level_id', $video->class_level_id) === $c->id)>{{ $c->name }}</option>@endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                @if ($topics->isNotEmpty())
                    <div class="col-12">
                        <label for="curriculum_node_id" class="form-label fw-semibold">Curriculum topic <span class="text-muted fw-normal">(optional)</span></label>
                        <select id="curriculum_node_id" name="curriculum_node_id" class="form-select">
                            <option value="">—</option>
                            @foreach ($topics as $t)<option value="{{ $t->id }}" @selected((int) old('curriculum_node_id', $video->curriculum_node_id) === $t->id)>{{ str_repeat('— ', $t->depth) }}{{ $t->code ? $t->code.' ' : '' }}{{ \Illuminate\Support\Str::limit($t->title, 80) }}</option>@endforeach
                        </select>
                    </div>
                @elseif ($video->exists && $video->subject_id)
                    <div class="col-12 small text-muted">This subject has no published curriculum topics yet.</div>
                @endif
                <div class="col-6 col-md-4">
                    <label for="access_level" class="form-label fw-semibold">Access</label>
                    <select id="access_level" name="access_level" class="form-select">
                        <option value="free" @selected(old('access_level', $video->access_level) === 'free')>Free</option>
                        <option value="premium" @selected(old('access_level', $video->access_level) === 'premium')>Premium</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label for="duration_minutes" class="form-label fw-semibold">Length (minutes)</label>
                    <input id="duration_minutes" type="number" name="duration_minutes" value="{{ old('duration_minutes', $video->duration_minutes) }}" class="form-control" min="1" max="600">
                </div>
                <div class="col-md-4">
                    <label for="instructor" class="form-label fw-semibold">Teacher / presenter</label>
                    <input id="instructor" name="instructor" value="{{ old('instructor', $video->instructor) }}" class="form-control" maxlength="120">
                </div>
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">What the lesson covers</label>
                    <textarea id="description" name="description" rows="4" class="form-control" maxlength="5000">{{ old('description', $video->description) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-premium" type="submit">{{ $video->exists ? 'Save changes' : 'Save draft' }}</button>
                <a href="{{ route('admin.videos.index') }}" class="btn btn-light">Back to list</a>
            </div>
        </form>
    </div>

    @if ($video->exists)
        <div class="col-xl-4">
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Preview</h2>
                <div class="ratio ratio-16x9 rounded overflow-hidden mb-2">
                    <iframe src="https://www.youtube-nocookie.com/embed/{{ $video->youtube_id }}?rel=0" title="{{ $video->title }}" allow="encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                </div>
                <p class="small text-muted mb-0">{{ number_format($video->view_count) }} views on {{ config('app.name') }}</p>
            </div>
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Status: {{ \App\Models\Videos\VideoLesson::STATUSES[$video->status] }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (['published' => ['Publish', 'btn-premium'], 'draft' => ['Back to draft', 'btn-outline-secondary'], 'archived' => ['Archive', 'btn-outline-dark']] as $status => [$label, $class])
                        @continue($video->status === $status)
                        <form method="POST" action="{{ route('admin.videos.status', $video) }}">@csrf<input type="hidden" name="status" value="{{ $status }}"><button class="btn btn-sm {{ $class }}" type="submit">{{ $label }}</button></form>
                    @endforeach
                </div>
                @if ($video->isVisible())<a href="{{ route('videos.show', $video) }}" target="_blank" rel="noopener" class="small d-block mt-2">Open public page</a>@endif
            </div>
            <div class="card-panel">
                <form method="POST" action="{{ route('admin.videos.destroy', $video) }}">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this video lesson?">Delete video</button></form>
            </div>
        </div>
    @endif
</div>
@endsection
