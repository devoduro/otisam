<?php

namespace App\Http\Controllers\Admin\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgrammeController extends Controller
{
    public function index(): View
    {
        $levels = EducationLevel::ordered()
            ->with(['programmes' => fn ($q) => $q->withCount('subjects')])
            ->get();

        return view('admin.curriculum.programmes.index', compact('levels'));
    }

    public function create(Request $request): View
    {
        $programme = new Programme(['education_level_id' => $request->integer('level') ?: EducationLevel::ordered()->value('id'), 'is_active' => true]);

        return $this->form($programme);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $request) {
            $programme = Programme::create($data);
            $programme->subjects()->sync($this->subjectSync($request, $programme));
        });

        return redirect()->route('admin.curriculum.programmes.index')->with('success', "Programme \"{$data['name']}\" added.");
    }

    public function edit(Programme $programme): View
    {
        return $this->form($programme->load('subjects'));
    }

    public function update(Request $request, Programme $programme): RedirectResponse
    {
        $data = $this->validated($request, $programme);

        DB::transaction(function () use ($programme, $data, $request) {
            $programme->update($data);
            $programme->subjects()->sync($this->subjectSync($request, $programme));
        });

        return redirect()->route('admin.curriculum.programmes.index')->with('success', "Programme \"{$programme->name}\" saved.");
    }

    public function destroy(Programme $programme): RedirectResponse
    {
        if (StudentProfile::where('programme_id', $programme->id)->exists()) {
            return back()->with('error', "Students are registered on {$programme->name}. Deactivate it instead of deleting it.");
        }

        $programme->delete();

        return redirect()->route('admin.curriculum.programmes.index')->with('success', "Programme \"{$programme->name}\" deleted.");
    }

    private function form(Programme $programme): View
    {
        $levels = EducationLevel::ordered()->with('subjects')->get();
        $selected = $programme->exists ? $programme->subjects->mapWithKeys(fn ($s) => [$s->id => (bool) $s->pivot->is_core])->all() : [];

        return view('admin.curriculum.programmes.form', compact('programme', 'levels', 'selected'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Programme $programme = null): array
    {
        $data = $request->validate([
            'education_level_id' => ['required', 'exists:education_levels,id'],
            'name' => ['required', 'string', 'max:80', Rule::unique('programmes', 'name')->where('education_level_id', $request->input('education_level_id'))->ignore($programme?->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'subjects' => ['array'],
            'subjects.*' => ['nullable', 'in:core,elective'],
        ]);

        return [
            'education_level_id' => (int) $data['education_level_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * subjects[<id>] = core|elective. Only subjects of the programme's own level are kept.
     *
     * @return array<int, array{is_core: bool}>
     */
    private function subjectSync(Request $request, Programme $programme): array
    {
        // "Not taken" arrives as an empty value.
        $chosen = array_filter((array) $request->input('subjects', []), fn ($v) => in_array($v, ['core', 'elective'], true));
        $allowed = Subject::where('education_level_id', $programme->education_level_id)
            ->whereIn('id', array_keys($chosen))->pluck('id');

        return $allowed->mapWithKeys(fn ($id) => [$id => ['is_core' => $chosen[$id] === 'core']])->all();
    }
}
