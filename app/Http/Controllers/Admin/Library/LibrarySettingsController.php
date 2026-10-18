<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\ExamType;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Models\Setting;
use App\Services\Library\PdfPreviewGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Resource categories, exam types and the default preview length. */
class LibrarySettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.library.settings', [
            'categories' => ResourceCategory::ordered()->withCount('resources')->get(),
            'examTypes' => ExamType::ordered()->get(),
            'previewPages' => PdfPreviewGenerator::defaultPages(),
        ]);
    }

    public function updatePreview(Request $request): RedirectResponse
    {
        $data = $request->validate(['preview_pages' => ['required', 'integer', 'min:1', 'max:20']]);
        Setting::put('library.preview_pages', $data['preview_pages'], 'library');

        return back()->with('success', "Default preview length set to {$data['preview_pages']} page(s). It applies to previews generated from now on.");
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $this->categoryData($request);
        ResourceCategory::create($data + ['is_active' => true]);

        return back()->with('success', "Category \"{$data['name']}\" added.");
    }

    public function updateCategory(Request $request, ResourceCategory $category): RedirectResponse
    {
        $category->update($this->categoryData($request, $category) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', "Category \"{$category->name}\" saved.");
    }

    public function destroyCategory(ResourceCategory $category): RedirectResponse
    {
        if (Resource::withTrashed()->where('category_id', $category->id)->exists()) {
            return back()->with('error', "\"{$category->name}\" has resources. Deactivate it instead.");
        }
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function storeExamType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:exam_types,name'],
            'code' => ['required', 'alpha_dash', 'max:20', 'unique:exam_types,code'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        ExamType::create(['sort' => $data['sort'] ?? 0, 'is_active' => true] + $data);

        return back()->with('success', "Exam type \"{$data['name']}\" added.");
    }

    public function updateExamType(Request $request, ExamType $examType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('exam_types', 'name')->ignore($examType->id)],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        // The code is fixed: rules such as "WAEC papers are free only" look it up.
        $examType->update(['name' => $data['name'], 'sort' => $data['sort'] ?? 0, 'is_active' => $request->boolean('is_active')]);

        return back()->with('success', "Exam type \"{$examType->name}\" saved.");
    }

    /** @return array<string, mixed> */
    private function categoryData(Request $request, ?ResourceCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('resource_categories', 'name')->ignore($category?->id)],
            'audience' => ['required', Rule::in(array_keys(ResourceCategory::AUDIENCES))],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        return ['name' => $data['name'], 'audience' => $data['audience'], 'sort' => $data['sort'] ?? 0, 'is_exam_paper' => $request->boolean('is_exam_paper')];
    }
}
