<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Videos\VideoLesson;
use App\Services\Audit;
use App\Services\Videos\YoutubeChannelImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

/** Staff management of video lessons (permission tutorials.manage). */
class VideoLessonController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(array_keys(VideoLesson::STATUSES))],
        ]);

        $videos = VideoLesson::with('subject')
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['subject'] ?? null, fn ($q, $s) => $q->where('subject_id', $s))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->latest('updated_at')->paginate(25)->withQueryString();

        return view('admin.videos.index', [
            'videos' => $videos,
            'filters' => $filters,
            'subjects' => Subject::with('educationLevel')->orderBy('education_level_id')->orderBy('name')->get(['id', 'name', 'education_level_id']),
        ]);
    }

    public function create(): View
    {
        return view('admin.videos.form', $this->formData(new VideoLesson(['access_level' => 'free'])));
    }

    public function store(Request $request): RedirectResponse
    {
        $video = new VideoLesson($this->validated($request));
        $video->status = 'draft';
        $video->created_by = $request->user()->id;
        $video->save();

        Audit::record('content', 'video.created', "Video lesson added: {$video->title}", $video);

        return redirect()->route('admin.videos.edit', $video)->with('success', 'Saved as a draft. Check the preview, then publish.');
    }

    /** Pull new uploads from the configured YouTube channels (same as the daily scheduled import). */
    public function importYoutube(YoutubeChannelImporter $importer): RedirectResponse
    {
        $created = 0;
        $source = 'rss';
        try {
            foreach (array_keys(config('platform.video_channels', [])) as $key) {
                $result = $importer->import($key);
                $created += $result['created'];
                $source = $result['source'];
            }
        } catch (RuntimeException $e) {
            return back()->with('error', 'YouTube import failed: '.$e->getMessage());
        }

        $note = $source === 'rss' ? ' Without a YouTube API key only the latest 15 uploads per channel are checked.' : '';

        return back()->with('success', ($created ? "{$created} new video lesson(s) imported and published." : 'No new videos on YouTube.').$note);
    }

    public function edit(VideoLesson $video): View
    {
        return view('admin.videos.form', $this->formData($video->load('subject', 'topic')));
    }

    public function update(Request $request, VideoLesson $video): RedirectResponse
    {
        $video->update($this->validated($request));

        return redirect()->route('admin.videos.edit', $video)->with('success', 'Saved.');
    }

    public function status(Request $request, VideoLesson $video): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', Rule::in(array_keys(VideoLesson::STATUSES))]])['status'];
        $old = $video->status;

        $video->status = $status;
        if ($status === 'published' && ! $video->published_at) {
            $video->published_at = now();
        }
        $video->save();

        Audit::record('content', "video.{$status}", 'Video lesson '.VideoLesson::STATUSES[$status].": {$video->title}", $video, ['status' => $old], ['status' => $status]);

        return back()->with('success', 'Status: '.VideoLesson::STATUSES[$status].'.');
    }

    public function destroy(VideoLesson $video): RedirectResponse
    {
        $video->delete();
        Audit::record('content', 'video.deleted', "Video lesson deleted: {$video->title}", $video);

        return redirect()->route('admin.videos.index')->with('success', "\"{$video->title}\" deleted.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'youtube' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'class_level_id' => ['nullable', 'integer', 'exists:class_levels,id'],
            'curriculum_node_id' => ['nullable', 'integer', 'exists:curriculum_nodes,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'instructor' => ['nullable', 'string', 'max:120'],
            'access_level' => ['required', Rule::in(['free', 'premium'])],
        ]);

        $youtubeId = VideoLesson::parseYoutubeId($data['youtube']);
        if (! $youtubeId) {
            throw ValidationException::withMessages(['youtube' => 'Paste a YouTube link (youtube.com/watch?v=…, youtu.be/…) or the 11-character video id.']);
        }

        // A topic must belong to the chosen subject.
        if (! empty($data['curriculum_node_id']) && CurriculumNode::find($data['curriculum_node_id'])?->subject_id !== (int) ($data['subject_id'] ?? 0)) {
            throw ValidationException::withMessages(['curriculum_node_id' => 'This topic belongs to a different subject.']);
        }

        unset($data['youtube']);

        return $data + ['youtube_id' => $youtubeId];
    }

    /** @return array<string, mixed> */
    private function formData(VideoLesson $video): array
    {
        return [
            'video' => $video,
            'levels' => EducationLevel::ordered()->with(['subjects', 'classLevels'])->get(),
            'topics' => $video->subject_id
                ? CurriculumNode::where('subject_id', $video->subject_id)->whereHas('version', fn ($v) => $v->published())
                    ->orderBy('path')->orderBy('sort')->get(['id', 'title', 'code', 'depth'])
                : collect(),
        ];
    }
}
