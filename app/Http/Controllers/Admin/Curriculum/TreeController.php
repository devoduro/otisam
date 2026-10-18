<?php

namespace App\Http\Controllers\Admin\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use App\Services\Audit;
use App\Services\Curriculum\CurriculumImporter;
use App\Services\Curriculum\CurriculumTree;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/** The topic-tree editor for one subject within one curriculum version. */
class TreeController extends Controller
{
    public function __construct(private readonly CurriculumTree $tree) {}

    public function show(CurriculumVersion $version, Subject $subject): View
    {
        $this->assertPair($version, $subject);

        $roots = $this->tree->load($version, $subject);

        return view('admin.curriculum.tree.show', [
            'version' => $version->load('educationLevel'),
            'subject' => $subject,
            'roots' => $roots,
            'parentOptions' => $this->parentOptions($roots),
            'classLevels' => $version->educationLevel->classLevels,
            'total' => CurriculumNode::inTree($version->id, $subject->id)->count(),
        ]);
    }

    public function store(Request $request, CurriculumVersion $version, Subject $subject): RedirectResponse
    {
        $this->assertPair($version, $subject);
        $data = $this->validated($request, $version, $subject);

        $parent = $data['parent_id'] ? CurriculumNode::findOrFail($data['parent_id']) : null;

        try {
            $node = $this->tree->add($version, $subject, $parent, $data['attributes']);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to(route('admin.curriculum.tree.show', [$version, $subject])."#node-{$node->id}")
            ->with('success', "Added {$node->typeLabel()}: {$node->title}");
    }

    public function edit(CurriculumVersion $version, Subject $subject, CurriculumNode $node): View
    {
        $this->assertNode($version, $subject, $node);

        return view('admin.curriculum.tree.edit', [
            'version' => $version->load('educationLevel'),
            'subject' => $subject,
            'node' => $node,
            // A node cannot move under itself or its own descendants.
            'parentOptions' => array_filter($this->parentOptions($this->tree->load($version, $subject)), fn ($option) => $option['id'] !== $node->id && ! str_contains($option['path'], '/'.$node->id.'/')),
            'classLevels' => $version->educationLevel->classLevels,
        ]);
    }

    public function update(Request $request, CurriculumVersion $version, Subject $subject, CurriculumNode $node): RedirectResponse
    {
        $this->assertNode($version, $subject, $node);
        $data = $this->validated($request, $version, $subject, $node);

        try {
            $this->tree->update($node, $data['attributes'], $data['parent_id']);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to(route('admin.curriculum.tree.show', [$version, $subject])."#node-{$node->id}")->with('success', "Saved: {$node->title}");
    }

    public function shift(Request $request, CurriculumVersion $version, Subject $subject, CurriculumNode $node): RedirectResponse
    {
        $this->assertNode($version, $subject, $node);
        $this->tree->shift($node, $request->validate(['direction' => ['required', 'in:up,down']])['direction']);

        return redirect()->to(route('admin.curriculum.tree.show', [$version, $subject])."#node-{$node->id}");
    }

    public function destroy(CurriculumVersion $version, Subject $subject, CurriculumNode $node): RedirectResponse
    {
        $this->assertNode($version, $subject, $node);

        $title = $node->title;
        $count = $this->tree->delete($node);
        Audit::record('curriculum', 'node.deleted', "Deleted curriculum item \"{$title}\" ({$subject->name}, {$version->name}) and ".($count - 1).' item(s) under it');

        return redirect()->route('admin.curriculum.tree.show', [$version, $subject])->with('success', $count > 1 ? "Deleted \"{$title}\" and ".($count - 1).' item(s) under it.' : "Deleted \"{$title}\".");
    }

    public function importForm(CurriculumVersion $version, Subject $subject): View
    {
        $this->assertPair($version, $subject);

        return view('admin.curriculum.tree.import', ['version' => $version->load('educationLevel'), 'subject' => $subject, 'preview' => null]);
    }

    /** Dry run: validate the uploaded file and show what would be created. Nothing is written. */
    public function importPreview(Request $request, CurriculumVersion $version, Subject $subject, CurriculumImporter $importer): View|RedirectResponse
    {
        $this->assertPair($version, $subject);
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        // Keep the file privately until the admin confirms, then commit re-reads and re-validates it.
        $token = (string) Str::uuid();
        $request->file('file')->storeAs('curriculum-imports', "{$token}.csv", 'local');
        $request->session()->put("curriculum_import.{$token}", ['version' => $version->id, 'subject' => $subject->id]);

        $preview = $importer->validate(Storage::disk('local')->path("curriculum-imports/{$token}.csv"), $version, $subject);

        return view('admin.curriculum.tree.import', [
            'version' => $version->load('educationLevel'),
            'subject' => $subject,
            'preview' => $preview,
            'token' => $token,
        ]);
    }

    public function importCommit(Request $request, CurriculumVersion $version, Subject $subject, CurriculumImporter $importer): RedirectResponse
    {
        $this->assertPair($version, $subject);
        $token = $request->validate(['token' => ['required', 'uuid']])['token'];

        $owner = $request->session()->pull("curriculum_import.{$token}");
        $path = "curriculum-imports/{$token}.csv";

        if ($owner !== ['version' => $version->id, 'subject' => $subject->id] || ! Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.curriculum.tree.import', [$version, $subject])->with('error', 'That preview has expired. Please upload the file again.');
        }

        $result = $importer->commit(Storage::disk('local')->path($path), $version, $subject);
        Storage::disk('local')->delete($path);

        if ($result['errors']) {
            return redirect()->route('admin.curriculum.tree.import', [$version, $subject])->with('error', 'The tree changed since the preview and the file no longer validates. Nothing was imported; please upload it again.');
        }

        Audit::record('curriculum', 'tree.imported', "Imported {$result['created']} curriculum item(s) into {$subject->name} ({$version->name})", $version, null, ['subject' => $subject->name, 'created' => $result['created']]);

        return redirect()->route('admin.curriculum.tree.show', [$version, $subject])->with('success', "Imported {$result['created']} item(s).");
    }

    public function export(CurriculumVersion $version, Subject $subject, CurriculumImporter $importer): Response
    {
        $this->assertPair($version, $subject);
        $name = Str::slug("{$subject->name} {$version->name}").'.csv';

        return response($importer->export($version, $subject), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$name}\"",
        ]);
    }

    public function template(): Response
    {
        $rows = [
            CurriculumImporter::TEMPLATE_HEADERS,
            ['S1', '', 'strand', 'Example strand title', '', 'SHS 1', 'yes', 'Curriculum document p. 12'],
            ['S1.1', 'S1', 'sub_strand', 'Example sub-strand title', '', 'SHS 1', 'yes', 'p. 13'],
            ['S1.1.1', 'S1.1', 'indicator', 'Example indicator', 'Learners can …', 'SHS 1', 'yes', 'p. 13'],
        ];

        $out = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        rewind($out);

        return response(stream_get_contents($out), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="curriculum-import-template.csv"',
        ]);
    }

    /** @return array{parent_id: ?int, attributes: array<string, mixed>} */
    private function validated(Request $request, CurriculumVersion $version, Subject $subject, ?CurriculumNode $node = null): array
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer', Rule::exists('curriculum_nodes', 'id')->where('curriculum_version_id', $version->id)->where('subject_id', $subject->id)],
            'node_type' => ['required', Rule::in(array_keys(CurriculumNode::TYPES))],
            'code' => ['nullable', 'string', 'max:40', Rule::unique('curriculum_nodes', 'code')->where('curriculum_version_id', $version->id)->where('subject_id', $subject->id)->ignore($node?->id)],
            'title' => ['required', 'string', 'max:255'],
            'statement' => ['nullable', 'string', 'max:5000'],
            'class_level_id' => ['nullable', 'integer', Rule::exists('class_levels', 'id')->where('education_level_id', $version->education_level_id)],
            'is_official' => ['boolean'],
            'source_ref' => ['nullable', 'string', 'max:255', 'required_if_accepted:is_official'],
        ], ['source_ref.required_if_accepted' => 'Say where in the official document this comes from (page or section).']);

        return [
            'parent_id' => isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            'attributes' => [
                'node_type' => $data['node_type'],
                'code' => $data['code'] ?? null,
                'title' => $data['title'],
                'statement' => $data['statement'] ?? null,
                'class_level_id' => $data['class_level_id'] ?? null,
                'is_official' => $request->boolean('is_official'),
                'source_ref' => $data['source_ref'] ?? null,
            ],
        ];
    }

    /**
     * Flattened, indented list for the "Put it under" select.
     *
     * @return array<int, array{id: int, label: string, path: string}>
     */
    private function parentOptions($roots): array
    {
        $options = [];
        $walk = function ($nodes) use (&$walk, &$options) {
            foreach ($nodes as $n) {
                if ($n->depth >= CurriculumNode::MAX_DEPTH) {
                    continue; // nothing may go under the deepest level
                }
                $options[] = [
                    'id' => $n->id,
                    'label' => str_repeat('— ', $n->depth).($n->code ? "{$n->code} " : '').Str::limit($n->title, 70),
                    'path' => $n->path,
                ];
                $walk($n->tree_children);
            }
        };
        $walk($roots);

        return $options;
    }

    private function assertPair(CurriculumVersion $version, Subject $subject): void
    {
        abort_unless($version->education_level_id === $subject->education_level_id, 404);
    }

    private function assertNode(CurriculumVersion $version, Subject $subject, CurriculumNode $node): void
    {
        $this->assertPair($version, $subject);
        abort_unless($node->curriculum_version_id === $version->id && $node->subject_id === $subject->id, 404);
    }
}
