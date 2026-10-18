<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\EducationLevel;
use App\Models\Videos\VideoLesson;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Teachers add their own YouTube tutorials. Teachers an administrator has trusted with
 * "teach.publish-tutorials" publish straight away; everyone else's videos wait for review.
 * Published videos appear in Video lessons and on the teacher's public profile.
 */
class VideoController extends Controller
{
    public function index(Request $request): View
    {
        return view('teach.videos.index', [
            'videos' => VideoLesson::where('created_by', $request->user()->id)->with('subject.educationLevel', 'classLevel')->latest()->get(),
            'canPublish' => $request->user()->can('teach.publish-tutorials'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('teach.videos.form', $this->formData($request, new VideoLesson(['access_level' => 'free'])));
    }

    public function store(Request $request): RedirectResponse
    {
        $video = new VideoLesson($this->validated($request));
        $video->instructor = $request->user()->name;
        $video->access_level = 'free';
        $video->created_by = $request->user()->id;
        $this->setStatus($request, $video);
        $video->save();

        Audit::record('content', 'video.submitted', "Teacher video added: {$video->title}", $video);

        return redirect()->route('teach.videos.index')->with('success', $video->status === 'published'
            ? 'Published. It is in Video lessons and on your profile.'
            : 'Sent for review. It will appear once an administrator approves it.');
    }

    public function edit(Request $request, VideoLesson $video): View
    {
        $this->authorizeOwner($request, $video);

        return view('teach.videos.form', $this->formData($request, $video));
    }

    public function update(Request $request, VideoLesson $video): RedirectResponse
    {
        $this->authorizeOwner($request, $video);
        $video->fill($this->validated($request, $video));
        if ($video->isDirty()) {
            $this->setStatus($request, $video);
        }
        $video->save();

        return redirect()->route('teach.videos.index')->with('success', $video->status === 'published' ? 'Saved.' : 'Saved and sent for review.');
    }

    public function destroy(Request $request, VideoLesson $video): RedirectResponse
    {
        $this->authorizeOwner($request, $video);
        $video->delete();

        return redirect()->route('teach.videos.index')->with('success', 'Removed.');
    }

    private function setStatus(Request $request, VideoLesson $video): void
    {
        $video->status = $request->user()->can('teach.publish-tutorials') ? 'published' : 'pending';
        if ($video->status === 'published' && ! $video->published_at) {
            $video->published_at = now();
        }
    }

    private function authorizeOwner(Request $request, VideoLesson $video): void
    {
        abort_unless($video->created_by === $request->user()->id, 404);
    }

    private function validated(Request $request, ?VideoLesson $existing = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'youtube' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'subject_id' => ['required', 'integer', Rule::exists('subjects', 'id')->where('is_active', true)],
            'class_level_id' => ['nullable', 'integer', 'exists:class_levels,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
        ]);

        $youtubeId = VideoLesson::parseYoutubeId($data['youtube']);
        if (! $youtubeId) {
            throw ValidationException::withMessages(['youtube' => 'Paste a YouTube link (youtube.com/watch?v=…, youtu.be/…) or the 11-character video id.']);
        }
        if (VideoLesson::where('youtube_id', $youtubeId)->when($existing, fn ($q) => $q->whereKeyNot($existing->id))->exists()) {
            throw ValidationException::withMessages(['youtube' => 'This video is already on '.config('app.name').'.']);
        }
        unset($data['youtube']);

        return $data + ['youtube_id' => $youtubeId];
    }

    private function formData(Request $request, VideoLesson $video): array
    {
        return [
            'video' => $video,
            'levels' => EducationLevel::active()->ordered()->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort'), 'classLevels'])->get(),
            'canPublish' => $request->user()->can('teach.publish-tutorials'),
        ];
    }
}
