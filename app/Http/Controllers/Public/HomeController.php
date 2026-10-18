<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Plan;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
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
        $data = Cache::remember('home.v1', now()->addMinutes(10), function () {
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
                ],
                'categories' => $categories,
                'subjects' => $subjects->each(fn ($s) => $s->setAttribute('resources_count', (int) ($resourceCounts[$s->id] ?? 0))),
                'cheapestPlan' => Plan::active()->where('price_minor', '>', 0)->orderBy('price_minor')->first(),
            ];
        });

        $data['latest'] = Resource::visible()->where('audience', '!=', 'teacher')
            ->with(['category', 'subject', 'examType', 'classLevel'])
            ->withCount(['linkedResources as solutions_count' => fn ($q) => $q->whereIn('resource_links.relation', ['solution', 'marking_scheme'])])
            ->latest('published_at')->limit(6)->get();

        return view('public.home', $data);
    }
}
