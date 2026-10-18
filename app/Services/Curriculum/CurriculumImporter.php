<?php

namespace App\Services\Curriculum;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Bulk entry of a subject's tree from a CSV file with the columns in TEMPLATE_HEADERS.
 *
 * Two passes: validate() checks every row and reports problems by line number without writing
 * anything; commit() re-validates and writes all rows in one transaction, or none.
 *
 * Rows refer to their parent by `parent_code`: either a code earlier in the same file or a code
 * already in the tree. Parents must therefore come before their children.
 */
class CurriculumImporter
{
    public const TEMPLATE_HEADERS = ['code', 'parent_code', 'type', 'title', 'statement', 'class', 'official', 'source_ref'];

    public const MAX_ROWS = 3000;

    public function __construct(private readonly CurriculumTree $tree) {}

    /**
     * @return array{rows: array<int, array<string, mixed>>, errors: array<int, string>, warnings: array<int, string>}
     *                                                                                                                 rows are keyed by CSV line number
     */
    public function validate(string $csvPath, CurriculumVersion $version, Subject $subject): array
    {
        $errors = [];
        $warnings = [];
        $rows = [];

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            return ['rows' => [], 'errors' => [0 => 'The file could not be read.'], 'warnings' => []];
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return ['rows' => [], 'errors' => [1 => 'The file is empty.'], 'warnings' => []];
        }

        $header = array_map(fn ($h) => Str::of((string) $h)->replace("\u{FEFF}", '')->trim()->lower()->replace(' ', '_')->toString(), $header);
        $missing = array_diff(['type', 'title'], $header);
        if ($missing) {
            fclose($handle);

            return ['rows' => [], 'errors' => [1 => 'Missing column(s): '.implode(', ', $missing).'. Download the template for the expected columns.'], 'warnings' => []];
        }

        $existingCodes = CurriculumNode::inTree($version->id, $subject->id)->whereNotNull('code')->pluck('depth', 'code')->all();
        $classLevels = ClassLevel::where('education_level_id', $version->education_level_id)->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id])->all();
        $typeKeys = array_keys(CurriculumNode::TYPES);
        $typeLabels = collect(CurriculumNode::TYPES)->mapWithKeys(fn ($label, $key) => [mb_strtolower($label) => $key])->all();

        $fileCodes = []; // code => depth
        $line = 1;

        while (($raw = fgetcsv($handle)) !== false) {
            $line++;

            if (count(array_filter($raw, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // blank line
            }
            if (count($rows) >= self::MAX_ROWS) {
                $errors[$line] = 'Too many rows: import at most '.self::MAX_ROWS.' at a time.';
                break;
            }

            $values = array_pad(array_map(fn ($v) => trim((string) $v), $raw), count($header), '');
            $r = array_combine($header, array_slice($values, 0, count($header)));
            $problems = [];

            $title = $r['title'] ?? '';
            if ($title === '') {
                $problems[] = 'title is required';
            } elseif (mb_strlen($title) > 255) {
                $problems[] = 'title is longer than 255 characters';
            }

            $typeInput = mb_strtolower($r['type'] ?? '');
            $type = in_array($typeInput, $typeKeys, true) ? $typeInput : ($typeLabels[$typeInput] ?? str_replace([' ', '-'], '_', $typeInput));
            if (! in_array($type, $typeKeys, true)) {
                $problems[] = "type \"{$r['type']}\" is not one of: ".implode(', ', $typeKeys);
            }

            $code = ($r['code'] ?? '') !== '' ? $r['code'] : null;
            if ($code !== null) {
                if (mb_strlen($code) > 40) {
                    $problems[] = 'code is longer than 40 characters';
                } elseif (isset($fileCodes[$code])) {
                    $problems[] = "code {$code} appears twice in this file";
                } elseif (array_key_exists($code, $existingCodes)) {
                    $problems[] = "code {$code} is already in this subject's tree";
                }
            }

            $parentCode = ($r['parent_code'] ?? '') !== '' ? $r['parent_code'] : null;
            $depth = 0;
            if ($parentCode !== null) {
                if (array_key_exists($parentCode, $fileCodes)) {
                    $depth = $fileCodes[$parentCode] + 1;
                } elseif (array_key_exists($parentCode, $existingCodes)) {
                    $depth = $existingCodes[$parentCode] + 1;
                } else {
                    $problems[] = "parent_code {$parentCode} was not found earlier in the file or in the tree";
                }
                if ($depth > CurriculumNode::MAX_DEPTH) {
                    $problems[] = 'too deep (at most '.(CurriculumNode::MAX_DEPTH + 1).' levels)';
                }
            }

            $classLevelId = null;
            if (($r['class'] ?? '') !== '') {
                $classLevelId = $classLevels[mb_strtolower($r['class'])] ?? null;
                if (! $classLevelId) {
                    $problems[] = "class \"{$r['class']}\" does not exist for {$version->educationLevel->name}";
                }
            }

            $official = in_array(mb_strtolower($r['official'] ?? ''), ['1', 'yes', 'y', 'true', 'official'], true);
            if ($official && ($r['source_ref'] ?? '') === '') {
                $warnings[$line] = 'marked official but has no source_ref (page or section of the official document)';
            }

            if ($problems) {
                $errors[$line] = implode('; ', $problems);
            }

            if ($code !== null && ! isset($fileCodes[$code])) {
                $fileCodes[$code] = $depth;
            }

            $rows[$line] = [
                'code' => $code,
                'parent_code' => $parentCode,
                'node_type' => $type,
                'title' => $title,
                'statement' => ($r['statement'] ?? '') !== '' ? $r['statement'] : null,
                'class_level_id' => $classLevelId,
                'is_official' => $official,
                'source_ref' => ($r['source_ref'] ?? '') !== '' ? mb_substr($r['source_ref'], 0, 255) : null,
            ];
        }

        fclose($handle);

        if (! $rows && ! $errors) {
            $errors[2] = 'The file has a header but no rows.';
        }

        return ['rows' => $rows, 'errors' => $errors, 'warnings' => $warnings];
    }

    /**
     * Validate again and, only if there are no errors, write every row.
     *
     * @return array{created: int, errors: array<int, string>}
     */
    public function commit(string $csvPath, CurriculumVersion $version, Subject $subject): array
    {
        $result = $this->validate($csvPath, $version, $subject);

        if ($result['errors']) {
            return ['created' => 0, 'errors' => $result['errors']];
        }

        $created = DB::transaction(function () use ($result, $version, $subject) {
            $byCode = CurriculumNode::inTree($version->id, $subject->id)->whereNotNull('code')->get()->keyBy('code')->all();

            foreach ($result['rows'] as $row) {
                $parent = $row['parent_code'] !== null ? $byCode[$row['parent_code']] : null;
                $node = $this->tree->add($version, $subject, $parent, collect($row)->except('parent_code')->all());

                if ($node->code !== null) {
                    $byCode[$node->code] = $node;
                }
            }

            return count($result['rows']);
        });

        return ['created' => $created, 'errors' => []];
    }

    /** CSV text of a subject's tree in the import format (export / backup / template). */
    public function export(CurriculumVersion $version, Subject $subject): string
    {
        $nodes = CurriculumNode::inTree($version->id, $subject->id)->with('classLevel')->orderBy('depth')->orderBy('sort')->orderBy('id')->get();
        $codeOf = $nodes->pluck('code', 'id');

        $out = fopen('php://temp', 'r+');
        fputcsv($out, self::TEMPLATE_HEADERS);

        // Parents always precede children: order by depth so the file re-imports cleanly.
        foreach ($nodes as $n) {
            fputcsv($out, [
                $n->code, $n->parent_id ? $codeOf[$n->parent_id] : '', $n->node_type, $n->title, $n->statement,
                $n->classLevel?->name, $n->is_official ? 'yes' : 'no', $n->source_ref,
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }
}
