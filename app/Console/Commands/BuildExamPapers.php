<?php

namespace App\Console\Commands;

use App\Models\Curriculum\Subject;
use App\Services\Practice\PaperBuilder;
use Illuminate\Console\Command;

class BuildExamPapers extends Command
{
    protected $signature = 'papers:build
        {subject?* : Subject slugs (default: every active subject)}
        {--limit= : At most N new papers per subject}';

    protected $description = 'Build numbered WASSCE/BECE-style objective papers from unused practice questions';

    public function handle(PaperBuilder $builder): int
    {
        $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
        $subjects = Subject::active()->with('educationLevel')
            ->when($this->argument('subject'), fn ($q, $slugs) => $q->whereIn('slug', $slugs))
            ->orderBy('education_level_id')->orderBy('sort')->get();

        $total = 0;
        foreach ($subjects as $subject) {
            $made = $builder->build($subject, $limit);
            $total += $made;
            if ($made > 0) {
                $this->line("{$subject->labelWithLevel()}: {$made} new ".str('paper')->plural($made));
            }
        }
        $this->info("{$total} papers created.");

        return self::SUCCESS;
    }
}
