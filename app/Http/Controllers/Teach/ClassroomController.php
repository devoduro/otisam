<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Classroom;
use App\Models\Curriculum\EducationLevel;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        $classrooms = Classroom::where('teacher_id', $request->user()->id)
            ->with(['subject', 'classLevel'])
            ->withCount(['students', 'assignments' => fn ($q) => $q->whereIn('status', ['published', 'closed'])])
            ->orderByRaw('archived_at is not null')->latest()->get();

        return view('teach.classes.index', compact('classrooms'));
    }

    public function create(): View
    {
        return view('teach.classes.form', $this->formData(new Classroom(['joining_open' => true])));
    }

    public function store(Request $request): RedirectResponse
    {
        $classroom = new Classroom($this->validated($request));
        $classroom->teacher_id = $request->user()->id;
        $classroom->save();

        Audit::record('classroom', 'classroom.created', "Class created: {$classroom->name}", $classroom);

        return redirect()->route('teach.classes.show', $classroom)->with('success', "Class created. Give your students the join code {$classroom->join_code}.");
    }

    public function show(Request $request, Classroom $classroom): View
    {
        $this->authorizeOwner($request, $classroom);

        $classroom->load(['subject', 'classLevel', 'students']);
        $assignments = $classroom->assignments()->withCount(['submissions as submitted_count' => fn ($q) => $q->whereNotNull('submitted_at')])
            ->latest()->get();

        return view('teach.classes.show', compact('classroom', 'assignments'));
    }

    public function edit(Request $request, Classroom $classroom): View
    {
        $this->authorizeOwner($request, $classroom);

        return view('teach.classes.form', $this->formData($classroom));
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $this->authorizeOwner($request, $classroom);
        $classroom->update($this->validated($request));

        return redirect()->route('teach.classes.show', $classroom)->with('success', 'Class updated.');
    }

    public function archive(Request $request, Classroom $classroom): RedirectResponse
    {
        $this->authorizeOwner($request, $classroom);
        $classroom->forceFill(['archived_at' => $classroom->isArchived() ? null : now(), 'joining_open' => $classroom->isArchived()])->save();

        return redirect()->route('teach.classes.show', $classroom)->with('success', $classroom->isArchived() ? 'Class archived. Students can still see past work.' : 'Class restored.');
    }

    public function newCode(Request $request, Classroom $classroom): RedirectResponse
    {
        $this->authorizeOwner($request, $classroom);
        $classroom->forceFill(['join_code' => Classroom::newJoinCode()])->save();

        return back()->with('success', "New join code: {$classroom->join_code}. The old code no longer works.");
    }

    public function removeStudent(Request $request, Classroom $classroom, User $student): RedirectResponse
    {
        $this->authorizeOwner($request, $classroom);
        $classroom->students()->detach($student->id);
        Audit::record('classroom', 'classroom.student_removed', "Removed {$student->name} from {$classroom->name}", $classroom);

        return back()->with('success', "{$student->name} was removed from the class.");
    }

    public static function authorizeOwner(Request $request, Classroom $classroom): void
    {
        abort_unless($classroom->teacher_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->where('is_active', true)],
            'class_level_id' => ['nullable', 'integer', 'exists:class_levels,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'joining_open' => ['nullable', 'boolean'],
        ]);
        $data['joining_open'] = $request->boolean('joining_open');

        return $data;
    }

    private function formData(Classroom $classroom): array
    {
        return [
            'classroom' => $classroom,
            'levels' => EducationLevel::active()->ordered()->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort'), 'classLevels'])->get(),
        ];
    }
}
