<?php

namespace App\Services\Classroom;

use App\Enums\AccountType;
use App\Enums\UserStatus;
use App\Models\Classroom\Classroom;
use App\Models\User;
use App\Services\Audit;
use App\Support\Permissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Turns a teacher's class list (full name, class name, email) into student accounts. Each student
 * gets a username and a 6-digit PIN, and is put straight into the teacher's class with that name
 * (made if it does not exist yet). The PINs are only known at this moment; the caller shows them to
 * the teacher once, and the teacher can reset a PIN later.
 *
 * The email is optional. With one, the account is the student's own for good: they can sign in
 * with the email too, and must choose their own password the first time they sign in. If a student
 * account with that email already exists, that account joins the class instead of a new one.
 */
class ClassListImporter
{
    public const MAX_ROWS = 300;

    /**
     * Rows from an uploaded CSV file or text pasted from a spreadsheet (tab, comma or semicolon
     * separated). A first row naming the columns ("Name", "Class", "Email") is skipped.
     *
     * @return list<array{name: string, class: ?string, email: ?string, line: int}>
     */
    public function parse(string $text): array
    {
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text); // Excel's UTF-8 marker
        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }
        $lines = preg_split('/\r\n|\r|\n/', trim($text)) ?: [];
        $delimiter = str_contains($text, "\t") ? "\t" : (substr_count($text, ';') > substr_count($text, ',') ? ';' : ',');

        $rows = [];
        foreach ($lines as $i => $line) {
            $cells = array_map(fn ($c) => Str::squish((string) $c), str_getcsv($line, $delimiter));
            if (($cells[0] ?? '') === '') {
                continue;
            }
            if ($i === 0 && preg_match('/^(full\s*)?names?$|^student/i', $cells[0])) {
                continue;
            }
            $rows[] = [
                'name' => $cells[0],
                'class' => ($cells[1] ?? '') !== '' ? $cells[1] : null,
                'email' => ($cells[2] ?? '') !== '' ? mb_strtolower(str_replace(' ', '', $cells[2])) : null,
                'line' => $i + 1,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array{name: string, class: ?string, email?: ?string, line: int}>  $rows
     * @return array{created: list<array{name: string, class: string, username: string, pin: string, email: ?string}>, enrolled: list<string>, skipped: list<string>}
     */
    public function import(User $teacher, array $rows, ?Classroom $defaultClass = null): array
    {
        if ($rows === []) {
            throw ValidationException::withMessages(['list' => 'No student names were found. Put each student on a new line: full name, then class name.']);
        }
        if (count($rows) > self::MAX_ROWS) {
            throw ValidationException::withMessages(['list' => 'Upload at most '.self::MAX_ROWS.' students at a time.']);
        }

        $errors = [];
        foreach ($rows as $row) {
            if (mb_strlen($row['name']) < 2 || mb_strlen($row['name']) > 120 || ! preg_match('/\p{L}/u', $row['name'])) {
                $errors[] = "Line {$row['line']}: \"{$row['name']}\" does not look like a name.";
            }
            if ($row['class'] === null && ! $defaultClass) {
                $errors[] = "Line {$row['line']}: no class name for {$row['name']}. Add it in the second column or choose a class below.";
            }
            if ($row['class'] !== null && mb_strlen($row['class']) > 120) {
                $errors[] = "Line {$row['line']}: the class name is too long.";
            }
            $email = $row['email'] ?? null;
            if ($email !== null && (mb_strlen($email) > 190 || ! filter_var($email, FILTER_VALIDATE_EMAIL))) {
                $errors[] = "Line {$row['line']}: \"{$email}\" is not a valid email address.";
            }
        }
        $emails = array_values(array_filter(array_map(fn ($r) => $r['email'] ?? null, $rows)));
        foreach (array_unique(array_diff_assoc($emails, array_unique($emails))) as $email) {
            $errors[] = "{$email} is on the list more than once. Each student needs their own email.";
        }
        $existing = $emails ? User::whereIn('email', $emails)->get()->keyBy('email') : collect();
        foreach ($existing as $email => $user) {
            if (! $user->isStudent()) {
                $errors[] = "{$email} belongs to a teacher or staff account, so it cannot be used for a student.";
            }
        }
        if ($errors) {
            throw ValidationException::withMessages(['list' => array_slice($errors, 0, 10)]);
        }

        return DB::transaction(function () use ($teacher, $rows, $defaultClass, $existing) {
            $classes = Classroom::where('teacher_id', $teacher->id)->whereNull('archived_at')->with('students:id,name')->get()
                ->keyBy(fn ($c) => mb_strtolower($c->name));
            $school = $teacher->teacherProfile?->school_name;
            $created = [];
            $enrolled = [];
            $skipped = [];

            foreach ($rows as $row) {
                $classroom = $row['class'] === null ? $defaultClass : ($classes[mb_strtolower($row['class'])] ?? null);
                if (! $classroom) {
                    $classroom = new Classroom(['name' => $row['class'], 'subject_id' => $defaultClass?->subject_id, 'class_level_id' => $defaultClass?->class_level_id, 'joining_open' => false]);
                    $classroom->teacher_id = $teacher->id;
                    $classroom->save();
                    $classroom->setRelation('students', collect());
                    $classes[mb_strtolower($classroom->name)] = $classroom;
                }

                $email = $row['email'] ?? null;
                $account = $email !== null ? ($existing[$email] ?? null) : null;

                // Uploading the same list twice should not make duplicate accounts.
                if ($classroom->students->contains(fn ($s) => $account ? $s->id === $account->id : mb_strtolower($s->name) === mb_strtolower($row['name']))) {
                    $skipped[] = "{$row['name']} is already in {$classroom->name}.";

                    continue;
                }

                // A student who already has an account keeps it and their own password.
                if ($account) {
                    $classroom->students()->attach($account->id, ['joined_at' => now()]);
                    $classroom->students->push($account);
                    $enrolled[] = "{$account->name} ({$email}) already has an account and was added to {$classroom->name}. They sign in with their own password.";

                    continue;
                }

                $pin = self::newPin();
                $student = new User(['name' => $row['name'], 'email' => $email, 'password' => $pin]);
                $student->forceFill([
                    'username' => self::newUsername($row['name']),
                    'managed_by_id' => $teacher->id,
                    'account_type' => AccountType::Student,
                    'status' => UserStatus::Active,
                    // With an email the account is the student's own: they choose their own password.
                    'must_change_password' => $email !== null,
                ])->save();
                if ($email !== null) {
                    $existing[$email] = $student;
                }
                $student->assignRole(Permissions::STUDENT);
                $student->studentProfile()->create(['class_level_id' => $classroom->class_level_id, 'school_name' => $school, 'is_minor' => true]);
                $classroom->students()->attach($student->id, ['joined_at' => now()]);
                $classroom->students->push($student);

                $created[] = ['name' => $student->name, 'class' => $classroom->name, 'username' => $student->username, 'pin' => $pin, 'email' => $email];
            }

            Audit::record('classroom', 'class_list.imported', count($created).' student account(s) created from a class list by '.$teacher->name, null, null, ['created' => count($created), 'enrolled' => count($enrolled), 'skipped' => count($skipped)]);

            return compact('created', 'enrolled', 'skipped');
        });
    }

    /** A new 6-digit PIN for a class-list student. Returns the PIN so it can be shown once. */
    public function resetPin(User $student): string
    {
        $pin = self::newPin();
        // A student with an email chooses their own password again after a reset.
        $student->forceFill(['password' => $pin, 'must_change_password' => $student->email !== null])->save();
        Audit::record('classroom', 'class_list.pin_reset', "PIN reset for {$student->name}", $student);

        return $pin;
    }

    public static function newPin(): string
    {
        return sprintf('%06d', random_int(0, 999999));
    }

    /** "Kofi Mensah Asante" → "kofi.asante482": first and last name plus three digits. */
    public static function newUsername(string $name): string
    {
        $base = self::usernameBase($name);

        do {
            $username = $base.random_int(100, 999);
        } while (User::where('username', $username)->exists());

        return $username;
    }

    /** The letters part of a username: first and last name in plain lowercase letters, each at most 15. */
    public static function usernameBase(string $name): string
    {
        $parts = array_values(array_filter(explode(' ', Str::lower(preg_replace('/[^A-Za-z ]/', '', Str::ascii($name))))));

        return $parts ? Str::limit($parts[0], 15, '').(count($parts) > 1 ? '.'.Str::limit(end($parts), 15, '') : '') : 'student';
    }
}
