<?php

namespace Database\Seeders;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\LessonPlans\LessonPlan;
use Illuminate\Database\Seeder;

/**
 * A handful of sample weekly lesson plans so Lesson plans is not empty on day one. Every word was
 * written for Otisam; none is taken from another site or from official curriculum documents.
 * Safe to run again: plans are matched by subject and title and left alone if they exist.
 */
class LessonPlanSeeder extends Seeder
{
    public function run(): void
    {
        $added = 0;
        foreach ($this->plans() as $data) {
            $subject = Subject::where('slug', $data['subject'])->first();
            if (! $subject || LessonPlan::withTrashed()->where('subject_id', $subject->id)->where('title', $data['title'])->exists()) {
                continue;
            }

            $plan = new LessonPlan(collect($data)->except(['subject', 'year', 'topic'])->all());
            $plan->subject_id = $subject->id;
            $plan->class_level_id = ClassLevel::where('education_level_id', $subject->education_level_id)->where('name', $data['year'])->value('id');
            // Link to a matching topic when the subject's tree has one (e.g. the practice topics).
            $plan->curriculum_node_id = CurriculumNode::where('subject_id', $subject->id)->where('title', 'like', '%'.$data['topic'].'%')->orderBy('depth')->value('id');
            $plan->status = 'published';
            $plan->published_at = now();
            $plan->save();
            $added++;
        }

        $this->command?->info("Lesson plans: {$added} added.");
    }

    private function plans(): array
    {
        return [
            [
                'subject' => 'core-mathematics', 'year' => 'SHS 1', 'term' => 1, 'week' => 4, 'topic' => 'Fraction',
                'title' => 'Adding and subtracting fractions with unlike denominators',
                'strand' => 'Number', 'sub_strand' => 'Fractions, decimals and percentages',
                'content_standard' => 'Learners carry out the four operations on fractions and use them to solve everyday problems.',
                'indicators' => "Find the lowest common multiple of two or three denominators\nAdd and subtract fractions and mixed numbers with unlike denominators\nCheck an answer by estimating with whole numbers",
                'core_competencies' => "Critical thinking and problem solving\nCommunication and collaboration\nPersonal development (perseverance)",
                'key_vocabulary' => 'numerator, denominator, equivalent fraction, lowest common multiple, mixed number, improper fraction',
                'lessons_per_week' => 3, 'duration_minutes' => 60, 'class_size' => 45,
                'resources' => "Paper strips (one A4 sheet per pair)\nA market price list written on the board\nExercise books and coloured pens",
                'starter' => 'Write 1/2 + 1/3 on the board and ask three learners to give a quick guess. Most will say 2/5. Fold a paper strip into halves and another into thirds and hold them side by side: is 2/5 really bigger than 1/2? Let pairs argue for two minutes before you move on.',
                'main_activities' => "1. Pairs fold strips into halves, thirds and sixths and shade 1/2 and 1/3 on the sixths strip. They record 1/2 = 3/6 and 1/3 = 2/6, so the sum is 5/6.\n2. Ask: why did sixths work? Draw out that 6 is a multiple of both 2 and 3. Introduce the lowest common multiple and list multiples to find it for 4 and 6, then 3, 4 and 8.\n3. Model two examples on the board, saying each step aloud: find the LCM, rewrite each fraction, add or subtract the numerators, simplify.\n4. Mixed numbers: a trader sells 2 3/4 kg of rice in the morning and 1 2/3 kg in the afternoon. Groups solve it two ways (whole numbers first, then improper fractions) and compare.\n5. Learners work through eight graded questions. Circulate, and pull a small group to the front table for extra modelling if needed.",
                'reflection' => "Learners write one sentence on a slip: 'The mistake I will not make again is…'. Read a few aloud. Close by returning to 1/2 + 1/3 and asking a learner to explain why 2/5 was wrong.",
                'assessment' => 'Exit questions: (a) 3/4 + 1/6  (b) 5/8 − 1/3  (c) 3 1/2 − 1 4/5. A learner who gets (c) right has met all three indicators. Mark in class; note who needs support in the next lesson.',
                'differentiation' => "Support: keep the folded strips on the desk and limit the work to denominators below 12.\nExtension: write a word problem that needs 2/3 + 3/5 and swap it with a partner.",
                'homework' => 'Find two prices at home or in the market that use halves or quarters (e.g. half a bag of gari) and write a subtraction problem from them.',
                'teacher_notes' => 'Learners often add the denominators. The paper strips in the starter are worth the time; do not skip them.',
            ],
            [
                'subject' => 'integrated-science', 'year' => 'SHS 1', 'term' => 1, 'week' => 6, 'topic' => 'Photosynthesis',
                'title' => 'What do plants need to make food? Investigating photosynthesis',
                'strand' => 'Systems', 'sub_strand' => 'Plant nutrition',
                'content_standard' => 'Learners explain how green plants make their own food and investigate the conditions it needs.',
                'indicators' => "State the raw materials and products of photosynthesis\nCarry out a fair test to show that light is needed for starch to form in a leaf\nWrite the word equation for photosynthesis",
                'core_competencies' => "Critical thinking and problem solving\nCollaboration\nDigital literacy (recording results in a table)",
                'key_vocabulary' => 'chlorophyll, starch, glucose, carbon dioxide, fair test, variable, control',
                'lessons_per_week' => 2, 'duration_minutes' => 80, 'class_size' => 40,
                'resources' => "A potted plant kept in the dark for two days, and one kept in sunlight\nAluminium foil or black paper, paper clips\nIodine solution, ethanol, hot water in a flask, white tiles\nSafety goggles",
                'starter' => 'Hold up a mango and ask: where did all this mass come from? The soil? The water? Learners vote with a show of hands, then hear that a large tree takes very little from the soil. Leave the question open; the lesson will answer it.',
                'main_activities' => "1. Revise the starch test with iodine on a slice of yam or cassava: blue-black means starch is present.\n2. Explain the plan: cover part of one leaf on the sunlit plant with foil the day before (done in advance) and compare it with an uncovered part.\n3. Groups follow the leaf-testing steps on the card: boil the leaf briefly in water, soften it in warm ethanol (no flames near the ethanol), rinse, then add iodine. The teacher demonstrates the ethanol step first.\n4. Groups record the colour of the covered and uncovered parts, and the leaf from the dark plant, in a results table.\n5. Discussion: which part is the control? What was the one variable changed? Build the word equation on the board from what learners say: carbon dioxide + water → (light, chlorophyll) → glucose + oxygen.",
                'reflection' => "Return to the mango. Each group writes a two-line answer to 'Where did the mass come from?' using the words carbon dioxide and glucose, and one group reads theirs out.",
                'assessment' => 'Short quiz: label the inputs and outputs of photosynthesis on a leaf diagram; explain why the covered part stayed brown with iodine; name one thing that made the test fair.',
                'differentiation' => "Support: give a partly completed results table and word equation with gaps.\nExtension: plan a test to show that carbon dioxide is needed, naming the control and the variable.",
                'homework' => 'Write a paragraph explaining to a farmer why leaves should not be removed from young maize plants.',
                'teacher_notes' => 'Prepare the dark plant two days ahead. Keep ethanol well away from any flame; warm it in a beaker standing in hot water from a flask.',
            ],
            [
                'subject' => 'english-language', 'year' => 'SHS 2', 'term' => 1, 'week' => 3, 'topic' => 'Letter',
                'title' => 'Writing a formal letter of complaint',
                'strand' => 'Writing', 'sub_strand' => 'Formal and transactional writing',
                'content_standard' => 'Learners write formal letters that suit their purpose and audience, using the right layout and tone.',
                'indicators' => "Set out a formal letter with both addresses, date, salutation, title and closing\nOrganise the complaint into clear paragraphs: problem, effect, request\nUse a polite, firm tone and avoid slang",
                'core_competencies' => "Communication and collaboration\nCitizenship (raising concerns through proper channels)\nCritical thinking",
                'key_vocabulary' => 'salutation, subscript, tone, register, request, Yours faithfully, Yours sincerely',
                'lessons_per_week' => 2, 'duration_minutes' => 60, 'class_size' => 50,
                'resources' => "Two short letters written by the teacher about the same problem: one rude, one formal\nA layout chart on manila card",
                'starter' => 'Read both teacher-written letters about a broken borehole in the community. Learners decide which one the District Assembly is more likely to act on, and give two reasons.',
                'main_activities' => "1. Using the layout chart, label the parts of the formal letter together. Point out when to use 'Yours faithfully' and when 'Yours sincerely'.\n2. Plan a complaint in three paragraphs: what the problem is, how it affects people, what you want done and by when.\n3. In pairs, learners rewrite three rude sentences from the first letter in a formal tone (e.g. 'You people never do anything' → 'Several reports to your office have not yet been answered').\n4. Each learner chooses a situation (late school bus, noise from a drinking spot, a faulty phone bought from a shop) and writes the first draft.\n5. Peer check with a checklist: layout, three paragraphs, tone, request.",
                'reflection' => 'Learners underline the one sentence in their draft that they think is most persuasive and share it with a partner.',
                'assessment' => 'Mark the final draft out of 20: layout 5, content 5, organisation 5, language and tone 5. Return with one written comment per learner.',
                'differentiation' => "Support: give a letter frame with the layout already in place and sentence starters for each paragraph.\nExtension: write the reply the official might send.",
                'homework' => 'Finish the letter neatly for marking.',
                'teacher_notes' => 'Keep the rude letter light-hearted; learners enjoy correcting it.',
            ],
            [
                'subject' => 'jhs-mathematics', 'year' => 'JHS 1', 'term' => 2, 'week' => 2, 'topic' => 'Perimeter',
                'title' => 'Perimeter and area of rectangles in the school compound',
                'strand' => 'Geometry and measurement', 'sub_strand' => 'Measurement: perimeter and area',
                'content_standard' => 'Learners measure and calculate the perimeter and area of rectangles and solve related problems.',
                'indicators' => "Measure lengths in metres and centimetres accurately\nCalculate the perimeter and area of a rectangle\nTell perimeter and area apart and choose the right one for a problem",
                'core_competencies' => "Collaboration\nCritical thinking and problem solving\nCreativity",
                'key_vocabulary' => 'length, breadth, perimeter, area, square metre, estimate',
                'lessons_per_week' => 4, 'duration_minutes' => 50, 'class_size' => 40,
                'resources' => "Measuring tapes or knotted ropes marked every metre\nSquare grids drawn on cardboard (1 cm squares)\nChalk for marking the ground",
                'starter' => "Ask: the school wants to fence the garden and plant grass on it. Is the fence the same thing as the grass? Learners discuss for two minutes; collect the words 'around' and 'covering'.",
                'main_activities' => "1. Groups measure one rectangle each outside (garden bed, classroom floor, door, notice board) and sketch it with the measurements.\n2. Back in class, groups find the distance around their shape and share the method; agree on perimeter = 2 × (length + breadth).\n3. On the square grids, learners draw rectangles of 3 by 4 and 2 by 6 and count squares. Ask why both have the same area but different perimeters.\n4. Agree on area = length × breadth and its units (square metres).\n5. Groups work out the cost of fencing and of grass for their rectangle using prices written on the board.",
                'reflection' => 'Each learner completes: "Perimeter is used when… Area is used when…" and gives one example from home.',
                'assessment' => 'Five questions: two perimeter, two area, and one that asks which to use (e.g. tiling a floor). Check units in every answer.',
                'differentiation' => "Support: use the grid cards throughout and only whole-number lengths.\nExtension: find three rectangles with a perimeter of 24 m and say which has the largest area.",
                'homework' => 'Measure your bed or a table at home with a string and a ruler, then find its perimeter and area.',
                'teacher_notes' => 'Ask the headteacher in advance for permission to use the compound. Keep groups to five or fewer.',
            ],
            [
                'subject' => 'jhs-science', 'year' => 'JHS 2', 'term' => 1, 'week' => 5, 'topic' => 'Separation',
                'title' => 'Separating mixtures: getting clean water and salt from muddy salt water',
                'strand' => 'Diversity of matter', 'sub_strand' => 'Materials: mixtures and separation',
                'content_standard' => 'Learners choose and use suitable methods to separate mixtures and relate them to everyday activities.',
                'indicators' => "Describe filtration and evaporation\nChoose a separation method based on the properties of the parts of a mixture\nLink separation methods to local activities such as salt winning",
                'core_competencies' => "Critical thinking and problem solving\nCollaboration\nPersonal development (safety awareness)",
                'key_vocabulary' => 'mixture, filtrate, residue, solute, solvent, evaporation, filtration',
                'lessons_per_week' => 3, 'duration_minutes' => 60, 'class_size' => 45,
                'resources' => "Muddy salt water in labelled bottles\nFunnels and filter paper (or a clean cloth)\nEvaporating dishes or tin lids, a small burner or charcoal stove, tongs",
                'starter' => 'Show the muddy salt water and ask: how could we get drinkable-looking water and dry salt back out of this? Groups list as many ideas as they can in two minutes.',
                'main_activities' => "1. Discuss the ideas and sort them: which deal with the mud, which deal with the salt?\n2. Groups filter the mixture and describe the residue and the filtrate. Is the filtrate clean? Taste test is not allowed; explain why.\n3. The teacher heats a small amount of filtrate in a dish until the water evaporates. Learners observe from a safe distance and record what is left.\n4. Groups draw a flow chart of the two steps with labels.\n5. Link to salt winning along the coast: which step does the sun do for the salt makers?",
                'reflection' => 'Learners explain to a partner why filtering alone could not remove the salt.',
                'assessment' => 'Label a filtration diagram; choose and justify a method for sand and water, salt and water, and iron filings and sand.',
                'differentiation' => "Support: give the flow chart with blank boxes to fill.\nExtension: suggest how to get the water back as well as the salt (lead towards distillation).",
                'homework' => 'Find one way mixtures are separated at home (sieving flour, winnowing rice) and name the property that makes it work.',
                'teacher_notes' => 'Only the teacher handles the heat source. Keep a bucket of sand nearby.',
            ],
            [
                'subject' => 'jhs-social-studies', 'year' => 'JHS 1', 'term' => 1, 'week' => 3, 'topic' => 'Family',
                'title' => 'Roles and responsibilities in the family',
                'strand' => 'Environment', 'sub_strand' => 'The family and the community',
                'content_standard' => 'Learners describe the roles of family members and how sharing responsibilities helps the family and community.',
                'indicators' => "Describe the roles of different members of a family\nExplain how sharing duties helps a family live well together\nSuggest ways to share household work fairly",
                'core_competencies' => "Citizenship and cultural identity\nCommunication and collaboration\nCritical thinking",
                'key_vocabulary' => 'nuclear family, extended family, role, responsibility, cooperation',
                'lessons_per_week' => 2, 'duration_minutes' => 60, 'class_size' => 45,
                'resources' => "Chart paper and markers\nThree short role-play cards written by the teacher",
                'starter' => 'Learners list everything that happened in their home this morning before school (fetching water, sweeping, cooking, getting siblings ready). Collect the list on the board and ask who did each task.',
                'main_activities' => "1. Sort the tasks on the board by family member. Ask whether the sorting would look the same in every home.\n2. Groups act out the role-play cards: a home where one person does everything, and a home where tasks are shared. Others note what changes.\n3. Discuss: what happens to a family when duties are not shared? What happens when they are?\n4. Groups design a fair weekly duty rota for a family of six, with reasons.",
                'reflection' => 'Each learner writes one duty they will take on at home this week and why it helps.',
                'assessment' => 'Short answers: two roles of parents, two roles of children, and one reason sharing duties is good for the community.',
                'differentiation' => "Support: give picture cards of tasks to sort.\nExtension: compare roles in a nuclear and an extended family.",
                'homework' => 'Ask an older relative how household duties were shared when they were young; note one difference from today.',
                'teacher_notes' => 'Be sensitive: not every learner lives with both parents. Talk about "the people we live with".',
            ],
        ];
    }
}
