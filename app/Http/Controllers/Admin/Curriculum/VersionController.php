<?php

namespace App\Http\Controllers\Admin\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VersionController extends Controller
{
    public function index(): View
    {
        $levels = EducationLevel::ordered()->with(['versions' => fn ($q) => $q->currentFirst()->withCount('nodes')])->get();

        return view('admin.curriculum.versions.index', compact('levels'));
    }

    public function show(CurriculumVersion $version): View
    {
        $version->load('educationLevel');
        $subjects = $version->educationLevel->subjects()
            ->withCount(['nodes' => fn ($q) => $q->where('curriculum_version_id', $version->id)])
            ->get();

        return view('admin.curriculum.versions.show', compact('version', 'subjects'));
    }

    public function create(Request $request): View
    {
        return view('admin.curriculum.versions.form', [
            'version' => new CurriculumVersion(['education_level_id' => $request->integer('level') ?: EducationLevel::ordered()->value('id'), 'status' => 'draft']),
            'levels' => EducationLevel::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $version = CurriculumVersion::create($this->validated($request));
        Audit::record('curriculum', 'version.created', "Curriculum version created: {$version->name}", $version, null, ['status' => $version->status]);

        return redirect()->route('admin.curriculum.versions.show', $version)->with('success', "Version \"{$version->name}\" created. Add each subject's topics next.");
    }

    public function edit(CurriculumVersion $version): View
    {
        return view('admin.curriculum.versions.form', ['version' => $version, 'levels' => EducationLevel::ordered()->get()]);
    }

    public function update(Request $request, CurriculumVersion $version): RedirectResponse
    {
        $data = $this->validated($request, $version);

        if ($data['education_level_id'] !== $version->education_level_id && $version->nodes()->exists()) {
            return back()->withInput()->with('error', 'This version already has topics, so its education level cannot change.');
        }

        $old = $version->only(['status', 'effective_from', 'effective_to']);
        $version->update($data);

        if ($old['status'] !== $version->status) {
            // Publishing or retiring a version changes what every learner sees: record it explicitly.
            Audit::record('curriculum', 'version.status', "Curriculum version {$version->name}: {$old['status']} → {$version->status}", $version, ['status' => $old['status']], ['status' => $version->status]);
        }

        return redirect()->route('admin.curriculum.versions.show', $version)->with('success', "Version \"{$version->name}\" saved.");
    }

    public function destroy(CurriculumVersion $version): RedirectResponse
    {
        if (CurriculumNode::where('curriculum_version_id', $version->id)->exists()) {
            return back()->with('error', 'This version has topics. Retire it instead, so the history is kept.');
        }

        $version->delete();

        return redirect()->route('admin.curriculum.versions.index')->with('success', "Version \"{$version->name}\" deleted.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?CurriculumVersion $version = null): array
    {
        $data = $request->validate([
            'education_level_id' => ['required', 'exists:education_levels,id'],
            'name' => ['required', 'string', 'max:120', Rule::unique('curriculum_versions', 'name')->where('education_level_id', $request->input('education_level_id'))->ignore($version?->id)],
            'status' => ['required', Rule::in(array_keys(CurriculumVersion::STATUSES))],
            'effective_from' => ['nullable', 'date', 'required_if:status,active'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'source_document' => ['nullable', 'string', 'max:500', 'required_if:status,active'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'effective_from.required_if' => 'An active version needs the date it takes effect.',
            'source_document.required_if' => 'An active version needs the official document it was entered from.',
        ]);

        $data['education_level_id'] = (int) $data['education_level_id'];

        return $data;
    }
}
