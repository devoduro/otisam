<?php

namespace App\Services\Practice;

use App\Enums\AccountType;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Points from saved attempts. Each practice paper counts once, with the student's best score on
 * it, so sitting the same paper again cannot inflate points; practice sets add their correct
 * answers. Only students are ranked.
 */
class Leaderboard
{
    /** @return Collection<int, object{user_id: int, name: string, points: int, attempts: int}> */
    public function top(?int $subjectId = null, ?CarbonInterface $since = null, int $limit = 20): Collection
    {
        $filtered = fn ($q) => $q
            ->when($subjectId, fn ($w) => $w->where('subject_id', $subjectId))
            ->when($since, fn ($w) => $w->where('created_at', '>=', $since));

        // Best score per paper per student, then practice sets as they are.
        $papers = $filtered(DB::table('quiz_attempts')->where('kind', 'paper'))
            ->selectRaw('user_id, max(score) as points, count(*) as attempts')
            ->groupBy('user_id', 'exam_paper_id');
        $practice = $filtered(DB::table('quiz_attempts')->where('kind', 'practice'))
            ->selectRaw('user_id, score as points, 1 as attempts');

        return DB::query()->fromSub($papers->unionAll($practice), 'p')
            ->join('users', 'users.id', '=', 'p.user_id')
            ->where('users.account_type', AccountType::Student->value)
            ->selectRaw('p.user_id, users.name, sum(p.points) as points, sum(p.attempts) as attempts')
            ->groupBy('p.user_id', 'users.name')
            ->orderByDesc('points')->orderBy('p.user_id')
            ->limit($limit)->get()
            ->map(function ($row) {
                $row->points = (int) $row->points;
                $row->attempts = (int) $row->attempts;
                $row->name = self::shortName($row->name);

                return $row;
            });
    }

    /** "Ama Serwaa Owusu" → "Ama O.": enough to recognise yourself without publishing full names of minors. */
    public static function shortName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        if (count($parts) < 2) {
            return $parts[0] ?? 'Student';
        }

        return $parts[0].' '.mb_strtoupper(mb_substr(end($parts), 0, 1)).'.';
    }
}
