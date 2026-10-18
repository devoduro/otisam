<?php

/*
| Demo practice questions in the style of the GES teacher promotion examinations (general paper for
| all ranks): education acts and agencies, teacher standards, curriculum, educational psychology,
| assessment, school management and professional conduct.
|
| Written for Otisam; not copied from any exam body or website.
|
| subject slug => list of [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    'ges-promotion-general' => [
        // Education law and agencies
        ['Which law currently provides the legal framework for pre-tertiary education in Ghana?', ['Education Act, 1961 (Act 87)', 'Education Act, 2008 (Act 778)', 'Ghana Education Service Act, 1995 (Act 506)', 'Children\'s Act, 1998 (Act 560)'], 'B', 'The Education Act, 2008 (Act 778) sets out the structure of education in Ghana, including free compulsory basic education.', 2],
        ['Under the Education Act, 2008 (Act 778), basic education in Ghana lasts', ['6 years', '9 years', '11 years', '12 years'], 'C', 'Basic education is two years of kindergarten, six years of primary and three years of junior high school: 11 years.', 2],
        ['Which body is responsible for developing the pre-tertiary curriculum in Ghana?', ['National Teaching Council (NTC)', 'National Council for Curriculum and Assessment (NaCCA)', 'West African Examinations Council (WAEC)', 'National Schools Inspectorate Authority (NaSIA)'], 'B', 'NaCCA develops the national pre-tertiary curriculum and assessment standards.', 1],
        ['Which body registers and licenses teachers in Ghana?', ['Ghana Education Service', 'National Teaching Council', 'NaCCA', 'Public Services Commission'], 'B', 'The National Teaching Council registers, licenses and promotes the professional development of teachers.', 1],
        ['The National Schools Inspectorate Authority (NaSIA) was formerly known as the', ['National Inspectorate Board', 'Curriculum Research and Development Division', 'Teacher Education Division', 'National Accreditation Board'], 'A', 'NaSIA replaced the National Inspectorate Board, which inspected and set standards for pre-tertiary schools.', 3],
        ['The head of the Ghana Education Service is the', ['Minister for Education', 'Director-General', 'Chief Director', 'Regional Director'], 'B', 'The Director-General is the chief executive of the GES; the Minister heads the Ministry of Education.', 1],
        ['Which article of the 1992 Constitution guarantees the right to equal educational opportunities?', ['Article 21', 'Article 25', 'Article 38', 'Article 42'], 'B', 'Article 25 provides that all persons have the right to equal educational opportunities and facilities.', 3],
        ['The Free Senior High School policy began in Ghana in', ['September 2015', 'September 2017', 'January 2019', 'September 2020'], 'B', 'Free SHS started with the first-year students admitted in September 2017.', 2],

        // Teacher standards and conduct
        ['The National Teachers\' Standards (NTS) for Ghana are organised under three domains. Which of these is one of them?', ['Professional Practice', 'Classroom Discipline', 'School Finance', 'Community Relations'], 'A', 'The three domains are Professional Values and Attitudes, Professional Knowledge, and Professional Practice.', 2],
        ['Which of the following is a serious breach of the teachers\' code of professional conduct?', ['attending in-service training', 'having a sexual relationship with a learner', 'keeping a lesson notes book', 'reporting a sick learner to the head'], 'B', 'Any sexual relationship with a learner is gross misconduct and a criminal matter.', 1],
        ['Which of these is a statutory record that every basic school must keep?', ['class timetable', 'log book', 'sports fixtures', 'PTA minutes'], 'B', 'Statutory records include the admission register, attendance register and log book.', 2],
        ['In the GES rank structure, which rank comes immediately after Principal Superintendent?', ['Senior Superintendent I', 'Assistant Director II', 'Deputy Director', 'Assistant Director I'], 'B', 'Principal Superintendent is followed by Assistant Director II, then Assistant Director I, Deputy Director and the Director ranks.', 2],

        // Curriculum
        ['The standards-based curriculum for kindergarten to Primary 6 was introduced in Ghanaian schools in', ['2007', '2012', '2019', '2023'], 'C', 'NaCCA\'s standards-based curriculum for KG–B6 began in September 2019.', 2],
        ['In the standards-based curriculum, what comes directly under a content standard?', ['strand', 'sub-strand', 'learning indicator', 'core competency'], 'C', 'The order is strand → sub-strand → content standard → learning indicators.', 3],
        ['Critical thinking, creativity, communication and collaboration are described in the Ghanaian curriculum as', ['learning indicators', 'core competencies', 'content standards', 'exemplars'], 'B', 'They are core competencies that cut across all subjects.', 2],
        ['Ghana\'s Inclusive Education Policy was launched in', ['2008', '2015', '2017', '2021'], 'B', 'The Inclusive Education Policy was launched in 2015 to make schools welcoming to all learners.', 3],

        // Educational psychology
        ['According to Piaget, children aged about 7 to 11 years are in the', ['sensorimotor stage', 'pre-operational stage', 'concrete operational stage', 'formal operational stage'], 'C', 'In the concrete operational stage children think logically about concrete objects and events.', 2],
        ['The gap between what a learner can do alone and what they can do with guidance is Vygotsky\'s', ['scaffolding', 'zone of proximal development', 'schema', 'readiness'], 'B', 'The ZPD is where teaching with support (scaffolding) is most effective.', 2],
        ['Praising a learner immediately after a correct answer so that it happens again is an example of', ['classical conditioning', 'positive reinforcement', 'negative punishment', 'extinction'], 'B', 'Adding something pleasant to strengthen a behaviour is positive reinforcement (Skinner\'s operant conditioning).', 2],
        ['Which need is at the base of Maslow\'s hierarchy of needs?', ['safety', 'esteem', 'physiological', 'self-actualisation'], 'C', 'Physiological needs (food, water, rest) must be met first.', 1],
        ['Howard Gardner is best known for the theory of', ['multiple intelligences', 'cognitive development', 'hierarchy of needs', 'social learning'], 'A', 'Gardner proposed that intelligence has several forms, such as linguistic, logical-mathematical and interpersonal.', 1],

        // Assessment and pedagogy
        ['Assessment carried out during teaching to improve learning is called', ['summative assessment', 'formative assessment', 'placement assessment', 'external assessment'], 'B', 'Formative assessment (assessment for learning) gives feedback while learning is still happening.', 1],
        ['In the revised Bloom\'s taxonomy, which is the highest level of thinking?', ['evaluating', 'analysing', 'creating', 'applying'], 'C', 'The revised order is remember, understand, apply, analyse, evaluate, create.', 2],
        ['A test that gives similar results when taken again under the same conditions is said to be', ['valid', 'reliable', 'objective', 'practical'], 'B', 'Reliability is consistency. Validity means the test measures what it is meant to measure.', 2],
        ['The median of the scores 4, 9, 6, 7 and 6 is', ['6', '6.4', '7', '9'], 'A', 'In order: 4, 6, 6, 7, 9. The middle score is 6.', 2],
        ['Grouping learners and adjusting tasks to suit their different abilities is called', ['streaming', 'differentiated instruction', 'rote learning', 'team teaching'], 'B', 'Differentiation adapts content, process or product to learners\' needs.', 2],
    ],
];
