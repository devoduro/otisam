<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\AuditLog;
use App\Models\Community\Answer;
use App\Models\Community\Question;
use App\Models\Community\Report;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\NotificationMessage;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Tests\TestCase;

class CommunityQaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
    }

    private function questionData(array $overrides = []): array
    {
        $shs = EducationLevel::where('code', 'SHS')->firstOrFail();

        return $overrides + [
            'title' => 'How do I find the gradient of a line through two points?',
            'body' => "The points are (1, 2) and (4, 8).\nI keep getting a negative answer.",
            'education_level_id' => $shs->id,
            'class_level_id' => ClassLevel::where('name', 'SHS 1')->value('id'),
            'subject_id' => Subject::where('slug', 'core-mathematics')->value('id'),
        ];
    }

    private function ask(?User $asker = null, array $overrides = []): Question
    {
        $asker ??= User::factory()->student()->create(['name' => 'Ama Owusu']);
        $this->actingAs($asker)->post(route('qa.store'), $this->questionData($overrides))->assertSessionHasNoErrors();

        return Question::latest('id')->firstOrFail();
    }

    private function answer(Question $question, User $by, string $body = 'Gradient is change in y over change in x: (8 - 2) / (4 - 1) = 2.'): Answer
    {
        $this->actingAs($by)->post(route('qa.answers.store', $question), ['body' => $body])->assertSessionHasNoErrors();

        return Answer::latest('id')->firstOrFail();
    }

    public function test_guests_can_read_but_not_post(): void
    {
        $question = $this->ask();
        auth()->logout();

        $this->get(route('qa.index'))->assertOk()->assertSee($question->title)->assertSee('Ama')->assertDontSee('Owusu')->assertSee('Sign in to ask');
        $this->get(route('qa.show', $question))->assertOk()->assertSee('I keep getting a negative answer.')->assertSee('to answer');

        $this->get(route('qa.create'))->assertRedirect(route('login'));
        $this->post(route('qa.store'), $this->questionData())->assertRedirect(route('login'));
        $this->post(route('qa.answers.store', $question), ['body' => 'Hello there'])->assertRedirect(route('login'));
        $this->post(route('qa.report', $question), ['reason' => 'spam'])->assertRedirect(route('login'));
        $this->assertSame(1, Question::count());
        $this->assertSame(0, Answer::count());
    }

    public function test_student_asks_and_feed_filters(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('qa.create'))->assertOk()->assertSee('Ask a question');
        $this->actingAs($student)->post(route('qa.store'), $this->questionData())
            ->assertRedirect(route('qa.show', Question::firstOrFail()));
        $maths = Question::firstOrFail();
        $this->assertSame($student->id, $maths->user_id);

        $jhs = EducationLevel::where('code', 'JHS')->firstOrFail();
        $science = Subject::where('education_level_id', $jhs->id)->where('is_active', true)->firstOrFail();
        $this->ask($student, ['title' => 'Why do plants need sunlight to make food?', 'education_level_id' => $jhs->id, 'class_level_id' => null, 'subject_id' => $science->id]);

        $this->get(route('qa.index', ['level' => 'shs']))->assertOk()->assertSee($maths->title)->assertDontSee('Why do plants need sunlight');
        $this->get(route('qa.index', ['subject' => $science->slug]))->assertOk()->assertSee('Why do plants need sunlight')->assertDontSee($maths->title);

        $this->answer($maths, User::factory()->student()->create());
        $this->get(route('qa.index', ['unanswered' => 1]))->assertOk()->assertSee('Why do plants need sunlight')->assertDontSee($maths->title);
        $this->get(route('qa.index'))->assertSee('1 answer');
    }

    public function test_question_validation_rejects_html_and_wrong_level_tags(): void
    {
        $student = User::factory()->student()->create();
        $jhs = EducationLevel::where('code', 'JHS')->firstOrFail();

        $this->actingAs($student)->post(route('qa.store'), $this->questionData(['body' => 'Look at this <script>alert(1)</script> please']))
            ->assertSessionHasErrors('body');
        // An SHS class with the JHS level.
        $this->actingAs($student)->post(route('qa.store'), $this->questionData(['education_level_id' => $jhs->id, 'subject_id' => null]))
            ->assertSessionHasErrors('class_level_id');
        $this->assertSame(0, Question::count());

        // Maths comparisons are not HTML.
        $this->actingAs($student)->post(route('qa.store'), $this->questionData(['body' => 'If x < y and y > 3, is x < 3 always true?']))
            ->assertSessionHasNoErrors();
    }

    public function test_output_is_escaped(): void
    {
        $question = $this->ask(null, ['title' => 'What does "a & b" mean in sets questions?']);
        $this->answer($question, User::factory()->student()->create(), 'It is the intersection: elements in both. Try x<y too.');

        $this->get(route('qa.show', $question))->assertOk()
            ->assertSee('What does &quot;a &amp; b&quot; mean', false)
            ->assertSee('Try x&lt;y too.', false);
    }

    public function test_answering_notifies_the_asker_and_teacher_answers_get_a_badge(): void
    {
        $asker = User::factory()->student()->create();
        $question = $this->ask($asker);

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create(['name' => 'Kofi Mensah']);
        $this->answer($question, $teacher);
        $notices = fn () => NotificationMessage::where('user_id', $asker->id)->where('event_key', 'qa.answered')->count();
        // SMS and email.
        $this->assertSame(2, $notices());

        // Answering your own question tells nobody.
        $this->answer($question, $asker, 'Thanks, I see where I went wrong.');
        $this->assertSame(2, $notices());

        // A pending teacher does not get the badge.
        $this->answer($question, User::factory()->teacher()->create(), 'Rise over run, so 6 over 3.');
        $this->assertSame(4, $notices());

        $html = $this->get(route('qa.show', $question))->assertOk()->assertSee('Kofi')->getContent();
        $this->assertSame(1, substr_count($html, '</i>Teacher</span>'));
    }

    public function test_asker_or_moderator_accepts_an_answer_others_cannot(): void
    {
        $asker = User::factory()->student()->create();
        $question = $this->ask($asker);
        $answer = $this->answer($question, User::factory()->student()->create());

        $other = User::factory()->student()->create();
        $this->actingAs($other)->post(route('qa.accept', [$question, $answer]))->assertForbidden();
        $this->actingAs(User::factory()->staff('Finance Officer')->create())->post(route('qa.accept', [$question, $answer]))->assertForbidden();
        $this->assertNull($question->fresh()->accepted_answer_id);

        $this->actingAs($asker)->post(route('qa.accept', [$question, $answer]))->assertRedirect();
        $this->assertSame($answer->id, $question->fresh()->accepted_answer_id);
        $this->get(route('qa.show', $question))->assertSee('Accepted');
        $this->get(route('qa.index'))->assertSee('Answered');

        // Again to undo; a moderator can set it too.
        $this->actingAs($asker)->post(route('qa.accept', [$question, $answer]));
        $this->assertNull($question->fresh()->accepted_answer_id);
        $this->actingAs(User::factory()->staff('Content Manager')->create())->post(route('qa.accept', [$question, $answer]))->assertRedirect();
        $this->assertSame($answer->id, $question->fresh()->accepted_answer_id);

        // An answer from another question cannot be accepted here.
        $elsewhere = $this->answer($this->ask($other, ['title' => 'Another question about straight lines']), $asker);
        $this->actingAs($asker)->post(route('qa.accept', [$question, $elsewhere]))->assertNotFound();
    }

    public function test_only_authors_edit_and_delete_their_posts(): void
    {
        $asker = User::factory()->student()->create();
        $question = $this->ask($asker);
        $answerer = User::factory()->teacher()->create();
        $answer = $this->answer($question, $answerer);
        $other = User::factory()->student()->create();

        $this->actingAs($other)->get(route('qa.edit', $question))->assertNotFound();
        $this->actingAs($other)->put(route('qa.update', $question), $this->questionData(['title' => 'Hijacked question title']))->assertNotFound();
        $this->actingAs($other)->delete(route('qa.destroy', $question))->assertNotFound();
        $this->actingAs($other)->put(route('qa.answers.update', $answer), ['body' => 'Hijacked'])->assertNotFound();
        $this->actingAs($other)->delete(route('qa.answers.destroy', $answer))->assertNotFound();
        $this->assertSame($question->title, $question->fresh()->title);

        $this->actingAs($asker)->get(route('qa.edit', $question))->assertOk();
        $this->actingAs($asker)->put(route('qa.update', $question), $this->questionData(['title' => 'Finding the gradient between two points']))->assertRedirect(route('qa.show', $question));
        $this->assertSame('Finding the gradient between two points', $question->fresh()->title);
        $this->assertNotNull($question->fresh()->edited_at);

        $this->actingAs($answerer)->get(route('qa.answers.edit', $answer))->assertOk();
        $this->actingAs($answerer)->put(route('qa.answers.update', $answer), ['body' => 'Use (y2 - y1) / (x2 - x1). Here that is 2.'])->assertRedirect();
        $this->assertStringContainsString('y2 - y1', $answer->fresh()->body);
        $this->actingAs($answerer)->delete(route('qa.answers.destroy', $answer))->assertRedirect(route('qa.show', $question));
        $this->assertNull(Answer::find($answer->id));

        $this->answer($question, $answerer);
        $this->actingAs($asker)->delete(route('qa.destroy', $question))->assertRedirect();
        $this->assertSame(0, Question::count());
        $this->assertSame(0, Answer::count());
    }

    public function test_staff_cannot_post_but_can_read(): void
    {
        $question = $this->ask();
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('qa.show', $question))->assertOk()->assertDontSee('Post answer');
        $this->actingAs($staff)->post(route('qa.store'), $this->questionData())->assertRedirect(route('admin.dashboard'));
        $this->assertSame(1, Question::count());
    }

    public function test_reports_hide_a_post_at_the_threshold(): void
    {
        config(['platform.qa.hide_after_reports' => 3]);
        $asker = User::factory()->student()->create();
        $question = $this->ask($asker);

        // Authors cannot report themselves, and one person counts once.
        $this->actingAs($asker)->post(route('qa.report', $question), ['reason' => 'spam'])->assertSessionHas('error');
        $first = User::factory()->student()->create();
        $this->actingAs($first)->post(route('qa.report', $question), ['reason' => 'spam'])->assertSessionHas('success');
        $this->actingAs($first)->post(route('qa.report', $question), ['reason' => 'rude'])->assertSessionHas('status');
        $this->actingAs(User::factory()->student()->create())->post(route('qa.report', $question), ['reason' => 'wrong', 'note' => 'Copied from a past paper']);
        $this->assertSame(2, $question->reports()->count());
        $this->assertTrue($question->fresh()->isVisible());

        $this->actingAs(User::factory()->teacher()->create())->post(route('qa.report', $question), ['reason' => 'cheating']);
        $question->refresh();
        $this->assertSame('hidden', $question->status);
        $this->assertSame('reports', $question->hidden_reason);
        $this->assertTrue(AuditLog::where('action', 'qa.auto-hidden')->exists());

        // Hidden from the public, still there for its author.
        auth()->logout();
        $this->get(route('qa.index'))->assertDontSee($question->title);
        $this->get(route('qa.show', $question))->assertNotFound();
        $this->actingAs($asker)->get(route('qa.show', $question))->assertOk()->assertSee('Only you and our moderators');
        $this->actingAs($asker)->get(route('qa.index', ['mine' => 1]))->assertSee($question->title)->assertSee('Hidden for review');
    }

    public function test_reported_answers_are_hidden_from_others(): void
    {
        config(['platform.qa.hide_after_reports' => 2]);
        $question = $this->ask();
        $author = User::factory()->student()->create();
        $answer = $this->answer($question, $author, 'Buy answers at my shop, call 0240000000');

        foreach (range(1, 2) as $i) {
            $this->actingAs(User::factory()->student()->create())->post(route('qa.answers.report', $answer), ['reason' => 'spam']);
        }
        $this->assertSame('hidden', $answer->fresh()->status);

        $this->actingAs(User::factory()->student()->create())->get(route('qa.show', $question))->assertOk()->assertDontSee('Buy answers at my shop');
        $this->actingAs($author)->get(route('qa.show', $question))->assertSee('Buy answers at my shop');
        $this->get(route('qa.index'))->assertSee('0 answers');
    }

    public function test_admin_reviews_reports_and_hides_restores_or_deletes(): void
    {
        config(['platform.qa.hide_after_reports' => 2]);
        $question = $this->ask();
        $answer = $this->answer($question, User::factory()->student()->create(), 'This is a rude answer.');
        $reporter = User::factory()->student()->create();
        $this->actingAs($reporter)->post(route('qa.report', $question), ['reason' => 'wrong']);
        $this->actingAs($reporter)->post(route('qa.answers.report', $answer), ['reason' => 'rude', 'note' => 'Insults the asker']);

        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->get(route('admin.qa.index'))->assertForbidden();
        $this->actingAs($finance)->post(route('admin.qa.hide', ['answer', $answer->id]))->assertForbidden();
        $this->actingAs(User::factory()->student()->create())->get(route('admin.qa.index'))->assertRedirect(route('learn.dashboard'));

        $admin = User::factory()->staff('Content Manager')->create();
        $this->actingAs($admin)->get(route('admin.qa.index'))->assertOk()
            ->assertSee($question->title)->assertSee('This is a rude answer.')->assertSee('Insults the asker');

        // Hide the answer: its reports close.
        $this->actingAs($admin)->post(route('admin.qa.hide', ['answer', $answer->id]))->assertRedirect();
        $this->assertSame('hidden', $answer->fresh()->status);
        $this->assertSame('moderator', $answer->fresh()->hidden_reason);
        $this->assertSame(0, $answer->openReports()->count());
        $this->actingAs($admin)->get(route('admin.qa.index', ['tab' => 'hidden']))->assertOk()->assertSee('This is a rude answer.');
        $this->actingAs($admin)->get(route('qa.show', $question))->assertOk()->assertSee('This is a rude answer.');

        // Keep the question: visible, reports closed, gone from the list.
        $this->actingAs($admin)->post(route('admin.qa.restore', ['question', $question->id]))->assertRedirect();
        $this->assertTrue($question->fresh()->isVisible());
        $this->assertSame(0, Report::whereNull('resolved_at')->count());
        $this->actingAs($admin)->get(route('admin.qa.index'))->assertSee('No open reports.');

        // Restore the answer, then delete it.
        $this->actingAs($admin)->post(route('admin.qa.restore', ['answer', $answer->id]));
        $this->assertTrue($answer->fresh()->isVisible());
        $this->actingAs($admin)->delete(route('admin.qa.destroy', ['answer', $answer->id]))->assertRedirect();
        $this->assertNull(Answer::find($answer->id));
        $this->assertSame(0, Report::where('reportable_type', (new Answer)->getMorphClass())->count());

        $this->actingAs($admin)->delete(route('admin.qa.destroy', ['question', $question->id]))->assertRedirect();
        $this->assertSame(0, Question::count());
        $this->assertSame(0, Report::count());

        foreach (['qa.answer.hidden', 'qa.question.restored', 'qa.answer.deleted', 'qa.question.deleted'] as $action) {
            $this->assertTrue(AuditLog::where('action', $action)->exists(), $action);
        }
    }

    public function test_posting_is_rate_limited(): void
    {
        $student = User::factory()->student()->create();
        foreach (range(1, 5) as $i) {
            $this->actingAs($student)->post(route('qa.store'), $this->questionData(['title' => "Question number {$i} about gradients"]))->assertRedirect();
        }
        $this->actingAs($student)->post(route('qa.store'), $this->questionData())->assertStatus(429);
        $this->assertSame(5, Question::count());
    }
}
