<?php

namespace App\Services\Videos;

use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use Database\Seeders\DemoQuestionSeeder;
use Illuminate\Support\Str;

/**
 * Files a maths video under a curriculum topic from the words in its title, so the video page can
 * group a channel's uploads by topic. Rules run top to bottom and the first match wins, so the
 * specific ones come before the catch-all groups (olympiad, real life, shortcuts).
 *
 * Topic titles are the ones the practice question bank uses (see DemoQuestionSeeder); the few that
 * have no curriculum equivalent, such as "Math Olympiad problems", are created as supplementary
 * topics the first time a video needs them.
 */
class VideoTopicClassifier
{
    public const CORE = 'core-mathematics';

    public const ELECTIVE = 'elective-mathematics';

    /** Groups that are not curriculum topics; the video page lists them after the lesson topics. */
    public const EXTRA_TOPICS = ['Math Olympiad problems', 'SAT preparation', 'Teacher licensure exam preparation', 'Revision shows', 'Mathematics in real life', 'Shortcuts, puzzles and challenges'];

    /** Clips that are not lessons: competition footage from other broadcasters, greetings, teasers. */
    private const NOT_LESSONS = '/nsmq ?\d{4}|nsmq final|merry christmas|happy new year|father\'?s day|prempeh|presec|prsec|mfantsipim|owass|adisco|augusco|keta shts|simms sinks|kofi agyei|ghana national took|^(may|june) \d{1,2}, \d{4}$|^otisam mathematics$/i';

    /** [pattern, subject slug, topic title]; first match wins. */
    private const RULES = [
        ['/olympiad|^mo \d/i', self::CORE, 'Math Olympiad problems'],
        ['/\bsat\b/i', self::CORE, 'SAT preparation'],
        ['/gtle|licen[cs]i/i', self::CORE, 'Teacher licensure exam preparation'],
        ['/revision show/i', self::CORE, 'Revision shows'],

        // Elective mathematics
        ['/differentiat|derivative|product rule|quotient rule|first principle|limit/i', self::ELECTIVE, 'Limits and differentiation'],
        ['/matri|determinant|cramer/i', self::ELECTIVE, 'Matrices'],
        ['/binomial|pascal/i', self::ELECTIVE, 'Sets and binomial expansions'],
        ['/polynomial|remainder|reminder|rem theorem|factor theorem|cubic function|rational function/i', self::ELECTIVE, 'Polynomial functions'],
        ['/(?<!symmetric )function|f01 |lesson 4g/i', self::ELECTIVE, 'Sequences and functions'],
        ['/permutation|combination|factorial|0!|arranging/i', self::ELECTIVE, 'Permutations, combinations and probability'],
        ['/quadratic inequalit/i', self::ELECTIVE, 'Sequences, series and inequalities'],
        ['/equation of (a )?circle|eoc\d|tangent of a circle|tangent to a circle|touch of a circle|touch externally|locus/i', self::ELECTIVE, 'Circles and loci'],
        ['/trig identit|compound angle|multiple angle|trig expression|simplify the identity/i', self::ELECTIVE, 'Trigonometric identities'],

        // Core mathematics
        ['/coordinate geometry|perpendicular/i', self::CORE, 'Coordinate geometry: distance, midpoint and gradient'],
        ['/circle theorem/i', self::CORE, 'Circle theorems and constructions'],
        ['/trigonometry|cos 90|complementary angle|elevation|depression/i', self::CORE, 'Trigonometry: right-angled triangles, elevation and depression'],
        ['/bearing/i', self::CORE, 'Bearings and distances'],
        ['/surd|radical|lesson 2c/i', self::CORE, 'Surds'],
        ['/log|four figure table|characteristic/i', self::CORE, 'Indices and logarithms'],
        ['/indic|indices|power/i', self::CORE, 'Indices and logarithms'],
        ['/sequence|progression|number pattern|number of terms/i', self::CORE, 'Arithmetic and geometric progressions'],
        ['/quadratic graph/i', self::CORE, 'Graphs of quadratic functions'],
        ['/trinomial/i', self::CORE, 'Expansion, factorisation and change of subject'],
        ['/quadratic|qe0|almighty formula|discriminant/i', self::CORE, 'Quadratic equations'],
        ['/simultaneous/i', self::CORE, 'Simultaneous equations'],
        ['/inequalit/i', self::CORE, 'Linear inequalities and linear programming'],
        ['/linear equation|linear form/i', self::CORE, 'Linear equations and word problems'],
        ['/change of subject|substitution/i', self::CORE, 'Expansion, factorisation and change of subject'],
        ['/algebraic|alg0|alge0|factori[sz]|difference of two squares|common factor|unlike terms|distributive|building block 2|block 2:/i', self::CORE, 'Expansion, factorisation and change of subject'],
        ['/binary operation/i', self::CORE, 'Algebraic fractions and binary operations'],
        ['/variation/i', self::CORE, 'Variation'],
        ['/percentage|profit|discount|commission|interest|borrowing|grow your money/i', self::CORE, 'Interest, depreciation and percentages'],
        ['/vector/i', self::CORE, 'Vectors in two dimensions'],
        ['/probability|mutually exclusive/i', self::CORE, 'Probability of combined events'],
        ['/variance|standard deviation/i', self::CORE, 'Measures of dispersion'],
        ['/\bmean\b|median|class boundar/i', self::CORE, 'Statistics: grouped data and cumulative frequency'],
        ['/\bsets?\b|venn/i', self::CORE, 'Sets and Venn diagrams'],
        ['/pythagor/i', self::CORE, 'Angles, polygons and Pythagoras'],
        ['/parallel lines|angles in real life/i', self::CORE, 'Angles, polygons and parallel lines'],
        ['/circumference|\bpi\b|2πr/i', self::CORE, 'Mensuration: circles, arcs and sectors'],
        ['/metre length/i', self::CORE, 'Perimeter, area and volume'],
        ['/significant figure/i', self::CORE, 'Real numbers, approximation and significant figures'],
        ['/fraction|bodmas|2\.0 and 2/i', self::CORE, 'Fractions, decimals and percentages'],
        ['/integer|prime|zero|\(0\)|by 0|rational|22\/7|reciprocal|inverse|identity element|closure|associative|commutative|divisi|dividing|multipl|addition|subtraction|squaring|perfect square|real number|negative|number 1|itself|arithmetic|building block|block 1/i', self::CORE, 'Number sets and properties'],

        // Catch-all groups
        ['/real life|real world|practical|daily life|shape our world|pizza|year 2025/i', self::CORE, 'Mathematics in real life'],
        ['/short ?cut|puzzle|challenge|solve|genius|wrong|hard|confused|simple|secret|succeed|evaluate|learn|easiest|easy|tricky|seconds/i', self::CORE, 'Shortcuts, puzzles and challenges'],
    ];

    /** @var array<string, CurriculumNode> */
    private array $nodes = [];

    /** @var array<string, ?Subject> */
    private array $subjects = [];

    public function isLesson(string $title): bool
    {
        return ! preg_match(self::NOT_LESSONS, trim($title));
    }

    /**
     * The subject slug and topic title for a video title, or null when no rule matches.
     *
     * @return array{subject: string, topic: string}|null
     */
    public function classify(string $title): ?array
    {
        if (! $this->isLesson($title)) {
            return null;
        }
        foreach (self::RULES as [$pattern, $subject, $topic]) {
            if (preg_match($pattern, $title)) {
                return ['subject' => $subject, 'topic' => $topic];
            }
        }

        return null;
    }

    /**
     * Subject, topic node and class level for a title, creating a supplementary topic when needed.
     *
     * @return array{subject: Subject, node: CurriculumNode}|null
     */
    public function resolve(string $title): ?array
    {
        $match = $this->classify($title);
        $subject = $match ? $this->subject($match['subject']) : null;
        if (! $subject) {
            return null;
        }

        return ['subject' => $subject, 'node' => $this->node($subject, $match['topic'])];
    }

    private function subject(string $slug): ?Subject
    {
        return $this->subjects[$slug] ??= Subject::where('slug', $slug)->first();
    }

    private function node(Subject $subject, string $topic): CurriculumNode
    {
        $key = $subject->id.'|'.Str::lower($topic);
        if (isset($this->nodes[$key])) {
            return $this->nodes[$key];
        }

        // Prefer a top-level topic of that name in any version (the question bank's topics),
        // so videos and practice questions share the same groups.
        $node = CurriculumNode::where('subject_id', $subject->id)->whereNull('parent_id')->where('title', $topic)->orderBy('id')->first();
        if (! $node) {
            $version = CurriculumVersion::firstOrCreate(
                ['education_level_id' => $subject->education_level_id, 'name' => DemoQuestionSeeder::TOPIC_VERSION],
                ['status' => 'active', 'notes' => 'Topic groups for the practice question bank, written by Otisam. Not the official curriculum document.'],
            );
            $node = new CurriculumNode(['node_type' => 'topic', 'title' => $topic, 'is_official' => false]);
            $node->forceFill([
                'curriculum_version_id' => $version->id,
                'subject_id' => $subject->id,
                'sort' => (int) CurriculumNode::where('curriculum_version_id', $version->id)->where('subject_id', $subject->id)->max('sort') + 1,
                'depth' => 0,
                'path' => '/',
            ])->save();
        }

        return $this->nodes[$key] = $node;
    }
}
