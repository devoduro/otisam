{{--
    One lesson plan on a plain page: printed (or saved as PDF) from the browser, and served as a .doc
    file ($forWord) that Word and LibreOffice open for editing. Inline styles and tables only, so it
    survives the trip into a word processor.
--}}
@php
    use App\Models\LessonPlans\LessonPlan;
    $cell = 'border:1px solid #9ca3af;padding:6px 8px;vertical-align:top;';
    $head = $cell.'background:#eef0f7;font-weight:bold;width:28%;';
    $rows = [
        'Subject' => $plan->subject?->labelWithLevel(),
        'Year' => $plan->classLevel?->name ?? 'Any year',
        'Term / week' => $plan->weekLabel(),
        'Lessons this week' => $plan->lessons_per_week ? $plan->lessons_per_week.($plan->duration_minutes ? ' × '.$plan->duration_minutes.' minutes' : '') : ($plan->duration_minutes ? $plan->duration_minutes.' minutes' : null),
        'Class size' => $plan->class_size,
        'Strand' => $plan->strand,
        'Sub-strand' => $plan->sub_strand,
        'Content standard' => $plan->content_standard,
        'Learning indicators' => $plan->indicators,
        'Core competencies' => $plan->core_competencies,
        'Key words' => $plan->key_vocabulary,
        'Resources' => $plan->resources,
    ];
    $after = ['Assessment' => $plan->assessment, 'Differentiation' => $plan->differentiation, 'Homework' => $plan->homework, 'Teacher notes' => $plan->teacher_notes];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $plan->title }} · Lesson plan</title>
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #111827; margin: 24px auto; max-width: 820px; padding: 0 16px; background: #fff; }
        h1 { font-size: 17pt; margin: 0 0 4px; }
        h2 { font-size: 12.5pt; margin: 18px 0 6px; }
        table { border-collapse: collapse; width: 100%; }
        .muted { color: #6b7280; font-size: 9.5pt; }
        .toolbar { display: flex; gap: 8px; margin-bottom: 16px; }
        .toolbar a, .toolbar button { font: inherit; padding: 8px 14px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; color: #1f2a5c; cursor: pointer; text-decoration: none; }
        .toolbar button { background: #1f2a5c; color: #fff; border-color: #1f2a5c; }
        @media print { .toolbar { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    @unless ($forWord)
        <div class="toolbar">
            <button type="button" onclick="window.print()">Print or save as PDF</button>
            <a href="{{ route('lesson-plans.download', $plan) }}">Download (Word)</a>
            <a href="{{ route('lesson-plans.show', $plan) }}">Back to the plan</a>
        </div>
    @endunless

    <h1>{{ $plan->title }}</h1>
    <p class="muted">Lesson plan by {{ $plan->authorName() }} · {{ config('app.name') }}</p>

    <table>
        @foreach ($rows as $label => $value)
            @continue(blank($value))
            <tr><td style="{{ $head }}">{{ $label }}</td><td style="{{ $cell }}">{!! nl2br(e($value)) !!}</td></tr>
        @endforeach
    </table>

    <h2>Lesson flow</h2>
    <table>
        <tr>
            <td style="{{ $cell }}background:#eef0f7;font-weight:bold;width:22%;">Phase</td>
            <td style="{{ $cell }}background:#eef0f7;font-weight:bold;">Teacher and learner activities</td>
        </tr>
        @foreach (LessonPlan::PHASES as $field => [$label])
            @continue(blank($plan->{$field}))
            <tr><td style="{{ $head }}width:22%;">{{ $loop->iteration }}. {{ $label }}</td><td style="{{ $cell }}">{!! nl2br(e($plan->{$field})) !!}</td></tr>
        @endforeach
    </table>

    @foreach ($after as $label => $value)
        @continue(blank($value))
        <h2>{{ $label }}</h2>
        <p style="margin:0;">{!! nl2br(e($value)) !!}</p>
    @endforeach

    <p class="muted" style="margin-top:24px;">Printed from {{ route('lesson-plans.show', $plan) }}</p>
</body>
</html>
