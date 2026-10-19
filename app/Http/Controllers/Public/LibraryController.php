<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Library\ExamType;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Models\Library\TakedownRequest;
use App\Models\QuestionBank\Question;
use App\Services\Library\ResourceAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function __construct(private readonly ResourceAccess $access) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:20'],
            'class' => ['nullable', 'integer'],
            'subject' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:90'],
            'exam' => ['nullable', 'integer'],
            'year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'access' => ['nullable', Rule::in(['free', 'premium'])],
        ]);

        $levels = EducationLevel::active()->ordered()->with([
            'classLevels',
            'subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort')->orderBy('name'),
        ])->get();
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? '')) ?? ($subject ? $levels->firstWhere('id', $subject->education_level_id) : null);
        $category = ! empty($filters['category']) ? ResourceCategory::where('slug', $filters['category'])->first() : null;

        $resources = Resource::visible()
            ->with(['category', 'subject', 'examType', 'classLevel'])
            ->withCount(['linkedResources as solutions_count' => fn ($q) => $q->whereIn('resource_links.relation', ['solution', 'marking_scheme'])])
            ->where('audience', '!=', 'teacher')
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($level && ! $subject, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('subject', fn ($s) => $s->where('education_level_id', $level->id))
                ->orWhereHas('classLevel', fn ($c) => $c->where('education_level_id', $level->id))))
            ->when($filters['class'] ?? null, fn ($q, $c) => $q->where('class_level_id', $c))
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->when($filters['exam'] ?? null, fn ($q, $e) => $q->where('exam_type_id', $e))
            ->when($filters['year'] ?? null, fn ($q, $y) => $q->where('exam_year', $y))
            ->when($filters['access'] ?? null, fn ($q, $a) => $q->where('access_level', $a))
            ->orderByDesc('exam_year')->orderByDesc('published_at')->orderBy('title')
            ->paginate(20)->withQueryString();

        return view('public.library.index', [
            'resources' => $resources,
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'categories' => ResourceCategory::active()->where('audience', '!=', 'teacher')->ordered()->get(),
            'examTypes' => ExamType::active()->ordered()->get(),
            'years' => self::examYears(),
            // Practice questions live in the question bank, not the library, so point to them.
            'practiceCount' => $subject ? Question::where('subject_id', $subject->id)->where('status', 'published')
                ->where('confidentiality', 'practice')->where('type', 'mcq')->whereNotNull('current_revision_id')->count() : 0,
            'subject' => $subject,
        ]);
    }

    /** Exam years to filter by: this year back to 1990 (WAEC has run the WASSCE since 1993, BECE since 1990). */
    public static function examYears(): array
    {
        return range((int) now()->year, 1990);
    }

    public function show(Request $request, Resource $resource): View
    {
        $user = $request->user();
        abort_unless($this->access->canSee($user, $resource), 404);

        $resource->load(['category', 'subject', 'examType', 'classLevel', 'programme', 'mainFile', 'previewFile',
            'curriculumNodes.version', 'linkedResources' => fn ($q) => $q->visible()->with('category'),
            'linkedFrom' => fn ($q) => $q->visible()->with('category')]);

        if ($resource->isVisible()) {
            Resource::whereKey($resource->id)->increment('view_count');
        }

        $related = Resource::visible()
            ->where('id', '!=', $resource->id)
            ->where('audience', $resource->audience)
            ->when($resource->subject_id, fn ($q) => $q->where('subject_id', $resource->subject_id))
            ->where('category_id', $resource->category_id)
            ->with('examType')
            ->orderByDesc('exam_year')->limit(6)->get();

        return view('public.library.show', [
            'resource' => $resource,
            'canViewFull' => $this->access->canViewFull($user, $resource),
            'canDownload' => $this->access->canDownload($user, $resource),
            'lockReason' => $this->access->lockReason($user, $resource),
            'related' => $related,
            // Curriculum tags only from versions learners can see.
            'topics' => $resource->curriculumNodes->filter(fn ($n) => $n->version?->isPublic()),
        ]);
    }

    public function copyright(Request $request): View
    {
        $resource = $request->filled('resource') ? Resource::visible()->where('slug', $request->string('resource'))->first() : null;

        return view('public.copyright', compact('resource'));
    }

    public function takedown(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'resource_slug' => ['nullable', 'string', 'max:220'],
            'resource_url' => ['nullable', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'rights_holder' => ['required', 'string', 'max:200'],
            'details' => ['required', 'string', 'min:20', 'max:5000'],
            'declaration' => ['accepted'],
        ], ['declaration.accepted' => 'Please confirm the statement about your rights.']);

        $takedown = new TakedownRequest($data);
        $takedown->resource_id = ! empty($data['resource_slug']) ? Resource::withTrashed()->where('slug', $data['resource_slug'])->value('id') : null;
        $takedown->ip_address = $request->ip();
        $takedown->save();

        return redirect()->route('copyright')->with('success', 'Thank you. Your request has been received and will be reviewed. We may contact you by email.');
    }
}
