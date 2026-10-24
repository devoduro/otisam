<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Admin\HomepageSettingsController;
use App\Http\Controllers\Controller;
use App\Models\Commerce\Plan;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\Question;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Landing page. Layout ideas from open-exam-prep.com (search-first hero, live numbers, browse by
 * category with counts) and examsghana.com (big category tiles); styling from jhs_portal.
 * All numbers are real counts; nothing is shown that the platform does not have.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $data = Cache::remember('home.v3', now()->addMinutes(10), function () {
            $visible = Resource::visible()->where('audience', '!=', 'teacher');

            $categories = ResourceCategory::active()->where('audience', '!=', 'teacher')->ordered()
                ->withCount(['resources' => fn ($q) => $q->visible()])
                ->get();

            $subjects = Subject::active()
                ->whereHas('educationLevel', fn ($q) => $q->where('is_active', true))
                ->withCount(['nodes' => fn ($q) => $q->whereHas('version', fn ($v) => $v->published())])
                ->orderByRaw("category = 'core' DESC")->orderBy('sort')->limit(12)->get();

            $resourceCounts = (clone $visible)->selectRaw('subject_id, COUNT(*) as n')->groupBy('subject_id')->pluck('n', 'subject_id');

            return [
                'stats' => [
                    'resources' => (clone $visible)->count(),
                    'free' => (clone $visible)->where('access_level', 'free')->count(),
                    'subjects' => Subject::active()->count(),
                    'topics' => CurriculumNode::whereHas('version', fn ($v) => $v->published())->count(),
                    // Everything a student can practise: published self-test questions and the papers built from them.
                    'questions' => Question::where('status', 'published')->where('confidentiality', 'practice')->whereIn('type', Question::PRACTICE_TYPES)->whereNotNull('current_revision_id')->count(),
                    'papers' => ExamPaper::visible()->count(),
                    'promotion' => Question::where('status', 'published')->where('confidentiality', 'promotion')->whereIn('type', Question::PRACTICE_TYPES)->whereNotNull('current_revision_id')->count(),
                ],
                // For the hero search: JHS (BECE) and SHS (WASSCE) subjects.
                'searchSubjects' => Subject::active()
                    ->whereHas('educationLevel', fn ($q) => $q->where('is_active', true)->whereIn('code', ['JHS', 'SHS']))
                    ->with('educationLevel:id,code')->orderBy('sort')->orderBy('name')->get(['id', 'slug', 'name', 'education_level_id'])
                    ->map(fn ($s) => ['slug' => $s->slug, 'name' => $s->name, 'level' => strtolower($s->educationLevel->code)])->values()->all(),
                'categories' => $categories,
                'subjects' => $subjects->each(fn ($s) => $s->setAttribute('resources_count', (int) ($resourceCounts[$s->id] ?? 0))),
                'cheapestPlan' => Plan::active()->where('price_minor', '>', 0)->orderBy('price_minor')->first(),
            ];
        });

        $data['latest'] = Resource::visible()->where('audience', '!=', 'teacher')
            ->with(['category', 'subject', 'examType', 'classLevel'])
            ->withCount(['linkedResources as solutions_count' => fn ($q) => $q->whereIn('resource_links.relation', ['solution', 'marking_scheme'])])
            ->latest('published_at')->limit(6)->get();

        $data['heroImage'] = HomepageSettingsController::heroImageUrl();
        $data['searchYears'] = range((int) now()->year, 1990);

        return view('public.home', $data);
    }
}
