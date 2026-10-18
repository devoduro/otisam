<?php

namespace App\Http\Controllers\Admin\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'level' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $subjects = Subject::with('educationLevel')
            ->withCount(['programmes', 'nodes'])
            ->when($filters['level'] ?? null, fn ($q, $level) => $q->where('education_level_id', $level))
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%")))
            ->orderBy('education_level_id')->orderBy('sort')->orderBy('name')
            ->paginate(50)->withQueryString();

        return view('admin.curriculum.subjects.index', [
            'subjects' => $subjects,
            'filters' => $filters,
            'levels' => EducationLevel::ordered()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.curriculum.subjects.form', [
            'subject' => new Subject(['education_level_id' => $request->integer('level') ?: EducationLevel::ordered()->value('id'), 'category' => 'elective', 'is_active' => true]),
            'levels' => EducationLevel::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $subject = Subject::create($this->validated($request));

        return redirect()->route('admin.curriculum.subjects.index', ['level' => $subject->education_level_id])->with('success', "Subject \"{$subject->name}\" added.");
    }

    public function edit(Subject $subject): View
    {
        return view('admin.curriculum.subjects.form', ['subject' => $subject, 'levels' => EducationLevel::ordered()->get()]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $this->validated($request, $subject);

        if ($data['education_level_id'] !== $subject->education_level_id && $subject->nodes()->exists()) {
            return back()->withInput()->with('error', 'This subject already has curriculum topics, so its education level cannot change.');
        }

        $subject->update($data);

        return redirect()->route('admin.curriculum.subjects.index', ['level' => $subject->education_level_id])->with('success', "Subject \"{$subject->name}\" saved.");
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if (CurriculumNode::where('subject_id', $subject->id)->exists()) {
            return back()->with('error', "{$subject->name} has curriculum topics. Delete those first, or deactivate the subject instead.");
        }

        $subject->delete();

        return redirect()->route('admin.curriculum.subjects.index')->with('success', "Subject \"{$subject->name}\" deleted.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Subject $subject = null): array
    {
        $data = $request->validate([
            'education_level_id' => ['required', 'exists:education_levels,id'],
            'name' => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')->where('education_level_id', $request->input('education_level_id'))->ignore($subject?->id)],
            'code' => ['nullable', 'alpha_dash', 'max:20', Rule::unique('subjects', 'code')->ignore($subject?->id)],
            'category' => ['required', Rule::in(array_keys(Subject::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:500'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        return [
            'education_level_id' => (int) $data['education_level_id'],
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
