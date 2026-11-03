<?php

namespace Tests\Unit;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Classroom;
use App\Models\Curriculum\ClassLevel;
use App\Models\User;
use App\Services\Classroom\ClassListImporter;
use App\Support\Permissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ClassListImporterTest extends TestCase
{
    private ClassListImporter $importer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importer = app(ClassListImporter::class);
    }

    /** @return list<array{0: string, 1: ?string}> */
    private function parsed(string $text): array
    {
        return array_map(fn ($r) => [$r['name'], $r['class']], $this->importer->parse($text));
    }

    // Parsing

    public function test_parses_comma_separated_rows_and_skips_the_header(): void
    {
        $this->assertSame(
            [['Ama Owusu', 'JHS 2A'], ['Kwame Boateng', 'JHS 2B']],
            $this->parsed("Full name,Class\nAma Owusu,JHS 2A\nKwame Boateng,JHS 2B"),
        );
    }

    public function test_recognises_common_header_names(): void
    {
        foreach (['Name,Class', 'Names,Class', 'Student name,Form', 'FULL NAME,CLASS'] as $header) {
            $this->assertSame([['Esi Mensah', 'JHS 1']], $this->parsed("{$header}\nEsi Mensah,JHS 1"), $header);
        }
    }

    public function test_a_name_in_the_first_row_is_not_mistaken_for_a_header(): void
    {
        $this->assertSame([['Nana Ama', 'JHS 1'], ['Kofi', 'JHS 1']], $this->parsed("Nana Ama,JHS 1\nKofi,JHS 1"));
    }

    public function test_parses_tab_separated_text_pasted_from_a_spreadsheet(): void
    {
        $this->assertSame([['Ama Owusu', 'JHS 2A'], ['Kofi Annan', 'JHS 2A']], $this->parsed("Ama Owusu\tJHS 2A\nKofi Annan\tJHS 2A"));
    }

    public function test_parses_semicolon_separated_files_from_european_excel(): void
    {
        $this->assertSame([['Ama Owusu', 'JHS 2A']], $this->parsed("Name;Class\nAma Owusu;JHS 2A"));
    }

    public function test_quoted_cells_may_contain_commas(): void
    {
        $this->assertSame([['Owusu, Ama', 'JHS 2A']], $this->parsed('"Owusu, Ama",JHS 2A'));
    }

    public function test_a_missing_class_column_gives_null(): void
    {
        $this->assertSame([['Ama Owusu', null], ['Kofi Annan', null]], $this->parsed("Ama Owusu\nKofi Annan,"));
    }

    public function test_blank_lines_extra_spaces_and_windows_line_endings_are_ignored(): void
    {
        $this->assertSame(
            [['Ama Serwaa Owusu', 'JHS 2A'], ['Kofi Annan', 'JHS 2A']],
            $this->parsed("\r\n  Ama   Serwaa  Owusu ,  JHS   2A \r\n\r\n,\r\nKofi Annan,JHS 2A\r\n"),
        );
    }

    public function test_strips_the_excel_utf8_marker_and_converts_windows_1252(): void
    {
        $this->assertSame([['Ama Owusu', 'JHS 2A']], $this->parsed("\xEF\xBB\xBFName,Class\nAma Owusu,JHS 2A"));
        // "Adèle" saved by old Excel in Windows-1252, not UTF-8.
        $this->assertSame([['Adèle Mensah', 'JHS 1']], $this->parsed("Ad\xE8le Mensah,JHS 1"));
    }

    public function test_rows_keep_their_line_number_for_error_messages(): void
    {
        $rows = $this->importer->parse("Name,Class\n\nAma Owusu,JHS 2A");
        $this->assertSame(3, $rows[0]['line']);
    }

    public function test_empty_text_gives_no_rows(): void
    {
        $this->assertSame([], $this->importer->parse(''));
        $this->assertSame([], $this->importer->parse("Name,Class\n"));
    }

    // PINs and usernames

    public function test_pins_are_always_six_digits(): void
    {
        for ($i = 0; $i < 500; $i++) {
            $this->assertMatchesRegularExpression('/^\d{6}$/', ClassListImporter::newPin());
        }
    }

    public function test_username_base_uses_first_and_last_name(): void
    {
        $this->assertSame('kofi.asante', ClassListImporter::usernameBase('Kofi Mensah Asante'));
        $this->assertSame('ama.owusu', ClassListImporter::usernameBase('  AMA   OWUSU '));
        $this->assertSame('yaw', ClassListImporter::usernameBase('Yaw'));
    }

    public function test_username_base_drops_accents_punctuation_and_digits(): void
    {
        $this->assertSame('adele.oneil', ClassListImporter::usernameBase("Adèle O'Neil"));
        $this->assertSame('abena.ampofoboateng', ClassListImporter::usernameBase('Abena Ampofo-Boateng'));
        $this->assertSame('kwame.mensah', ClassListImporter::usernameBase('Kwame Mensah 2'));
    }

    public function test_username_base_falls_back_to_student_and_limits_length(): void
    {
        $this->assertSame('student', ClassListImporter::usernameBase('123 456'));
        $this->assertSame('abcdefghijklmno.pqrstuvwxyzabcd', ClassListImporter::usernameBase('Abcdefghijklmnopqrst Pqrstuvwxyzabcdefgh'));
    }

    public function test_new_usernames_add_three_digits_and_are_not_reused(): void
    {
        $username = ClassListImporter::newUsername('Kofi Mensah');
        $this->assertMatchesRegularExpression('/^kofi\.mensah\d{3}$/', $username);
        $this->assertLessThanOrEqual(40, strlen(ClassListImporter::newUsername(str_repeat('a', 50).' '.str_repeat('b', 50))));

        // Fill every free number but one: the generator must find the last one.
        $taken = collect(range(100, 999))->reject(fn ($n) => $n === 555)->map(fn ($n) => ['name' => 'X', 'username' => "esi.owusu{$n}", 'password' => 'x', 'account_type' => 'student', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        foreach ($taken->chunk(200) as $chunk) {
            User::insert($chunk->values()->all());
        }
        $this->assertSame('esi.owusu555', ClassListImporter::newUsername('Esi Owusu'));
    }

    // Import

    private function teacher(): User
    {
        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $teacher->teacherProfile->update(['school_name' => 'Wesley Girls\' High School']);

        return $teacher->fresh('teacherProfile');
    }

    private function rows(array $lines): array
    {
        return array_map(fn ($l, $i) => ['name' => $l[0], 'class' => $l[1] ?? null, 'line' => $i + 1], $lines, array_keys($lines));
    }

    public function test_import_creates_class_managed_student_accounts(): void
    {
        $teacher = $this->teacher();
        $result = $this->importer->import($teacher, $this->rows([['Ama Owusu', 'JHS 2A']]));

        $this->assertCount(1, $result['created']);
        [$row] = $result['created'];
        $student = User::where('username', $row['username'])->firstOrFail();

        $this->assertSame('Ama Owusu', $student->name);
        $this->assertTrue($student->isStudent());
        $this->assertTrue($student->isActive());
        $this->assertTrue($student->isClassManaged());
        $this->assertSame($teacher->id, $student->managed_by_id);
        $this->assertTrue($student->hasRole(Permissions::STUDENT));
        $this->assertNull($student->phone);
        $this->assertNull($student->email);
        $this->assertTrue(Hash::check($row['pin'], $student->password));
        $this->assertTrue($student->studentProfile->is_minor);
        $this->assertSame('Wesley Girls\' High School', $student->studentProfile->school_name);
    }

    public function test_import_creates_missing_classes_and_reuses_existing_ones_by_name(): void
    {
        $teacher = $this->teacher();
        $existing = new Classroom(['name' => 'JHS 2A']);
        $existing->teacher_id = $teacher->id;
        $existing->save();

        $this->importer->import($teacher, $this->rows([['Ama Owusu', 'jhs 2a'], ['Kofi Annan', 'JHS 2B'], ['Esi Mensah', 'JHS 2B']]));

        $this->assertSame(2, Classroom::where('teacher_id', $teacher->id)->count());
        $this->assertSame(['Ama Owusu'], $existing->students->pluck('name')->all());
        $new = Classroom::where('name', 'JHS 2B')->firstOrFail();
        $this->assertSame(['Esi Mensah', 'Kofi Annan'], $new->students->pluck('name')->all());
        $this->assertFalse($new->joining_open, 'Classes made from a list do not take join codes by default.');
    }

    public function test_import_does_not_reuse_another_teachers_or_an_archived_class(): void
    {
        $teacher = $this->teacher();
        $other = new Classroom(['name' => 'JHS 2A']);
        $other->teacher_id = $this->teacher()->id;
        $other->save();
        $archived = new Classroom(['name' => 'JHS 2A']);
        $archived->teacher_id = $teacher->id;
        $archived->archived_at = now();
        $archived->save();

        $this->importer->import($teacher, $this->rows([['Ama Owusu', 'JHS 2A']]));

        $this->assertSame(0, $other->students()->count());
        $this->assertSame(0, $archived->students()->count());
        $this->assertSame(1, Classroom::where('teacher_id', $teacher->id)->whereNull('archived_at')->where('name', 'JHS 2A')->firstOrFail()->students()->count());
    }

    public function test_rows_without_a_class_go_into_the_default_class_and_new_classes_copy_its_year(): void
    {
        $teacher = $this->teacher();
        $year = ClassLevel::firstOrFail();
        $default = new Classroom(['name' => 'Form 1 Science', 'class_level_id' => $year->id]);
        $default->teacher_id = $teacher->id;
        $default->save();

        $this->importer->import($teacher, $this->rows([['Ama Owusu'], ['Kofi Annan', 'Form 1 Arts']]), $default);

        $this->assertSame(['Ama Owusu'], $default->students->pluck('name')->all());
        $this->assertSame($year->id, Classroom::where('name', 'Form 1 Arts')->value('class_level_id'));
        $this->assertSame($year->id, User::where('name', 'Ama Owusu')->first()->studentProfile->class_level_id);
    }

    public function test_the_same_name_in_the_same_class_is_skipped_but_allowed_in_another_class(): void
    {
        $teacher = $this->teacher();
        $this->importer->import($teacher, $this->rows([['Ama Owusu', 'JHS 2A']]));

        $result = $this->importer->import($teacher, $this->rows([['AMA OWUSU', 'JHS 2A'], ['Ama Owusu', 'JHS 2B'], ['Kofi Annan', 'JHS 2A'], ['Kofi Annan', 'JHS 2A']]));

        $this->assertSame(['Ama Owusu', 'Kofi Annan'], array_column($result['created'], 'name'));
        $this->assertSame(['AMA OWUSU is already in JHS 2A.', 'Kofi Annan is already in JHS 2A.'], $result['skipped']);
        $this->assertSame(3, User::whereNotNull('username')->count());
    }

    public function test_every_student_gets_a_different_username(): void
    {
        $teacher = $this->teacher();
        $result = $this->importer->import($teacher, $this->rows(array_fill(0, 30, ['Kofi Mensah', 'X'])));

        // Same name, same class: only the first is created; across classes each is unique.
        $this->assertCount(1, $result['created']);
        $more = $this->importer->import($teacher, $this->rows(array_map(fn ($i) => ['Kofi Mensah', "Class {$i}"], range(1, 30))));
        $this->assertCount(30, array_unique(array_column($more['created'], 'username')));
    }

    /** @return array<string, array{0: array, 1: string}> */
    public static function invalidLists(): array
    {
        return [
            'nothing' => [[], 'No student names were found'],
            'a number for a name' => [[['12345', 'JHS 1']], 'does not look like a name'],
            'one letter' => [[['A', 'JHS 1']], 'does not look like a name'],
            'no class and no default' => [[['Ama Owusu']], 'no class name for Ama Owusu'],
            'class name too long' => [[['Ama Owusu', str_repeat('x', 121)]], 'class name is too long'],
            'too many rows' => [array_fill(0, ClassListImporter::MAX_ROWS + 1, ['Ama Owusu', 'JHS 1']), 'at most 300 students'],
        ];
    }

    #[DataProvider('invalidLists')]
    public function test_invalid_lists_are_rejected_before_anything_is_saved(array $lines, string $message): void
    {
        $teacher = $this->teacher();
        try {
            $this->importer->import($teacher, $this->rows($lines));
            $this->fail('Expected a validation error.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString($message, implode(' ', $e->errors()['list']));
        }
        $this->assertSame(0, User::whereNotNull('username')->count());
        $this->assertSame(0, Classroom::count());
    }

    public function test_one_bad_line_saves_none_of_the_list(): void
    {
        $this->expectException(ValidationException::class);
        try {
            $this->importer->import($this->teacher(), $this->rows([['Ama Owusu', 'JHS 1'], ['7', 'JHS 1']]));
        } finally {
            $this->assertSame(0, User::whereNotNull('username')->count());
        }
    }

    public function test_reset_pin_replaces_the_old_pin(): void
    {
        $result = $this->importer->import($this->teacher(), $this->rows([['Ama Owusu', 'JHS 2A']]));
        $student = User::where('username', $result['created'][0]['username'])->firstOrFail();

        $pin = $this->importer->resetPin($student);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $pin);
        $this->assertTrue(Hash::check($pin, $student->fresh()->password));
        if ($pin !== $result['created'][0]['pin']) {
            $this->assertFalse(Hash::check($result['created'][0]['pin'], $student->fresh()->password));
        }
    }

    // Emails

    public function test_parses_an_optional_email_column(): void
    {
        $rows = $this->importer->parse("Full name,Class,Email\nAma Owusu,JHS 2A, Ama.Owusu@Example.COM \nKofi Annan,JHS 2A,\nEsi Mensah,JHS 2A");

        $this->assertSame(['ama.owusu@example.com', null, null], array_column($rows, 'email'));
        $this->assertSame(['Ama Owusu', 'Kofi Annan', 'Esi Mensah'], array_column($rows, 'name'));
    }

    private function emailRows(array $lines): array
    {
        return array_map(fn ($l, $i) => ['name' => $l[0], 'class' => $l[1] ?? 'JHS 2A', 'email' => $l[2] ?? null, 'line' => $i + 1], $lines, array_keys($lines));
    }

    public function test_a_student_with_an_email_gets_a_permanent_account(): void
    {
        $result = $this->importer->import($this->teacher(), $this->emailRows([['Ama Owusu', 'JHS 2A', 'ama@example.com'], ['Kofi Annan', 'JHS 2A']]));

        [$withEmail, $withoutEmail] = $result['created'];
        $this->assertSame('ama@example.com', $withEmail['email']);
        $this->assertNull($withoutEmail['email']);

        $ama = User::where('email', 'ama@example.com')->firstOrFail();
        $this->assertSame($withEmail['username'], $ama->username);
        $this->assertTrue($ama->must_change_password, 'Students with an email choose their own password.');
        $this->assertTrue(Hash::check($withEmail['pin'], $ama->password));
        $this->assertTrue(User::findByLogin('AMA@example.com')->is($ama));
        $this->assertFalse(User::where('username', $withoutEmail['username'])->first()->must_change_password);
    }

    public function test_an_existing_student_account_joins_the_class_instead_of_a_new_one(): void
    {
        $teacher = $this->teacher();
        $existing = User::factory()->student()->create(['name' => 'Ama Serwaa Owusu', 'email' => 'ama@example.com']);
        $password = $existing->password;

        $result = $this->importer->import($teacher, $this->emailRows([['Ama Owusu', 'JHS 2A', 'ama@example.com'], ['Kofi Annan', 'JHS 2A', 'kofi@example.com']]));

        $this->assertSame(['Kofi Annan'], array_column($result['created'], 'name'));
        $this->assertSame(['Ama Serwaa Owusu (ama@example.com) already has an account and was added to JHS 2A. They sign in with their own password.'], $result['enrolled']);
        $class = Classroom::where('name', 'JHS 2A')->firstOrFail();
        $this->assertTrue($class->hasStudent($existing));
        $this->assertSame($password, $existing->fresh()->password, 'Their password is not changed.');
        $this->assertNull($existing->fresh()->managed_by_id, 'The teacher does not take over the account.');
        $this->assertSame(1, User::where('email', 'ama@example.com')->count());

        // Uploading again skips both.
        $again = $this->importer->import($teacher, $this->emailRows([['Ama Owusu', 'JHS 2A', 'ama@example.com'], ['Kofi Annan', 'JHS 2A', 'kofi@example.com']]));
        $this->assertSame([], $again['created']);
        $this->assertSame([], $again['enrolled']);
        $this->assertCount(2, $again['skipped']);
    }

    public function test_the_same_email_in_two_classes_makes_one_account_in_both(): void
    {
        $result = $this->importer->import($this->teacher(), $this->emailRows([['Ama Owusu', 'Maths club', 'ama@example.com']]));
        $this->assertCount(1, $result['created']);

        $more = $this->importer->import(User::find(Classroom::first()->teacher_id), $this->emailRows([['Ama Owusu', 'Science club', 'ama@example.com']]));
        $this->assertSame([], $more['created']);
        $this->assertCount(1, $more['enrolled']);
        $this->assertSame(1, User::where('email', 'ama@example.com')->count());
        $this->assertSame(2, User::where('email', 'ama@example.com')->first()->classrooms()->count());
    }

    /** @return array<string, array{0: array, 1: string}> */
    public static function badEmails(): array
    {
        return [
            'not an email' => [[['Ama Owusu', 'JHS 1', 'ama.example.com']], '"ama.example.com" is not a valid email address'],
            'twice on the list' => [[['Ama Owusu', 'JHS 1', 'ama@example.com'], ['Ama Mensah', 'JHS 1', 'ama@example.com']], 'ama@example.com is on the list more than once'],
        ];
    }

    #[DataProvider('badEmails')]
    public function test_bad_emails_are_rejected(array $lines, string $message): void
    {
        try {
            $this->importer->import($this->teacher(), $this->emailRows($lines));
            $this->fail('Expected a validation error.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString($message, implode(' ', $e->errors()['list']));
        }
        $this->assertSame(0, User::whereNotNull('username')->count());
    }

    public function test_a_teachers_or_staff_email_cannot_be_used_for_a_student(): void
    {
        $teacher = $this->teacher();
        $teacher->forceFill(['email' => 'teacher@example.com'])->save();

        try {
            $this->importer->import($teacher, $this->emailRows([['Ama Owusu', 'JHS 1', 'teacher@example.com']]));
            $this->fail('Expected a validation error.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('teacher@example.com belongs to a teacher or staff account', implode(' ', $e->errors()['list']));
        }
        $this->assertSame(0, Classroom::count());
    }

    public function test_resetting_the_pin_of_a_student_with_an_email_asks_for_a_new_password(): void
    {
        $result = $this->importer->import($this->teacher(), $this->emailRows([['Ama Owusu', 'JHS 2A', 'ama@example.com'], ['Kofi Annan']]));
        $ama = User::where('email', 'ama@example.com')->firstOrFail();
        $kofi = User::where('username', $result['created'][1]['username'])->firstOrFail();
        $ama->forceFill(['must_change_password' => false])->save();

        $this->importer->resetPin($ama);
        $this->importer->resetPin($kofi);

        $this->assertTrue($ama->fresh()->must_change_password);
        $this->assertFalse($kofi->fresh()->must_change_password);
    }
}
