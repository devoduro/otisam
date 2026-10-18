<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Services\Audit;
use App\Services\Classroom\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Lesson plans, notes and other materials teachers keep for themselves or share with colleagues. */
class MaterialController extends Controller
{
    public function __construct(private readonly PrivateFiles $files) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->validate([
            'scope' => ['nullable', 'in:shared,mine'],
            'q' => ['nullable', 'string', 'max:100'],
            'kind' => ['nullable', Rule::in(array_keys(TeacherMaterial::KINDS))],
            'subject' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer'],
        ]);
        $scope = $filters['scope'] ?? 'shared';
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;

        $materials = TeacherMaterial::query()
            ->with(['owner.teacherProfile', 'subject.educationLevel', 'classLevel'])
            ->when($scope === 'mine', fn ($q) => $q->where('user_id', $user->id), fn ($q) => $q->whereIn('visibility', ['teachers', 'public']))
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($filters['kind'] ?? null, fn ($q, $k) => $q->where('kind', $k))
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            ->when($filters['year'] ?? null, fn ($q, $y) => $q->where('class_level_id', $y))
            ->latest()->paginate(20)->withQueryString();

        return view('teach.materials.index', [
            'materials' => $materials,
            'filters' => $filters,
            'scope' => $scope,
            'levels' => $this->levels(),
            'mineCount' => TeacherMaterial::where('user_id', $user->id)->count(),
        ]);
    }

    public function create(): View
    {
        return view('teach.materials.form', ['material' => new TeacherMaterial(['visibility' => 'teachers', 'kind' => 'lesson_plan']), 'levels' => $this->levels()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $material = new TeacherMaterial($data);
        $material->user_id = $request->user()->id;
        $material->forceFill($this->files->store($request->file('file'), 'teacher-materials'));
        $material->save();

        Audit::record('content', 'material.uploaded', "Teacher material uploaded: {$material->title}", $material);

        return redirect()->route('teach.materials.index', ['scope' => 'mine'])->with('success', $material->isShared() ? 'Uploaded and shared with other teachers.' : 'Uploaded. Only you can see it.');
    }

    public function edit(Request $request, TeacherMaterial $material): View
    {
        $this->authorizeOwner($request, $material);

        return view('teach.materials.form', ['material' => $material, 'levels' => $this->levels()]);
    }

    public function update(Request $request, TeacherMaterial $material): RedirectResponse
    {
        $this->authorizeOwner($request, $material);
        $material->fill($this->validated($request, false));
        if ($request->hasFile('file')) {
            $old = $material->file_path;
            $material->forceFill($this->files->store($request->file('file'), 'teacher-materials'));
            $this->files->delete($old);
        }
        $material->save();

        return redirect()->route('teach.materials.index', ['scope' => 'mine'])->with('success', 'Saved.');
    }

    public function destroy(Request $request, TeacherMaterial $material): RedirectResponse
    {
        $this->authorizeOwner($request, $material);
        $material->delete();

        return redirect()->route('teach.materials.index', ['scope' => 'mine'])->with('success', 'Deleted.');
    }

    public function download(Request $request, TeacherMaterial $material): StreamedResponse
    {
        abort_unless($material->user_id === $request->user()->id || $material->isShared(), 404);
        if ($material->user_id !== $request->user()->id) {
            $material->increment('download_count');
        }

        return $this->files->download($material->file_path, $material->file_name, $material->file_mime);
    }

    private function authorizeOwner(Request $request, TeacherMaterial $material): void
    {
        abort_unless($material->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request, bool $creating): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'kind' => ['required', Rule::in(array_keys(TeacherMaterial::KINDS))],
            'description' => ['nullable', 'string', 'max:3000'],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->where('is_active', true)],
            'class_level_id' => ['nullable', 'integer', 'exists:class_levels,id'],
            'visibility' => ['required', Rule::in(array_keys(TeacherMaterial::VISIBILITY))],
            'file' => [$creating ? 'required' : 'nullable', 'file', 'max:'.(config('platform.library.max_upload_mb') * 1024)],
        ]);
        unset($data['file']);

        return $data;
    }

    private function levels()
    {
        return EducationLevel::active()->ordered()->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort'), 'classLevels'])->get();
    }
}
