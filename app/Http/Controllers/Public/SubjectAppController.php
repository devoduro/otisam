<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\SubjectApps;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Directory of Subject Specific App's free AI lesson-planning apps: pick a level, year and
 * subject and open that app on Playlab. The apps are a third party's; Otisam only links to them.
 */
class SubjectAppController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'level' => ['nullable', 'in:jhs,shs'],
            'year' => ['nullable', 'integer', 'in:1,2,3'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $apps = collect(SubjectApps::all())
            ->when($filters['level'] ?? null, fn ($c, $level) => $c->where('level', strtoupper($level)))
            ->when($filters['year'] ?? null, fn ($c, $year) => $c->filter(fn ($a) => $a['year'] === null || $a['year'] === (int) $year))
            ->when($filters['q'] ?? null, fn ($c, $q) => $c->filter(fn ($a) => str_contains(mb_strtolower($a['subject']), mb_strtolower($q))));

        return view('public.subject-apps', [
            'filters' => $filters,
            'groups' => $apps->groupBy(fn ($a) => $a['level'] === 'JHS' ? 'JHS 1–3' : 'SHS Year '.$a['year']),
            // For the quick picker: every SHS year's subjects, and the JHS subjects.
            'picker' => [
                'JHS' => collect(SubjectApps::JHS)->map(fn ($id) => SubjectApps::url($id)),
                'SHS 1' => collect(SubjectApps::SHS[1])->map(fn ($id) => SubjectApps::url($id)),
                'SHS 2' => collect(SubjectApps::SHS[2])->map(fn ($id) => SubjectApps::url($id)),
                'SHS 3' => collect(SubjectApps::SHS[3])->map(fn ($id) => SubjectApps::url($id)),
            ],
            'total' => $apps->count(),
        ]);
    }
}
