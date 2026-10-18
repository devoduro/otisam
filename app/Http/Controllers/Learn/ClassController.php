<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Students join their teachers' classes with a code and see the work set for them. */
class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $classrooms = $user->classrooms()->with(['teacher', 'subject'])->orderByDesc('classroom_student.joined_at')->get();

        $assignments = Assignment::published()
            ->whereIn('classroom_id', $classrooms->pluck('id'))
            ->with('classroom')
            ->orderByRaw('due_at is null')->orderBy('due_at')->latest('published_at')->get();
        $submissions = Submission::where('student_id', $user->id)->whereIn('assignment_id', $assignments->pluck('id'))->get()->keyBy('assignment_id');

        return view('learn.classes.index', compact('classrooms', 'assignments', 'submissions'));
    }

    public function join(Request $request): RedirectResponse
    {
        $code = Classroom::normaliseCode($request->validate(['code' => ['required', 'string', 'max:12']])['code']);
        $classroom = Classroom::where('join_code', $code)->first();

        if (! $classroom || $classroom->isArchived() || ! $classroom->joining_open) {
            throw ValidationException::withMessages(['code' => 'That class code is not valid, or the class is not taking new students. Check the code with your teacher.']);
        }
        if ($classroom->hasStudent($request->user())) {
            return redirect()->route('learn.classes.show', $classroom)->with('success', "You are already in {$classroom->name}.");
        }

        $classroom->students()->attach($request->user()->id, ['joined_at' => now()]);
        Audit::record('classroom', 'classroom.joined', "{$request->user()->name} joined {$classroom->name}", $classroom);

        return redirect()->route('learn.classes.show', $classroom)->with('success', "You joined {$classroom->name}.");
    }

    public function show(Request $request, Classroom $classroom): View
    {
        abort_unless($classroom->hasStudent($request->user()), 404);

        $assignments = $classroom->assignments()->published()->latest('published_at')->get();
        $submissions = Submission::where('student_id', $request->user()->id)->whereIn('assignment_id', $assignments->pluck('id'))->get()->keyBy('assignment_id');

        return view('learn.classes.show', ['classroom' => $classroom->load('teacher', 'subject', 'classLevel'), 'assignments' => $assignments, 'submissions' => $submissions]);
    }

    public function leave(Request $request, Classroom $classroom): RedirectResponse
    {
        abort_unless($classroom->hasStudent($request->user()), 404);
        $classroom->students()->detach($request->user()->id);

        return redirect()->route('learn.classes.index')->with('success', "You left {$classroom->name}.");
    }
}
