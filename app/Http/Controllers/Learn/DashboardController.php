<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Assignment;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\User;
use App\Services\Access\Entitlements;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Entitlements $entitlements): View
    {
        $user = $request->user()->load('studentProfile.classLevel', 'studentProfile.programme.subjects');
        $profile = $user->studentProfile;

        // The student's subjects: their programme's, or the core subjects of their level.
        $subjects = $profile?->programme
            ? $profile->programme->subjects->where('is_active', true)->values()
            : Subject::active()->where('category', 'core')
                ->when($profile?->classLevel, fn ($q) => $q->where('education_level_id', $profile->classLevel->education_level_id))
                ->orderBy('sort')->get();

        $library = Resource::visible()->where('audience', '!=', 'teacher')->whereIn('subject_id', $subjects->pluck('id'));

        return view('learn.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'subscription' => $entitlements->currentSubscription($user),
            'subjects' => $subjects,
            'stats' => [
                'resources' => (clone $library)->count(),
                'free' => (clone $library)->where('access_level', 'free')->count(),
                'papers' => (clone $library)->whereNotNull('exam_year')->count(),
            ],
            'classwork' => $this->classwork($user),
            'latest' => (clone $library)->with(['category', 'subject', 'examType', 'classLevel'])->latest('published_at')->limit(6)->get(),
        ]);
    }

    /** @return array{classes: int, todo: int} */
    private function classwork(User $user): array
    {
        $classIds = $user->classrooms()->pluck('classrooms.id');

        return [
            'classes' => $classIds->count(),
            'todo' => Assignment::where('status', 'published')->whereIn('classroom_id', $classIds)
                ->whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $user->id)->whereNotNull('submitted_at'))
                ->count(),
        ];
    }
}
