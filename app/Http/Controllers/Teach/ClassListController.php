<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Classroom;
use App\Models\User;
use App\Services\Classroom\ClassListImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Teachers upload a class list (full name, class name, optional email) and get back a username and
 * 6-digit PIN for every new student. PINs are stored hashed, so the sheet is kept in the teacher's session only until
 * they clear it; after that a lost PIN is reset, not looked up.
 */
class ClassListController extends Controller
{
    private const SESSION_KEY = 'class_list.credentials';

    public function __construct(private readonly ClassListImporter $importer) {}

    public function create(Request $request): View
    {
        return view('teach.classes.upload', [
            'classrooms' => Classroom::where('teacher_id', $request->user()->id)->whereNull('archived_at')->orderBy('name')->get(['id', 'ulid', 'name']),
            'selected' => $request->query('class'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['nullable', 'file', 'mimes:csv,txt', 'max:1024', 'required_without:list'],
            'list' => ['nullable', 'string', 'max:60000', 'required_without:file'],
            'classroom' => ['nullable', 'string'],
        ], [
            'file.required_without' => 'Choose a CSV file or paste the list.',
            'list.required_without' => 'Choose a CSV file or paste the list.',
            'file.mimes' => 'Save the spreadsheet as CSV (File → Save As → CSV) and upload that, or copy the rows and paste them below.',
        ]);

        $default = ! empty($data['classroom'])
            ? Classroom::where('teacher_id', $request->user()->id)->where('ulid', $data['classroom'])->firstOrFail()
            : null;
        $text = $request->hasFile('file') ? (string) file_get_contents($request->file('file')->getRealPath()) : (string) $data['list'];

        $result = $this->importer->import($request->user(), $this->importer->parse($text), $default);
        $request->session()->put(self::SESSION_KEY, $result['created']);

        return redirect()->route('teach.class-list.credentials')
            ->with('success', count($result['created']).' student account(s) created'.($result['enrolled'] ? ', '.count($result['enrolled']).' existing account(s) added to a class' : '').'.')
            ->with('class_list.enrolled', $result['enrolled'])
            ->with('class_list.skipped', $result['skipped']);
    }

    public function credentials(Request $request): View|RedirectResponse
    {
        $rows = $request->session()->get(self::SESSION_KEY, []);
        $enrolled = session('class_list.enrolled', []);
        if (! $rows && ! $enrolled) {
            return redirect()->route('teach.classes.index')->with('status', 'There is no class list sheet to show. Reset a student\'s PIN from the class page if it has been lost.');
        }

        if ($request->boolean('print')) {
            return view('teach.classes.slips', ['rows' => $rows, 'teacher' => $request->user()]);
        }

        return view('teach.classes.credentials', ['rows' => $rows, 'enrolled' => $enrolled, 'skipped' => session('class_list.skipped', [])]);
    }

    public function download(Request $request): StreamedResponse|RedirectResponse
    {
        $rows = $request->session()->get(self::SESSION_KEY, []);
        if (! $rows) {
            return redirect()->route('teach.classes.index');
        }

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Full name', 'Class', 'Email', 'Username', 'PIN', 'Sign in at']);
            foreach ($rows as $row) {
                fputcsv($out, [$row['name'], $row['class'], $row['email'] ?? '', $row['username'], $row['pin'], route('login')]);
            }
            fclose($out);
        }, 'class-list-logins.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }

    public function forget(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('teach.classes.index')->with('success', 'The login sheet has been cleared from this computer.');
    }

    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Full name', 'Class', 'Email']);
            fputcsv($out, ['Ama Serwaa Owusu', 'JHS 2A', 'ama.owusu@example.com']);
            fputcsv($out, ['Kwame Boateng', 'JHS 2A', 'kwame.boateng@example.com']);
            fclose($out);
        }, 'class-list-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function resetPin(Request $request, Classroom $classroom, User $student): RedirectResponse
    {
        abort_unless($classroom->teacher_id === $request->user()->id && $classroom->hasStudent($student), 404);
        abort_unless($student->isClassManaged() && $student->managed_by_id === $request->user()->id, 403, 'Only accounts you created from a class list have a PIN you can reset.');

        $pin = $this->importer->resetPin($student);

        return back()->with('pin_reset', ['name' => $student->name, 'username' => $student->username, 'pin' => $pin]);
    }
}
