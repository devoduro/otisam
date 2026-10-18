<?php

namespace App\Http\Controllers\Admin\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\EducationLevel;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Education levels and their classes, on one page. */
class LevelController extends Controller
{
    public function index(): View
    {
        $levels = EducationLevel::ordered()->with('classLevels')->withCount(['subjects', 'programmes', 'versions'])->get();

        return view('admin.curriculum.levels', compact('levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:education_levels,name'],
            'code' => ['required', 'alpha_dash', 'max:20', 'unique:education_levels,code'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        EducationLevel::create($data + ['sort' => 0, 'is_active' => true]);

        return back()->with('success', "Education level \"{$data['name']}\" added.");
    }

    public function update(Request $request, EducationLevel $level): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('education_levels', 'name')->ignore($level->id)],
            'code' => ['required', 'alpha_dash', 'max:20', Rule::unique('education_levels', 'code')->ignore($level->id)],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['boolean'],
        ]);

        $level->update(['sort' => $data['sort'] ?? 0, 'is_active' => $request->boolean('is_active')] + $data);

        return back()->with('success', "{$level->name} saved.");
    }

    public function storeClass(Request $request, EducationLevel $level): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:40', Rule::unique('class_levels', 'name')->where('education_level_id', $level->id)],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $level->classLevels()->create(['name' => $data['name'], 'sort' => $data['sort'] ?? ($level->classLevels()->max('sort') + 1)]);

        return back()->with('success', "Class \"{$data['name']}\" added to {$level->name}.");
    }

    public function updateClass(Request $request, ClassLevel $class): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:40', Rule::unique('class_levels', 'name')->where('education_level_id', $class->education_level_id)->ignore($class->id)],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['boolean'],
        ]);

        $class->update(['name' => $data['name'], 'sort' => $data['sort'] ?? 0, 'is_active' => $request->boolean('is_active')]);

        return back()->with('success', "Class \"{$class->name}\" saved.");
    }

    public function destroyClass(ClassLevel $class): RedirectResponse
    {
        if (StudentProfile::where('class_level_id', $class->id)->exists()) {
            return back()->with('error', "Students are registered in {$class->name}. Deactivate it instead of deleting it.");
        }

        $class->delete();

        return back()->with('success', "Class \"{$class->name}\" deleted.");
    }
}
