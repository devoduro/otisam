<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Services\Classroom\PrivateFiles;
use App\Services\Library\ResourceAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Downloads: every published document that has a file, with a clear "can I download this?" state,
 * plus the signed-in user's own download history. Files still go through ResourceFileController.
 */
class DownloadController extends Controller
{
    public function index(Request $request, ResourceAccess $access): View
    {
        $user = $request->user();
        $filters = $request->validate([
            'source' => ['nullable', 'in:library,teachers'],
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:90'],
            'kind' => ['nullable', Rule::in(array_keys(TeacherMaterial::KINDS))],
        ]);
        $source = $filters['source'] ?? 'library';

        $levels = EducationLevel::active()->ordered()->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort')->orderBy('name')])->get();
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? '')) ?? ($subject ? $levels->firstWhere('id', $subject->education_level_id) : null);
        $category = ! empty($filters['category']) ? ResourceCategory::where('slug', $filters['category'])->first() : null;
        $levelSubjects = fn ($q) => $q->whereIn('subject_id', $level->subjects->pluck('id'));

        $resources = Resource::visible()->whereHas('mainFile')
            ->when(! $user?->isApprovedTeacher(), fn ($q) => $q->where('audience', '!=', 'teacher'))
            ->with(['category', 'subject', 'examType', 'mainFile'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($level && ! $subject, $levelSubjects)
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->orderByDesc('download_count')->orderByDesc('published_at')
            ->paginate(25, ['*'], 'page')->withQueryString();

        // Lesson materials teachers chose to make public.
        $teacherMaterials = TeacherMaterial::where('visibility', 'public')
            ->whereHas('owner.teacherProfile', fn ($q) => $q->where('verification_status', 'approved'))
            ->with(['owner.teacherProfile', 'subject', 'classLevel'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($level && ! $subject, $levelSubjects)
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            ->when($filters['kind'] ?? null, fn ($q, $k) => $q->where('kind', $k))
            ->latest()->paginate(25, ['*'], 'page')->withQueryString();

        $mine = collect();
        if ($user) {
            $lastDownloads = DB::table('resource_access_logs')->where('user_id', $user->id)->where('action', 'download')
                ->selectRaw('resource_id, MAX(created_at) as last_at')->groupBy('resource_id')->orderByDesc('last_at')->limit(20)->get();
            $byId = Resource::visible()->with(['mainFile', 'subject'])->whereIn('id', $lastDownloads->pluck('resource_id'))->get()->keyBy('id');
            $mine = $lastDownloads->map(fn ($row) => ($r = $byId->get($row->resource_id)) ? ['resource' => $r, 'at' => Carbon::parse($row->last_at)] : null)->filter()->values();
        }

        return view('public.downloads', [
            'source' => $source,
            'resources' => $resources,
            'teacherMaterials' => $teacherMaterials,
            'filters' => $filters,
            'mine' => $mine,
            'canDownload' => fn (Resource $r) => $access->canDownload($user, $r),
            'levels' => $levels,
            'level' => $level,
            'categories' => ResourceCategory::active()->where('audience', '!=', 'teacher')->ordered()->get(),
        ]);
    }

    /** A public teacher material. Like library files, downloading needs a (free) account. */
    public function material(Request $request, TeacherMaterial $material, PrivateFiles $files): StreamedResponse|RedirectResponse
    {
        abort_unless($material->visibility === 'public' && $material->owner?->isApprovedTeacher(), 404);
        if (! $request->user()) {
            return redirect()->guest(route('login'))->with('status', 'Sign in or create a free account to download teacher materials.');
        }
        if ($material->user_id !== $request->user()->id) {
            $material->increment('download_count');
        }

        return $files->download($material->file_path, $material->file_name, $material->file_mime);
    }
}
