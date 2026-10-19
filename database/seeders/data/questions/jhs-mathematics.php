<?php

/*
| JHS Mathematics: BECE-style objective questions written for Otisam (not copied from WAEC papers).
| Topics follow the JHS Common Core Programme: number, sets, algebra, geometry and measurement,
| statistics and probability, vectors and transformations.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Number' => [
        ['Write 0.0045 in standard form.', ['4.5 × 10⁻²', '45 × 10⁻⁴', '4.5 × 10⁻³', '4.5 × 10³'], 'C', 'Move the decimal point 3 places to the right to get 4.5, so 0.0045 = 4.5 × 10⁻³.', 2],
        ['Find the HCF of 24 and 36.', ['6', '12', '18', '72'], 'B', '24 = 2³ × 3 and 36 = 2² × 3². The common factors are 2² × 3 = 12.', 1],
        ['Express 72 as a product of its prime factors.', ['2² × 3³', '2 × 3 × 12', '2³ × 9', '2³ × 3²'], 'D', '72 = 2 × 2 × 2 × 3 × 3 = 2³ × 3².', 2],
        ['Convert 23 (base ten) to a base two numeral.', ['11101₂', '10011₂', '10111₂', '11011₂'], 'C', '23 = 16 + 4 + 2 + 1, so the digits for 16, 8, 4, 2, 1 are 1, 0, 1, 1, 1: 10111₂.', 2],
        ['Convert 1101₂ to a base ten numeral.', ['11', '13', '14', '1101'], 'B', '1101₂ = 1×8 + 1×4 + 0×2 + 1×1 = 13.', 2],
        ['Evaluate 2/3 + 3/4.', ['5/7', '5/12', '1 1/12', '1 5/12'], 'D', 'LCM of 3 and 4 is 12: 8/12 + 9/12 = 17/12 = 1 5/12.', 1],
        ['Round 3.14159 to three decimal places.', ['3.141', '3.142', '3.140', '3.14'], 'B', 'The fourth decimal digit is 5, so the third digit (1) rounds up to 2: 3.142.', 1],
        ['What is 15% of GH₵ 240?', ['GH₵ 16', 'GH₵ 24', 'GH₵ 36', 'GH₵ 225'], 'C', '15/100 × 240 = 36.', 1],
        ['Find the next number in the sequence 3, 7, 11, 15, …', ['17', '18', '19', '21'], 'C', 'Each term increases by 4, so the next term is 15 + 4 = 19.', 1],
        ['Simplify 2³ × 2⁴.', ['2⁷', '2¹²', '4⁷', '4¹²'], 'A', 'When multiplying powers of the same base, add the indices: 2³⁺⁴ = 2⁷.', 1],
        ['Evaluate 9^½.', ['3', '4.5', '18', '81'], 'A', 'A power of ½ means the square root: √9 = 3.', 2],
    ],

    'Sets' => [
        ['If A = {1, 2, 3, 4, 5} and B = {2, 4, 6, 8}, find A ∩ B.', ['{1, 3, 5}', '{2, 4}', '{1, 2, 3, 4, 5, 6, 8}', '{6, 8}'], 'B', 'A ∩ B is the set of elements in both A and B: 2 and 4.', 1],
        ['In a class of 40 pupils, 25 like football, 20 like volleyball and 8 like both. How many like neither game?', ['3', '5', '7', '12'], 'A', 'Pupils who like at least one game = 25 + 20 − 8 = 37. Neither = 40 − 37 = 3.', 3],
    ],

    'Commercial arithmetic and ratio' => [
        ['Find the simple interest on GH₵ 500 at 8% per annum for 3 years.', ['GH₵ 40', 'GH₵ 120', 'GH₵ 150', 'GH₵ 620'], 'B', 'I = PRT/100 = 500 × 8 × 3 / 100 = GH₵ 120.', 2],
        ['A trader bought a radio for GH₵ 80 and sold it for GH₵ 100. Find the profit as a percentage of the cost price.', ['20%', '25%', '80%', '125%'], 'B', 'Profit = 100 − 80 = 20. Profit % = 20/80 × 100% = 25%.', 2],
        ['On a map with scale 1 : 50 000, two towns are 4 cm apart. What is the actual distance between them?', ['0.2 km', '2 km', '20 km', '200 km'], 'B', '4 × 50 000 = 200 000 cm = 2 000 m = 2 km.', 3],
        ['A car travels 180 km in 3 hours. What is its average speed?', ['50 km/h', '60 km/h', '90 km/h', '540 km/h'], 'B', 'Speed = distance ÷ time = 180 ÷ 3 = 60 km/h.', 1],
    ],

    'Algebra' => [
        ['Solve the inequality 3x − 5 < 7.', ['x < 4', 'x > 4', 'x < 2/3', 'x < 12'], 'A', '3x < 12, so x < 4.', 2],
        ['If y = 3x − 4, find y when x = 5.', ['1', '11', '15', '19'], 'B', 'y = 3(5) − 4 = 15 − 4 = 11.', 1],
        ['Expand and simplify 2(x + 3) − 3(x − 1).', ['−x + 3', '−x + 9', '5x + 3', '−x + 5'], 'B', '2x + 6 − 3x + 3 = −x + 9.', 2],
        ['Factorise 6x + 9.', ['3(2x + 3)', '6(x + 3)', '3(2x + 9)', '9(6x + 1)'], 'A', 'The HCF of 6x and 9 is 3: 6x + 9 = 3(2x + 3).', 1],
        ['Solve (x + 2)/3 = 5.', ['x = 3', 'x = 17', 'x = 15', 'x = 13'], 'D', 'Multiply both sides by 3: x + 2 = 15, so x = 13.', 2],
        ['Find the gradient of the line through the points (1, 2) and (3, 8).', ['1/3', '2', '3', '6'], 'C', 'Gradient = (8 − 2)/(3 − 1) = 6/2 = 3.', 2],
    ],

    'Geometry and measurement' => [
        ['What is the sum of the interior angles of a pentagon?', ['360°', '480°', '540°', '720°'], 'C', 'Sum = (n − 2) × 180° = 3 × 180° = 540°.', 2],
        ['Two angles of a triangle are 65° and 48°. Find the third angle.', ['57°', '113°', '77°', '67°'], 'D', 'Angles in a triangle add up to 180°: 180 − 65 − 48 = 67°.', 1],
        ['Find the size of each exterior angle of a regular hexagon.', ['45°', '120°', '72°', '60°'], 'D', 'Exterior angles add up to 360°: 360 ÷ 6 = 60°.', 2],
        ['Find the circumference of a circle of radius 7 cm. (Take π = 22/7)', ['44 cm', '22 cm', '88 cm', '154 cm'], 'A', 'C = 2πr = 2 × 22/7 × 7 = 44 cm.', 2],
        ['Find the area of a triangle with base 10 cm and height 6 cm.', ['16 cm²', '120 cm²', '60 cm²', '30 cm²'], 'D', 'Area = ½ × base × height = ½ × 10 × 6 = 30 cm².', 1],
        ['Find the volume of a cuboid 5 cm long, 4 cm wide and 3 cm high.', ['12 cm³', '47 cm³', '60 cm³', '94 cm³'], 'C', 'V = l × b × h = 5 × 4 × 3 = 60 cm³.', 1],
        ['The two shorter sides of a right-angled triangle are 6 cm and 8 cm. Find the longest side.', ['10 cm', '12 cm', '14 cm', '48 cm'], 'A', 'By Pythagoras, c² = 6² + 8² = 36 + 64 = 100, so c = 10 cm.', 2],
        ['The bearing of B from A is 060°. What is the bearing of A from B?', ['060°', '120°', '240°', '300°'], 'C', 'A back bearing differs by 180°: 060° + 180° = 240°.', 3],
    ],

    'Vectors and transformations' => [
        ['If a = (2, 3) and b = (4, −1) are column vectors, find a + b.', ['(6, 2)', '(6, 4)', '(−2, 4)', '(8, −3)'], 'A', 'Add the corresponding components: (2 + 4, 3 + (−1)) = (6, 2).', 2],
        ['The point P(2, −3) is translated by the vector (3, 4). Find the image of P.', ['(−1, −7)', '(6, −12)', '(5, −7)', '(5, 1)'], 'D', 'Add the vector: (2 + 3, −3 + 4) = (5, 1).', 2],
        ['Find the image of the point (3, 5) after a reflection in the x-axis.', ['(−3, 5)', '(5, 3)', '(−3, −5)', '(3, −5)'], 'D', 'Reflection in the x-axis keeps x and changes the sign of y: (3, −5).', 2],
    ],

    'Statistics and probability' => [
        ['Find the mean of 4, 7, 9, 10 and 15.', ['9', '7', '10', '45'], 'A', 'Sum = 45 and there are 5 numbers, so the mean = 45 ÷ 5 = 9.', 1],
        ['Find the median of 3, 8, 2, 9, 5 and 7.', ['5', '7', '6', '5.5'], 'C', 'In order: 2, 3, 5, 7, 8, 9. The median is the mean of the middle two: (5 + 7) ÷ 2 = 6.', 2],
        ['Find the mode of 2, 3, 3, 4, 5, 5, 5, 6.', ['3', '4', '5', '6'], 'C', '5 appears most often (three times).', 1],
        ['A bag contains 3 red balls and 5 blue balls. A ball is picked at random. What is the probability that it is red?', ['3/5', '1/3', '5/8', '3/8'], 'D', 'There are 8 balls and 3 are red: P(red) = 3/8.', 1],
    ],
];
