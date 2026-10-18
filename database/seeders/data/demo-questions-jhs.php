<?php

/*
| Demo practice questions in the style of the BECE objective papers.
|
| Written for Otisam; not copied from WAEC papers or other websites.
|
| subject slug => list of [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    'jhs-mathematics' => [
        ['Find the LCM of 4, 6 and 8.', ['12', '24', '48', '2'], 'B', 'Multiples of 8: 8, 16, 24… 24 is the first one also divisible by 4 and 6.', 1],
        ['Simplify 3a + 5b − a + 2b.', ['2a + 7b', '4a + 7b', '2a + 3b', '8ab'], 'A', 'Collect like terms: (3a − a) + (5b + 2b) = 2a + 7b.', 1],
        ['Express 3/5 as a percentage.', ['35%', '53%', '60%', '75%'], 'C', '3/5 × 100% = 60%.', 1],
        ['Find the area of a rectangle 8 cm long and 5 cm wide.', ['13 cm²', '26 cm²', '40 cm²', '80 cm²'], 'C', 'Area = length × width = 8 × 5 = 40 cm².', 1],
        ['If 2x + 3 = 11, find x.', ['3', '4', '7', '8'], 'B', '2x = 11 − 3 = 8, so x = 4.', 1],
        ['Kofi shared GH₵ 60 between Ama and Esi in the ratio 2 : 3. How much did Esi get?', ['GH₵ 24', 'GH₵ 30', 'GH₵ 36', 'GH₵ 40'], 'C', 'Total parts = 5. Esi gets 3/5 × 60 = GH₵ 36.', 2],
    ],

    'jhs-english-language' => [
        ['Choose the correct option: The children ___ playing in the field when it started to rain.', ['was', 'were', 'is', 'be'], 'B', '"Children" is plural, so the past continuous takes "were".', 1],
        ['Choose the word nearest in meaning to the word in capitals: The road to the village is very NARROW.', ['wide', 'thin', 'long', 'short'], 'B', 'Narrow means having little width; "thin" is closest.', 1],
        ['Choose the correct plural of "child".', ['childs', 'childes', 'children', 'childrens'], 'C', '"Child" has the irregular plural "children".', 1],
        ['Choose the correct option: Abena is ___ than her brother.', ['tall', 'taller', 'tallest', 'more tall'], 'B', 'When comparing two people, use the comparative form "taller".', 1],
        ['Choose the word opposite in meaning to the word in capitals: The water in the pot is COLD.', ['cool', 'hot', 'fresh', 'clean'], 'B', 'The opposite of cold is hot.', 1],
        ['Choose the correct option: If I ___ you, I would study harder.', ['am', 'was', 'were', 'be'], 'C', 'In unreal conditions, standard English uses "were" for all persons: "If I were you…".', 2],
    ],

    'jhs-science' => [
        ['Which of the following is a property of all living things?', ['they can fly', 'they reproduce', 'they are green', 'they live in water'], 'B', 'Reproduction is one of the characteristics of all living things.', 1],
        ['Water boils at ___ at sea level.', ['0 °C', '37 °C', '100 °C', '212 °C'], 'C', 'Pure water boils at 100 °C at standard atmospheric pressure.', 1],
        ['The part of a plant that absorbs water from the soil is the', ['leaf', 'stem', 'root', 'flower'], 'C', 'Root hairs absorb water and mineral salts.', 1],
        ['Which of the following is a good conductor of electricity?', ['wood', 'rubber', 'copper', 'plastic'], 'C', 'Copper is a metal with free electrons, so it conducts electricity well.', 1],
        ['The change of state from liquid to gas is called', ['melting', 'evaporation', 'condensation', 'freezing'], 'B', 'Evaporation turns a liquid into a gas (vapour).', 1],
        ['Which food nutrient is mainly needed for body building?', ['carbohydrates', 'fats and oils', 'proteins', 'water'], 'C', 'Proteins build and repair body tissues.', 1],
    ],

    'jhs-social-studies' => [
        ['The capital of Ghana is', ['Kumasi', 'Accra', 'Tamale', 'Cape Coast'], 'B', 'Accra is the capital city of Ghana.', 1],
        ['How many regions does Ghana currently have?', ['10', '12', '16', '18'], 'C', 'Six new regions were created in 2018, bringing the total to 16.', 1],
        ['The first President of Ghana was', ['Dr. Kwame Nkrumah', 'Dr. K. A. Busia', 'Lt. Gen. J. A. Ankrah', 'Dr. Hilla Limann'], 'A', 'Dr. Kwame Nkrumah became Ghana\'s first President when Ghana became a republic in 1960.', 1],
        ['Which of the following is a way of conserving the environment?', ['bush burning', 'planting trees', 'illegal mining', 'dumping refuse in rivers'], 'B', 'Planting trees (afforestation) protects soil and water and restores vegetation.', 1],
        ['A person who has the right to live in a country and enjoy its protection is a', ['tourist', 'citizen', 'refugee', 'visitor'], 'B', 'A citizen is a full legal member of a country.', 1],
        ['The body responsible for conducting elections in Ghana is the', ['Judiciary', 'Electoral Commission', 'Ghana Police Service', 'Parliament'], 'B', 'The Electoral Commission organises and supervises public elections.', 1],
    ],

    'jhs-computing' => [
        ['Which of the following is an output device?', ['mouse', 'scanner', 'printer', 'keyboard'], 'C', 'A printer produces a hard copy of information from the computer.', 1],
        ['The physical parts of a computer that you can touch are called', ['software', 'hardware', 'data', 'programs'], 'B', 'Hardware is the physical equipment; software is the programs.', 1],
        ['Which key is used to delete the character to the left of the cursor?', ['Delete', 'Backspace', 'Shift', 'Enter'], 'B', 'Backspace removes the character before (to the left of) the cursor.', 1],
        ['What does "www" stand for?', ['World Wide Web', 'World Web Wide', 'Wide World Web', 'Web World Wide'], 'A', 'www is short for World Wide Web.', 1],
        ['A strong password should', ['be your name', 'be "123456"', 'mix letters, numbers and symbols', 'be shared with friends'], 'C', 'Mixing letters, numbers and symbols makes a password hard to guess. Never share it.', 1],
        ['The temporary memory that is lost when the computer is switched off is', ['ROM', 'RAM', 'hard disk', 'flash drive'], 'B', 'RAM is volatile: its contents are lost without power.', 2],
    ],

    'jhs-religious-and-moral-education' => [
        ['The holy book of Islam is the', ['Bible', 'Qur\'an', 'Torah', 'Vedas'], 'B', 'Muslims believe the Qur\'an was revealed to Prophet Muhammad (SAW).', 1],
        ['In Christianity, Christmas celebrates the', ['death of Jesus', 'birth of Jesus', 'resurrection of Jesus', 'ascension of Jesus'], 'B', 'Christmas marks the birth of Jesus Christ.', 1],
        ['Which of the following is a moral value?', ['stealing', 'honesty', 'laziness', 'greed'], 'B', 'Honesty (telling the truth) is a moral virtue.', 1],
        ['In African Traditional Religion, the Supreme Being is called "Onyame" by the', ['Ewe', 'Akan', 'Ga', 'Dagomba'], 'B', 'The Akan call the Supreme Being Onyame (or Onyankopon).', 2],
        ['The month of fasting in Islam is', ['Ramadan', 'Muharram', 'Shawwal', 'Hajj'], 'A', 'Muslims fast from dawn to sunset during Ramadan.', 1],
        ['Showing respect to elders is an example of', ['bad behaviour', 'good manners', 'disobedience', 'pride'], 'B', 'Greeting and respecting elders is valued as good manners in Ghanaian society.', 1],
    ],
];
