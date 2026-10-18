<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Videos\VideoLesson;
use App\Services\Audit;
use App\Services\Classroom\PrivateFiles;
use App\Services\Notifications\Notifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * One queue for what teachers put on the public site: YouTube tutorials waiting for review, and
 * lesson materials they made public (shown on Downloads and their profile without review).
 * Staff approve or reject videos and hide unsuitable materials; every decision is audited and
 * the teacher is told why.
 */
class ContentModerationController extends Controller
{
    public function __construct(private readonly Notifier $notifier) {}

    /** Teacher videos waiting for review (also counted for the sidebar badge). */
    public static function pendingVideos(): Builder
    {
        return VideoLesson::where('status', 'pending')
            ->whereHas('creator', fn ($q) => $q->where('account_type', AccountType::Teacher));
    }

    public function index(): View
    {
        return view('admin.moderation.index', [
            'videos' => self::pendingVideos()->with(['creator', 'subject.educationLevel', 'classLevel'])->oldest('updated_at')->get(),
            'materials' => TeacherMaterial::where('visibility', 'public')
                ->with(['owner', 'subject.educationLevel', 'classLevel'])
                ->latest()->paginate(25),
        ]);
    }

    public function approveVideo(VideoLesson $video): RedirectResponse
    {
        $this->ensurePending($video);

        $video->status = 'published';
        $video->published_at ??= now();
        $video->save();

        Audit::record('content', 'video.approved', "Teacher video approved: {$video->title}", $video, ['status' => 'pending'], ['status' => 'published']);

        $app = config('app.name');
        $this->notifier->toUser($video->creator, 'video.approved', 'transactional', "Your {$app} video is live",
            "Hello {$video->creator->name}, your video \"{$video->title}\" was approved. It is now in {$app} Video lessons and on your profile.",
            "video.approved:{$video->id}:".$video->updated_at->timestamp);

        return back()->with('success', "\"{$video->title}\" published.");
    }

    public function rejectVideo(Request $request, VideoLesson $video): RedirectResponse
    {
        $this->ensurePending($video);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']])['reason'];

        // Back to draft: when the teacher edits and saves it, it goes for review again.
        $video->status = 'draft';
        $video->save();

        Audit::record('content', 'video.rejected', "Teacher video rejected: {$video->title}", $video, ['status' => 'pending'], ['status' => 'draft', 'reason' => $reason]);

        $app = config('app.name');
        $this->notifier->toUser($video->creator, 'video.rejected', 'transactional', "Your {$app} video was not approved",
            "Hello {$video->creator->name}, your video \"{$video->title}\" was not approved. Reason: {$reason} You can edit it and send it for review again.",
            "video.rejected:{$video->id}:".$video->updated_at->timestamp);

        return back()->with('success', "\"{$video->title}\" rejected. The teacher has been told why.");
    }

    /** Staff reviewing a file is not counted as a download. */
    public function downloadMaterial(TeacherMaterial $material, PrivateFiles $files): StreamedResponse
    {
        return $files->download($material->file_path, $material->file_name, $material->file_mime);
    }

    public function hideMaterial(Request $request, TeacherMaterial $material): RedirectResponse
    {
        if ($material->visibility !== 'public') {
            return back()->with('error', "\"{$material->title}\" is no longer public.");
        }
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']])['reason'];

        $material->visibility = 'teachers';
        $material->save();

        Audit::record('content', 'material.hidden', "Teacher material taken off Downloads: {$material->title}", $material, ['visibility' => 'public'], ['visibility' => 'teachers', 'reason' => $reason]);

        $app = config('app.name');
        $this->notifier->toUser($material->owner, 'material.hidden', 'transactional', "Your {$app} material was taken off Downloads",
            "Hello {$material->owner->name}, your material \"{$material->title}\" was removed from {$app} Downloads and your public profile. Other teachers can still see it. Reason: {$reason}",
            "material.hidden:{$material->id}:".$material->updated_at->timestamp);

        return back()->with('success', "\"{$material->title}\" is now visible to teachers only.");
    }

    private function ensurePending(VideoLesson $video): void
    {
        abort_unless($video->status === 'pending' && $video->creator?->isTeacher(), 404);
    }
}
