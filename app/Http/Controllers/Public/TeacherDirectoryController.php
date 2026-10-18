<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Classroom\TeacherMaterial;
use App\Models\TeacherProfile;
use App\Models\Videos\VideoLesson;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The public Teachers page: approved teachers who chose to be listed, with their school,
 * subjects, video tutorials and public lesson materials. Contact details are never shown.
 */
class TeacherDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:80'],
            'has' => ['nullable', 'in:videos,materials'],
            'sort' => ['nullable', 'in:newest,name,videos'],
        ]);

        $videoCounts = VideoLesson::visible()->whereNotNull('created_by')->selectRaw('created_by, count(*) n')->groupBy('created_by')->pluck('n', 'created_by');
        $materialCounts = TeacherMaterial::where('visibility', 'public')->selectRaw('user_id, count(*) n')->groupBy('user_id')->pluck('n', 'user_id');

        $teachers = TeacherProfile::listed()
            ->with('user:id,name')
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('school_name', 'like', "%{$s}%")
                ->orWhere('headline', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))))
            ->when($filters['region'] ?? null, fn ($q, $r) => $q->where('region', $r))
            ->when($filters['subject'] ?? null, fn ($q, $s) => $q->where('subjects_taught', 'like', "%{$s}%"))
            ->when(($filters['has'] ?? null) === 'videos', fn ($q) => $q->whereIn('user_id', $videoCounts->keys()))
            ->when(($filters['has'] ?? null) === 'materials', fn ($q) => $q->whereIn('user_id', $materialCounts->keys()))
            ->get();

        $teachers = match ($filters['sort'] ?? 'newest') {
            'name' => $teachers->sortBy(fn ($t) => $t->user->name),
            'videos' => $teachers->sortByDesc(fn ($t) => $videoCounts[$t->user_id] ?? 0),
            default => $teachers->sortByDesc('verified_at'),
        };

        // Subject suggestions for the filter: the subjects listed teachers say they teach.
        $subjects = TeacherProfile::listed()->pluck('subjects_taught')
            ->flatMap(fn ($s) => preg_split('/[,;\/]+/', (string) $s))->map(fn ($s) => trim($s))->filter()
            ->unique(fn ($s) => mb_strtolower($s))->sort()->values();

        return view('public.teachers.index', [
            'teachers' => $teachers->values(),
            'filters' => $filters,
            'regions' => config('platform.regions'),
            'subjects' => $subjects,
            'videoCounts' => $videoCounts,
            'materialCounts' => $materialCounts,
            'schools' => TeacherProfile::listed()->whereNotNull('school_name')->distinct()->count('school_name'),
        ]);
    }

    public function show(string $slug): View
    {
        $profile = TeacherProfile::listed()->where('slug', $slug)->with('user:id,name')->firstOrFail();

        return view('public.teachers.show', [
            'profile' => $profile,
            'teacher' => $profile->user,
            'videos' => VideoLesson::visible()->where('created_by', $profile->user_id)->with('subject')->latest('published_at')->get(),
            'materials' => TeacherMaterial::where('user_id', $profile->user_id)->where('visibility', 'public')->with('subject', 'classLevel')->latest()->get(),
            'colleagues' => $profile->school_name
                ? TeacherProfile::listed()->where('school_name', $profile->school_name)->whereKeyNot($profile->user_id)->with('user:id,name')->limit(6)->get()
                : collect(),
        ]);
    }
}
