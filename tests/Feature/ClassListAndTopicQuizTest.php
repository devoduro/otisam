<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Concerns\CreatesTopicQuestions;
use Tests\TestCase;

class ClassListAndTopicQuizTest extends TestCase
{
    use CreatesTopicQuestions;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
    }

    /** Uploads a list and returns the login sheet rows. */
    private function upload(string $csv, array $extra = []): array
    {
        $this->actingAs($this->teacher)->post(route('teach.class-list.store'), [
            'file' => UploadedFile::fake()->createWithContent('class.csv', $csv),
        ] + $extra)->assertSessionHasNoErrors()->assertRedirect(route('teach.class-list.credentials'));

        return session('class_list.credentials');
    }

    public function test_teacher_uploads_a_class_list_and_gets_usernames_and_pins(): void
    {
        $rows = $this->upload("Full name,Class\nAma Serwaa Owusu,JHS 2A\nKwame Boateng,JHS 2A\nEsi Mensah,JHS 2B\n");

        $this->assertCount(3, $rows);
        foreach ($rows as $row) {
            $this->assertMatchesRegularExpression('/^\d{6}$/', $row['pin']);
            $this->assertMatchesRegularExpression('/^[a-z.]+\d{3}$/', $row['username']);
        }
        $this->assertStringStartsWith('ama.owusu', $rows[0]['username']);

        // Classes are made from the class names and the students are already in them.
        $jhs2a = Classroom::where('teacher_id', $this->teacher->id)->where('name', 'JHS 2A')->firstOrFail();
        $this->assertSame(['Ama Serwaa Owusu', 'Kwame Boateng'], $jhs2a->students->pluck('name')->all());
        $this->assertSame(1, Classroom::where('name', 'JHS 2B')->first()->students()->count());

        $student = User::where('username', $rows[0]['username'])->firstOrFail();
        $this->assertTrue($student->isStudent());
        $this->assertTrue($student->isClassManaged());
        $this->assertNull($student->phone);
        $this->assertNotSame($rows[0]['pin'], $student->password, 'The PIN is stored hashed.');

        // The sheet shows the PINs; the CSV and slips carry them too.
        $this->get(route('teach.class-list.credentials'))->assertOk()->assertSee($rows[0]['username'])->assertSee($rows[0]['pin']);
        $this->get(route('teach.class-list.credentials', ['print' => 1]))->assertOk()->assertSee($rows[2]['pin'])->assertSee('Cut along the dashed lines');
        $csv = $this->get(route('teach.class-list.download'))->assertOk()->streamedContent();
        $this->assertStringContainsString($rows[1]['username'].','.$rows[1]['pin'], $csv);

        // Uploading the same list again makes no duplicates.
        $again = $this->upload("Ama Serwaa Owusu,JHS 2A\nYaw Asante,JHS 2A\n");
        $this->assertCount(1, $again);
        $this->assertSame('Yaw Asante', $again[0]['name']);
        $this->assertSame(['Ama Serwaa Owusu is already in JHS 2A.'], session('class_list.skipped'));

        // Clearing the sheet removes the PINs from the session.
        $this->delete(route('teach.class-list.forget'))->assertRedirect(route('teach.classes.index'));
        $this->get(route('teach.class-list.credentials'))->assertRedirect(route('teach.classes.index'));
    }

    public function test_pasted_list_uses_the_chosen_class_and_bad_lines_are_reported(): void
    {
        $this->actingAs($this->teacher)->post(route('teach.classes.store'), ['name' => 'Form 1 Science', 'joining_open' => '1']);
        $class = Classroom::firstOrFail();

        // Pasted from a spreadsheet (tab separated); a line with no class goes into the chosen class.
        $this->post(route('teach.class-list.store'), ['list' => "Kofi Annan\tForm 1 Science\nAbena Pokua", 'classroom' => $class->ulid])->assertSessionHasNoErrors();
        $this->assertSame(2, $class->students()->count());

        $this->post(route('teach.class-list.store'), ['list' => "Akua Donkor\n12345,JHS 1"])->assertSessionHasErrors('list');
        $this->assertSame(2, User::whereNotNull('username')->count());

        $this->post(route('teach.class-list.store'), [])->assertSessionHasErrors('file');

        // Another teacher's class cannot be used as the default.
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->post(route('teach.class-list.store'), ['list' => 'Ama Ampofo', 'classroom' => $class->ulid])->assertNotFound();

        // Pending teachers cannot upload class lists.
        $pending = User::factory()->teacher()->create();
        $this->actingAs($pending)->get(route('teach.class-list.create'))->assertRedirect(route('teach.dashboard'));
    }

    public function test_class_list_student_signs_in_with_username_and_pin(): void
    {
        $row = $this->upload("Esi Mensah,JHS 2B\n")[0];
        auth()->logout();

        $this->post(route('login.attempt'), ['login' => strtoupper($row['username']), 'password' => $row['pin']])->assertRedirect(route('learn.dashboard'));
        // No phone, so no SMS verification step.
        $this->get(route('learn.classes.index'))->assertOk()->assertSee('JHS 2B');
        $this->get(route('learn.dashboard'))->assertOk();
    }

    public function test_wrong_pins_lock_the_username_whatever_the_address(): void
    {
        $row = $this->upload("Esi Mensah,JHS 2B\n")[0];
        auth()->logout();
        RateLimiter::clear('login-account:'.sha1($row['username']));
        $wrong = $row['pin'] === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 10; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.'.($i + 1)])
                ->post(route('login.attempt'), ['login' => $row['username'], 'password' => $wrong])->assertSessionHasErrors('login');
        }
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
            ->post(route('login.attempt'), ['login' => $row['username'], 'password' => $row['pin']])
            ->assertSessionHasErrors(['login' => 'Too many sign-in attempts. Try again in 900 seconds.']);
        $this->assertGuest();
    }

    public function test_teacher_resets_a_lost_pin(): void
    {
        $row = $this->upload("Esi Mensah,JHS 2B\n")[0];
        $class = Classroom::firstOrFail();
        $student = User::where('username', $row['username'])->firstOrFail();

        $this->get(route('teach.classes.show', $class))->assertOk()->assertSee($row['username'])->assertSee('New PIN for Esi Mensah', false);
        $this->post(route('teach.classes.students.pin', [$class, $student]))->assertSessionHas('pin_reset');
        $pin = session('pin_reset')['pin'];
        $this->assertNotSame($row['pin'], $pin);
        auth()->logout();

        $this->post(route('login.attempt'), ['login' => $row['username'], 'password' => $row['pin']])->assertSessionHasErrors('login');
        $this->post(route('login.attempt'), ['login' => $row['username'], 'password' => $pin])->assertRedirect(route('learn.dashboard'));
        auth()->logout();

        // Only the teacher who made the account can reset it, and only for class-list accounts.
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->post(route('teach.classes.students.pin', [$class, $student]))->assertNotFound();
        $phoneStudent = User::factory()->student()->create();
        $class->students()->attach($phoneStudent->id, ['joined_at' => now()]);
        $this->actingAs($this->teacher)->post(route('teach.classes.students.pin', [$class, $phoneStudent]))->assertForbidden();
    }

    public function test_topic_quiz_with_time_limit_and_assessment_details(): void
    {
        $maths = Subject::where('slug', 'jhs-mathematics')->firstOrFail();
        $fractions = $this->topicWithQuestions($maths, 'Fractions', 6);
        $this->topicWithQuestions($maths, 'Angles', 4);

        $rows = $this->upload("Ama Owusu,JHS 1A\nKwame Boateng,JHS 1A\nEsi Mensah,JHS 1A\n");
        $class = Classroom::firstOrFail();

        $this->post(route('teach.assignments.store', $class), ['type' => 'quiz', 'title' => 'Fractions test', 'time_limit_minutes' => 15]);
        $quiz = Assignment::firstOrFail();

        // The topic list for the subject shows how many questions each topic has.
        $this->getJson(route('teach.assignments.bank-topics', ['subject_id' => $maths->id]))->assertOk()
            ->assertJsonFragment(['title' => 'Fractions', 'count' => 6])->assertJsonFragment(['title' => 'Angles', 'count' => 4]);

        $this->post(route('teach.assignments.questions.bank', $quiz), ['subject_id' => $maths->id, 'topic_id' => $fractions->id, 'count' => 5])->assertSessionHas('success');
        $this->assertSame(5, $quiz->questions()->count());
        $this->assertTrue($quiz->questions()->with('currentRevision')->get()->every(fn ($q) => str_starts_with($q->currentRevision->stem, 'Fractions')));
        $this->post(route('teach.assignments.publish', $quiz))->assertSessionHas('success');
        auth()->logout();

        // Two students sit the quiz; the third does not start.
        $questions = $quiz->questions()->get();
        foreach ([[$rows[0], 5], [$rows[1], 2]] as [$row, $right]) {
            $this->post(route('login.attempt'), ['login' => $row['username'], 'password' => $row['pin']]);
            $this->get(route('learn.assignments.take', $quiz))->assertOk()->assertSee('Time left');
            $this->travel(3)->minutes();
            $answers = $questions->mapWithKeys(fn ($q, $i) => [$q->ulid => $i < $right ? 'B' : 'C'])->all();
            $this->post(route('learn.assignments.submit-quiz', $quiz), ['answers' => $answers])->assertRedirect(route('learn.assignments.show', $quiz));
            auth()->logout();
        }
        $this->assertSame(5.0, (float) Submission::whereHas('student', fn ($q) => $q->where('username', $rows[0]['username']))->value('score'));

        $report = $this->actingAs($this->teacher)->get(route('teach.assignments.report', $quiz))->assertOk();
        $report->assertSee('2/3')->assertSee('70%')->assertSee('100%')->assertSee('40%')->assertSee('3:00')
            ->assertSee('Not started')->assertSee('Most common wrong answer: C. wrong two (1)')
            ->assertSeeInOrder(['Ama Owusu', 'Kwame Boateng', 'Esi Mensah']);
        $this->get(route('teach.assignments.report', [$quiz, 'pass' => 60]))->assertSee('Passed (60% or more)')->assertSee('1/2');

        $csv = $this->get(route('teach.assignments.report-csv', $quiz))->assertOk()->streamedContent();
        $this->assertStringContainsString('"Ama Owusu",'.$rows[0]['username'].',Submitted,5,5,100%,3:00', $csv);
        $this->assertStringContainsString('"Esi Mensah",'.$rows[2]['username'].',"Not started"', $csv);

        // Only the quiz's teacher sees the results.
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->get(route('teach.assignments.report', $quiz))->assertNotFound();
    }

    public function test_the_template_has_an_email_column(): void
    {
        $csv = $this->actingAs($this->teacher)->get(route('teach.class-list.template'))->assertOk()->streamedContent();

        $this->assertStringStartsWith("\"Full name\",Class,Email\n", $csv);
        $this->get(route('teach.class-list.create'))->assertSee('<strong>Email</strong>', false);
    }

    public function test_student_with_an_email_signs_in_with_it_and_chooses_a_password(): void
    {
        $row = $this->upload("Full name,Class,Email\nEsi Mensah,JHS 2B,esi@example.com\n")[0];
        $this->assertSame('esi@example.com', $row['email']);
        $this->get(route('teach.class-list.credentials'))->assertSee('esi@example.com');
        $this->get(route('teach.class-list.credentials', ['print' => 1]))->assertSee('with your email or username');
        $this->assertStringContainsString('"Esi Mensah","JHS 2B",esi@example.com,'.$row['username'].','.$row['pin'], $this->get(route('teach.class-list.download'))->streamedContent());
        auth()->logout();

        // First sign-in with the email and PIN goes to "choose your password".
        $this->post(route('login.attempt'), ['login' => 'esi@example.com', 'password' => $row['pin']])->assertRedirect(route('learn.dashboard'));
        $this->get(route('learn.dashboard'))->assertRedirect(route('password.change'));
        $this->put(route('password.change.update'), ['current_password' => $row['pin'], 'password' => 'mangoTree2026', 'password_confirmation' => 'mangoTree2026'])
            ->assertRedirect(route('learn.dashboard'));
        $this->get(route('learn.classes.index'))->assertOk()->assertSee('JHS 2B');
        auth()->logout();

        // From then on the account is theirs: the PIN no longer works, their password does.
        $this->post(route('login.attempt'), ['login' => 'esi@example.com', 'password' => $row['pin']])->assertSessionHasErrors('login');
        $this->post(route('login.attempt'), ['login' => $row['username'], 'password' => 'mangoTree2026'])->assertRedirect(route('learn.dashboard'));
    }

    public function test_a_self_registered_student_is_added_to_the_class_by_email(): void
    {
        $student = User::factory()->student()->create(['name' => 'Kojo Antwi', 'email' => 'kojo@example.com']);

        $this->upload("Kojo Antwi,JHS 3A,kojo@example.com\nAdwoa Pokua,JHS 3A,adwoa@example.com\n");

        $this->get(route('teach.class-list.credentials'))->assertSee('Kojo Antwi (kojo@example.com) already has an account and was added to JHS 3A.')->assertSee('adwoa@example.com');
        $this->assertTrue(Classroom::where('name', 'JHS 3A')->first()->hasStudent($student));
        $this->assertSame(1, User::where('email', 'kojo@example.com')->count());
    }

    public function test_a_list_with_only_existing_accounts_still_shows_the_summary(): void
    {
        User::factory()->student()->create(['name' => 'Kojo Antwi', 'email' => 'kojo@example.com']);

        $this->actingAs($this->teacher)->post(route('teach.class-list.store'), ['list' => 'Kojo Antwi,JHS 3A,kojo@example.com'])
            ->assertRedirect(route('teach.class-list.credentials'))->assertSessionHas('success', '0 student account(s) created, 1 existing account(s) added to a class.');
        $this->get(route('teach.class-list.credentials'))->assertOk()->assertSee('already has an account');
    }
}
