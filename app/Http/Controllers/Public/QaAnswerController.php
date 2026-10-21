<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Community\Answer;
use App\Models\Community\Question;
use App\Rules\PlainText;
use App\Services\Notifications\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Answers to community Q&A questions. The asker is told (SMS/email) when someone else answers. */
class QaAnswerController extends Controller
{
    public function __construct(private readonly Notifier $notifier) {}

    public function store(Request $request, Question $question): RedirectResponse
    {
        abort_unless($question->isVisible(), 404);
        $user = $request->user();

        $answer = new Answer($this->validated($request));
        $answer->qa_question_id = $question->id;
        $answer->user_id = $user->id;
        $answer->save();

        $asker = $question->author;
        if ($asker && $asker->id !== $user->id) {
            $app = config('app.name');
            $this->notifier->toUser($asker, 'qa.answered', 'transactional', "{$app}: your question has an answer",
                "Hello {$asker->name}, ".strtok(trim($user->name), ' ').' answered your question "'.Str::limit($question->title, 60)."\" on {$app} Q&A: ".route('qa.show', $question),
                "qa.answered:{$answer->id}");
        }

        return redirect()->to(route('qa.show', $question).'#answer-'.$answer->id)->with('success', 'Your answer is posted.');
    }

    public function edit(Request $request, Answer $answer): View
    {
        abort_unless($answer->isOwnedBy($request->user()), 404);

        return view('public.qa.answer-edit', ['answer' => $answer->load('question')]);
    }

    public function update(Request $request, Answer $answer): RedirectResponse
    {
        abort_unless($answer->isOwnedBy($request->user()), 404);
        $answer->fill($this->validated($request));
        if ($answer->isDirty()) {
            $answer->edited_at = now();
            $answer->save();
        }

        return redirect()->to(route('qa.show', $answer->qa_question_id).'#answer-'.$answer->id)->with('success', 'Answer updated.');
    }

    public function destroy(Request $request, Answer $answer): RedirectResponse
    {
        abort_unless($answer->isOwnedBy($request->user()), 404);
        $questionId = $answer->qa_question_id;
        $answer->delete();

        return redirect()->route('qa.show', $questionId)->with('success', 'Answer deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000', new PlainText],
        ], [], ['body' => 'answer']);
    }
}
