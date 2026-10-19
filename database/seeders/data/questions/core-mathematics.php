<?php

/*
| SHS Core Mathematics: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers. Topics follow the WASSCE Core Mathematics syllabus: number and numeration, algebra,
| mensuration, plane geometry, coordinate geometry, trigonometry, statistics and probability.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Number and numeration' => [
        ['Convert 214₅ to a number in base ten.', ['44', '59', '64', '214'], 'B', '214₅ = 2 × 25 + 1 × 5 + 4 = 50 + 5 + 4 = 59.', 2],
        ['Simplify (2√3)².', ['6', '12', '4√3', '36'], 'B', '(2√3)² = 4 × 3 = 12.', 2],
        ['Rationalise 6/√3.', ['2√3', '3√2', '√3', '6√3'], 'A', '6/√3 × √3/√3 = 6√3/3 = 2√3.', 2],
        ['Evaluate log₁₀ 1000 − log₁₀ 10.', ['1', '2', '3', '990'], 'B', 'log₁₀ 1000 = 3 and log₁₀ 10 = 1, so the result is 3 − 1 = 2.', 2],
        ['Evaluate 27^(−2/3).', ['−9', '1/9', '9', '−1/9'], 'B', '27^(1/3) = 3, so 27^(2/3) = 9 and 27^(−2/3) = 1/9.', 3],
        ['If 2^(x + 1) = 16, find x.', ['2', '3', '4', '8'], 'B', '16 = 2⁴, so x + 1 = 4 and x = 3.', 2],
        ['Find the value of 17 (mod 5).', ['1', '2', '3', '4'], 'B', '17 = 3 × 5 + 2, so 17 ≡ 2 (mod 5).', 1],
        ['Express the recurring decimal 0.333… as a fraction.', ['1/3', '3/10', '33/100', '1/9'], 'A', 'Let x = 0.333…; 10x = 3.333…; 9x = 3, so x = 1/3.', 2],
        ['Find the amount when GH₵ 1,000 is invested for 2 years at 10% compound interest per annum.', ['GH₵ 1,100', 'GH₵ 1,200', 'GH₵ 1,210', 'GH₵ 1,331'], 'C', 'A = 1000 × (1.1)² = 1000 × 1.21 = GH₵ 1,210.', 3],
        ['In a class, n(A) = 12, n(B) = 9 and n(A ∩ B) = 4. Find n(A ∪ B).', ['13', '17', '21', '25'], 'B', 'n(A ∪ B) = n(A) + n(B) − n(A ∩ B) = 12 + 9 − 4 = 17.', 2],
    ],

    'Algebra' => [
        ['Solve x² − 5x + 6 = 0.', ['x = −2 or −3', 'x = 2 or 3', 'x = 1 or 6', 'x = −1 or 6'], 'B', 'x² − 5x + 6 = (x − 2)(x − 3) = 0, so x = 2 or x = 3.', 2],
        ['Simplify (x² − 4)/(x + 2).', ['x + 2', 'x − 2', 'x − 4', 'x² − 2'], 'B', 'x² − 4 = (x − 2)(x + 2), so dividing by (x + 2) leaves x − 2.', 2],
        ['Solve the simultaneous equations x + y = 7 and x − y = 1.', ['x = 3, y = 4', 'x = 6, y = 1', 'x = 5, y = 2', 'x = 4, y = 3'], 'D', 'Adding gives 2x = 8, so x = 4 and y = 3.', 2],
        ['Solve the inequality 2(x − 3) ≥ x + 1.', ['x ≥ 4', 'x ≤ 7', 'x ≥ 7', 'x ≥ 5'], 'C', '2x − 6 ≥ x + 1, so x ≥ 7.', 2],
        ['Make r the subject of the formula V = πr²h.', ['r = V/(πh)', 'r = πh/V', 'r = √(Vπh)', 'r = √(V/(πh))'], 'D', 'r² = V/(πh), so r = √(V/(πh)).', 3],
        ['y varies directly as x, and y = 12 when x = 3. Find y when x = 5.', ['20', '15', '24', '36'], 'A', 'y = kx with k = 12/3 = 4, so y = 4 × 5 = 20.', 2],
        ['y varies inversely as x, and y = 4 when x = 6. Find y when x = 8.', ['3', '5⅓', '6', '32'], 'A', 'y = k/x with k = 24, so y = 24/8 = 3.', 3],
        ['Find the 20th term of the arithmetic progression 3, 7, 11, …', ['76', '80', '79', '83'], 'C', 'Tₙ = a + (n − 1)d = 3 + 19 × 4 = 79.', 3],
        ['Find the sum of the first 10 terms of the AP 2, 5, 8, …', ['145', '290', '165', '155'], 'D', 'Sₙ = n/2 [2a + (n − 1)d] = 5 × (4 + 27) = 155.', 3],
        ['Find the 5th term of the geometric progression 2, 6, 18, …', ['54', '108', '162', '486'], 'C', 'Tₙ = arⁿ⁻¹ = 2 × 3⁴ = 2 × 81 = 162.', 3],
    ],

    'Mensuration' => [
        ['Find the area of a sector of a circle of radius 7 cm that subtends an angle of 90° at the centre. (Take π = 22/7)', ['11 cm²', '154 cm²', '77 cm²', '38.5 cm²'], 'D', 'Area = (θ/360) × πr² = ¼ × 22/7 × 49 = 38.5 cm².', 3],
        ['Find the volume of a cylinder of radius 7 cm and height 10 cm. (Take π = 22/7)', ['1,540 cm³', '440 cm³', '3,080 cm³', '15,400 cm³'], 'A', 'V = πr²h = 22/7 × 49 × 10 = 1,540 cm³.', 2],
        ['Find the total surface area of a cube of side 4 cm.', ['16 cm²', '64 cm²', '96 cm²', '128 cm²'], 'C', 'A cube has 6 faces each 4 × 4 = 16 cm², so 6 × 16 = 96 cm².', 2],
    ],

    'Plane geometry' => [
        ['An arc subtends an angle of 100° at the centre of a circle. What angle does it subtend at the circumference?', ['50°', '100°', '130°', '200°'], 'A', 'The angle at the centre is twice the angle at the circumference: 100° ÷ 2 = 50°.', 2],
        ['What is the size of an angle in a semicircle?', ['45°', '60°', '90°', '180°'], 'C', 'The angle subtended by a diameter at the circumference is a right angle.', 1],
        ['One angle of a cyclic quadrilateral is 70°. Find the angle opposite it.', ['20°', '70°', '110°', '290°'], 'C', 'Opposite angles of a cyclic quadrilateral add up to 180°: 180 − 70 = 110°.', 2],
    ],

    'Coordinate geometry' => [
        ['Find the distance between the points (1, 2) and (4, 6).', ['3', '4', '5', '7'], 'C', 'd = √[(4 − 1)² + (6 − 2)²] = √(9 + 16) = √25 = 5.', 2],
        ['Find the midpoint of the line joining (2, 4) and (6, 10).', ['(4, 7)', '(8, 14)', '(2, 3)', '(3, 7)'], 'A', 'Midpoint = ((2 + 6)/2, (4 + 10)/2) = (4, 7).', 1],
        ['Find the equation of the line with gradient 2 that passes through (0, −3).', ['y = 2x + 3', 'y = −3x + 2', 'y = 2x − 3', 'y = 3x − 2'], 'C', 'y = mx + c with m = 2 and c = −3 (the y-intercept).', 2],
    ],

    'Trigonometry' => [
        ['If sin θ = 3/5 and θ is acute, find cos θ.', ['3/4', '5/3', '5/4', '4/5'], 'D', 'Using a 3-4-5 triangle: opposite 3, hypotenuse 5, adjacent 4, so cos θ = 4/5.', 2],
        ['Find the value of tan 45°.', ['0', '1/√3', '√3', '1'], 'D', 'In a right isosceles triangle the opposite and adjacent sides are equal, so tan 45° = 1.', 1],
        ['A ladder 10 m long leans against a wall and makes an angle of 60° with the ground. How high up the wall does it reach?', ['5 m', '20 m', '10√3 m', '5√3 m'], 'D', 'Height = 10 sin 60° = 10 × √3/2 = 5√3 m.', 3],
    ],

    'Statistics and probability' => [
        ['Find the mean of the distribution: x = 1, 2, 3 with frequencies 2, 3, 5.', ['2.3', '2.0', '2.5', '3.0'], 'A', 'Σfx = 2 + 6 + 15 = 23 and Σf = 10, so the mean = 23/10 = 2.3.', 2],
        ['Find the range of 12, 7, 15, 3 and 9.', ['9', '18', '15', '12'], 'D', 'Range = highest − lowest = 15 − 3 = 12.', 1],
        ['Two fair coins are tossed together. What is the probability of getting two heads?', ['1/4', '1/3', '1/2', '3/4'], 'A', 'Outcomes: HH, HT, TH, TT. Only HH is two heads: 1/4.', 2],
        ['A fair die is thrown once. What is the probability of getting a number greater than 4?', ['1/6', '2/3', '1/2', '1/3'], 'D', 'Numbers greater than 4 are 5 and 6: 2/6 = 1/3.', 1],
    ],

    'SHS 1: Number sets and properties' => [
        ['Which of these numbers is irrational?', ['0.25', '√16', '√7', '−3/4'], 'C', '√7 cannot be written as a fraction of two integers, and its decimal never ends or repeats. √16 = 4 is rational.', 2],
        ['The recurring decimal 0.333… is best described as', ['an irrational number', 'a rational number', 'a natural number', 'not a real number'], 'B', 'A recurring decimal can be written as a fraction: 0.333… = 1/3, so it is rational.', 2],
        ['The statement (4 + 9) + 6 = 4 + (9 + 6) shows the', ['commutative property', 'distributive property', 'associative property', 'identity property'], 'C', 'Changing how the numbers are grouped does not change the sum: the associative property of addition.', 1],
        ['Using the distributive property, 8 × 43 = 8 × 40 + 8 × 3 =', ['344', '324', '384', '51'], 'A', '8 × 40 = 320 and 8 × 3 = 24, so the product is 320 + 24 = 344.', 1],
        ['Write 0.85 as a fraction in its lowest terms.', ['85/10', '17/20', '17/25', '8/5'], 'B', '0.85 = 85/100. Dividing top and bottom by 5 gives 17/20.', 1],
    ],

    'SHS 1: Fractions, decimals and percentages' => [
        ['A choir has 48 members and 5/8 of them are female. How many members are male?', ['30', '18', '24', '13'], 'B', 'Female = 5/8 × 48 = 30, so male = 48 − 30 = 18.', 2],
        ['Express 7/8 as a percentage.', ['87.5%', '78%', '70%', '0.875%'], 'A', '7/8 × 100% = 700/8 % = 87.5%.', 1],
        ['A phone priced GH¢1,200 is reduced by 15%. What is the sale price?', ['GH¢180', 'GH¢1,185', 'GH¢1,020', 'GH¢1,380'], 'C', 'Discount = 15% of 1,200 = GH¢180. Sale price = 1,200 − 180 = GH¢1,020.', 2],
        ['Write 0.36 as a fraction in its lowest terms.', ['36/10', '18/25', '3/6', '9/25'], 'D', '0.36 = 36/100. Dividing top and bottom by 4 gives 9/25.', 1],
    ],

    'SHS 1: Algebraic expressions and factorisation' => [
        ['Expand (x + 6)(x − 2).', ['x² + 4x − 12', 'x² − 4x − 12', 'x² + 8x − 12', 'x² + 4x + 12'], 'A', 'x × x + x × (−2) + 6 × x + 6 × (−2) = x² − 2x + 6x − 12 = x² + 4x − 12.', 2],
        ['Factorise x² − 7x + 12.', ['(x + 3)(x + 4)', '(x − 2)(x − 6)', '(x − 3)(x − 4)', '(x − 12)(x + 1)'], 'C', 'Find two numbers that multiply to 12 and add to −7: −3 and −4.', 2],
        ['Factorise 2x² − 18 completely.', ['2(x − 3)(x + 3)', '2(x − 9)(x + 9)', '(2x − 3)(x + 6)', '2(x² − 9)'], 'A', 'Take out 2 to get 2(x² − 9), then factorise the difference of two squares: 2(x − 3)(x + 3).', 3],
        ['What is the nth term of the sequence 4, 7, 10, 13, …?', ['n + 3', '4n − 1', '3n + 4', '3n + 1'], 'D', 'The terms go up by 3, so the rule starts 3n. When n = 1, 3n = 3, and the term is 4, so add 1: 3n + 1.', 2],
        ['Simplify 2/x + 3/(2x).', ['5/(3x)', '7/(2x)', '5/(2x)', '6/(2x²)'], 'B', 'Use the common denominator 2x: 4/(2x) + 3/(2x) = 7/(2x).', 3],
    ],

    'SHS 1: Linear equations, relations and functions' => [
        ['Solve 5(y − 2) = 3y + 4.', ['3', '7', '−3', '14'], 'B', '5y − 10 = 3y + 4, so 2y = 14 and y = 7.', 2],
        ['Solve the inequality 3x − 5 > 7.', ['x > 4', 'x < 4', 'x > 2/3', 'x ≥ 4'], 'A', 'Add 5 to both sides: 3x > 12. Divide by 3: x > 4.', 2],
        ['Three consecutive odd numbers add up to 57. What is the largest of them?', ['19', '23', '21', '17'], 'C', 'Let them be n, n + 2 and n + 4: 3n + 6 = 57, so n = 17. The numbers are 17, 19 and 21.', 3],
        ['Which of these relations is NOT a function?', ['y = 2x + 3', 'y = x²', 'y² = x', 'y = 5'], 'C', 'In y² = x, an input such as x = 4 gives two outputs, y = 2 and y = −2. A function gives exactly one output for each input.', 3],
        ['If f(x) = 4x − 3, find f(−2).', ['−5', '5', '11', '−11'], 'D', 'f(−2) = 4 × (−2) − 3 = −8 − 3 = −11.', 1],
        ['What is the gradient of the line y = −3x + 7?', ['7', '−3', '3', '−7'], 'B', 'In y = mx + c the gradient is m, so it is −3. The 7 is the y-intercept.', 1],
    ],

    'SHS 1: Angles, polygons and Pythagoras' => [
        ['An angle is 20° more than its complement. Find the angle.', ['55°', '35°', '70°', '110°'], 'A', 'Let the angle be x. Its complement is x − 20, and x + (x − 20) = 90, so x = 55°.', 3],
        ['Each interior angle of a regular polygon is 150°. How many sides does it have?', ['10', '12', '15', '6'], 'B', 'Each exterior angle is 180° − 150° = 30°. Exterior angles add up to 360°, so there are 360 ÷ 30 = 12 sides.', 3],
        ['What is the sum of the interior angles of a hexagon?', ['540°', '1,080°', '720°', '360°'], 'C', 'Sum = (n − 2) × 180° = 4 × 180° = 720°.', 2],
        ['A ladder 13 m long reaches 12 m up a wall. How far is its foot from the wall?', ['1 m', '25 m', '17.7 m', '5 m'], 'D', 'By Pythagoras, distance² = 13² − 12² = 169 − 144 = 25, so the distance is 5 m.', 2],
        ['When a transversal cuts two parallel lines, alternate angles are', ['equal', 'supplementary', 'complementary', 'always 90°'], 'A', 'Alternate (Z) angles between parallel lines are equal. Co-interior angles add up to 180°.', 1],
    ],

    'SHS 1: Vectors and trigonometric ratios' => [
        ['Find the magnitude of the vector (6, −8).', ['2', '14', '10', '100'], 'C', '|v| = √(6² + (−8)²) = √(36 + 64) = √100 = 10.', 2],
        ['A vector starts at (1, 2) and ends at (4, 6). How long is it?', ['5', '7', '25', '√13'], 'A', 'The vector is (4 − 1, 6 − 2) = (3, 4), and its length is √(9 + 16) = 5.', 2],
        ['In a right-angled triangle, the side opposite θ is 3 and the hypotenuse is 5. What is cos θ?', ['3/5', '4/5', '3/4', '5/4'], 'B', 'The adjacent side is √(25 − 9) = 4, so cos θ = adjacent ÷ hypotenuse = 4/5.', 2],
        ['What is the exact value of sin 30°?', ['√3/2', '1', '1/2', '√2/2'], 'C', 'sin 30° = 1/2. (cos 30° = √3/2 and sin 45° = √2/2.)', 1],
        ['From a point 50 m from the foot of a tower on level ground, the angle of elevation of the top is 45°. How tall is the tower?', ['25 m', '50√2 m', '100 m', '50 m'], 'D', 'tan 45° = height ÷ 50 and tan 45° = 1, so the height is 50 m.', 2],
    ],

    'SHS 1: Perimeter, area and volume' => [
        ['Find the area of a trapezium with parallel sides of 8 cm and 12 cm and a height of 5 cm.', ['50 cm²', '100 cm²', '60 cm²', '25 cm²'], 'A', 'Area = ½ × (8 + 12) × 5 = ½ × 20 × 5 = 50 cm².', 2],
        ['A rhombus has diagonals of 16 m and 10 m. What is its area?', ['160 m²', '80 m²', '26 m²', '40 m²'], 'B', 'Area of a rhombus = ½ × d₁ × d₂ = ½ × 16 × 10 = 80 m².', 2],
        ['A prism has a volume of 480 cm³ and a height of 12 cm. What is its base area?', ['5,760 cm²', '468 cm²', '40 cm²', '20 cm²'], 'C', 'Volume = base area × height, so base area = 480 ÷ 12 = 40 cm².', 2],
        ['A parallelogram has a base of 9 m, a perpendicular height of 4 m and a slanting side of 5 m. What is its area?', ['45 m²', '20 m²', '18 m²', '36 m²'], 'D', 'Area = base × perpendicular height = 9 × 4 = 36 m². The slanting side is not used.', 2],
    ],

    'SHS 1: Collecting and presenting data' => [
        ['Which of these is primary data?', ['answers from a questionnaire you gave out yourself', 'figures from a government report', 'a table in a newspaper', 'a graph in a textbook'], 'A', 'Primary data is collected first-hand by the researcher. The others are secondary data collected by someone else.', 1],
        ['What is the class width of the class 20–29 (whole-number data)?', ['9', '10', '29', '20'], 'B', 'The class boundaries are 19.5 and 29.5, so the width is 29.5 − 19.5 = 10.', 2],
        ['Which chart best shows how a student\'s height changed over several years?', ['a pie chart', 'a Venn diagram', 'a line graph', 'a pictogram'], 'C', 'A line graph shows how a quantity changes over time.', 1],
    ],

    'SHS 1: Probability of independent events' => [
        ['A fair coin and a fair die are tossed together. How many outcomes are in the sample space?', ['8', '36', '2', '12'], 'D', '2 outcomes for the coin × 6 for the die = 12 outcomes.', 2],
        ['The probability of rain on any day is 0.3, independently of other days. What is the probability of no rain on two days running?', ['0.49', '0.09', '0.7', '0.51'], 'A', 'P(no rain) = 1 − 0.3 = 0.7 each day, so P(no rain on both days) = 0.7 × 0.7 = 0.49.', 3],
        ['Two fair dice are thrown. What is the probability that the total is 11?', ['1/36', '1/18', '1/6', '11/36'], 'B', 'A total of 11 comes from (5, 6) or (6, 5): 2 of the 36 outcomes, so 2/36 = 1/18.', 3],
        ['Which pair of events is independent?', ['drawing two cards without replacement', 'wearing a raincoat and it raining', 'getting a head on a coin and a 6 on a die', 'scoring a goal and winning the match'], 'C', 'The coin result has no effect on the die. In the other pairs, one event changes the chance of the other.', 2],
    ],

    'SHS 2: Real numbers, surds and number bases' => [
        ['Convert 1101 (base two) to base ten.', ['11', '13', '15', '26'], 'B', '1101₂ = 8 + 4 + 0 + 1 = 13.', 2],
        ['Convert 25 (base ten) to base five.', ['100₅', '50₅', '45₅', '110₅'], 'A', '25 = 1 × 25 + 0 × 5 + 0 × 1, so 25 = 100₅.', 2],
        ['Simplify √50 − √18.', ['√32', '4√2', '2√2', '8√2'], 'C', '√50 = 5√2 and √18 = 3√2, so the difference is 2√2.', 3],
        ['Rationalise the denominator of 6/√3.', ['6√3', '√3/2', '3√2', '2√3'], 'D', 'Multiply top and bottom by √3: 6√3/3 = 2√3.', 3],
        ['Write 0.000 47 in standard form.', ['4.7 × 10⁻⁴', '4.7 × 10⁻³', '47 × 10⁻⁵', '4.7 × 10⁴'], 'A', 'Move the decimal point 4 places right to get 4.7, so the power is −4.', 2],
    ],

    'SHS 2: Equations and inequalities' => [
        ['Solve the simultaneous equations x + y = 10 and x − y = 4.', ['x = 3, y = 7', 'x = 7, y = 3', 'x = 6, y = 4', 'x = 14, y = −4'], 'B', 'Adding the equations gives 2x = 14, so x = 7, and then y = 10 − 7 = 3.', 2],
        ['Solve x² − 7x + 10 = 0.', ['x = −2 or −5', 'x = 1 or 10', 'x = 2 or 5', 'x = −1 or 10'], 'C', 'x² − 7x + 10 = (x − 2)(x − 5) = 0, so x = 2 or x = 5.', 2],
        ['Solve −2x + 3 ≤ 11.', ['x ≤ −4', 'x ≥ 4', 'x ≤ 4', 'x ≥ −4'], 'D', '−2x ≤ 8. Dividing by a negative number reverses the sign: x ≥ −4.', 3],
        ['A taxi charges GH¢5 plus GH¢3 per kilometre. A trip cost GH¢41. How long was it?', ['12 km', '13.7 km', '15 km', '8 km'], 'A', '5 + 3k = 41, so 3k = 36 and k = 12 km.', 2],
    ],

    'SHS 2: Rigid motion (transformations)' => [
        ['What is the image of the point (3, −5) after a reflection in the x-axis?', ['(−3, −5)', '(3, 5)', '(−3, 5)', '(5, 3)'], 'B', 'A reflection in the x-axis keeps x and changes the sign of y: (3, 5).', 1],
        ['The point (2, 7) is translated by the vector (−4, 3). What is its image?', ['(6, 4)', '(−2, 4)', '(−2, 10)', '(6, 10)'], 'C', 'Add the vector: (2 − 4, 7 + 3) = (−2, 10).', 1],
        ['The point (4, 1) is rotated 90° anticlockwise about the origin. What is its image?', ['(1, −4)', '(−4, −1)', '(4, −1)', '(−1, 4)'], 'D', 'A 90° anticlockwise rotation about the origin maps (x, y) to (−y, x), giving (−1, 4).', 3],
        ['Which transformation does NOT keep the size of a shape the same?', ['enlargement', 'reflection', 'rotation', 'translation'], 'A', 'Reflections, rotations and translations are rigid motions. An enlargement changes the size unless the scale factor is ±1.', 1],
        ['A triangle of area 5 cm² is enlarged by a scale factor of 3. What is the area of its image?', ['15 cm²', '45 cm²', '8 cm²', '125 cm²'], 'B', 'Areas are multiplied by the square of the scale factor: 5 × 3² = 45 cm².', 3],
    ],

    'SHS 2: Ratios, rates and proportions' => [
        ['6 workers take 10 days to build a wall. How long would 4 workers take, working at the same rate?', ['6⅔ days', '20 days', '15 days', '12 days'], 'C', 'The job takes 6 × 10 = 60 worker-days, so 4 workers need 60 ÷ 4 = 15 days.', 2],
        ['If GH¢15 = US$1, how many US dollars do you get for GH¢1,200?', ['80', '18,000', '120', '8'], 'A', '1,200 ÷ 15 = 80 dollars.', 1],
        ['A map has a scale of 1 : 50,000. What real distance does 4 cm on the map represent?', ['20 km', '0.2 km', '200 km', '2 km'], 'D', '4 × 50,000 = 200,000 cm = 2,000 m = 2 km.', 2],
        ['GH¢900 is shared in the ratio 2 : 3 : 4. What is the largest share?', ['GH¢300', 'GH¢400', 'GH¢200', 'GH¢450'], 'B', 'There are 9 parts, so one part is GH¢100 and the largest share is 4 × 100 = GH¢400.', 2],
        ['A car uses 8 litres of fuel for every 100 km. How much fuel does it need for 350 km?', ['28 litres', '43.75 litres', '35 litres', '24 litres'], 'A', '350 ÷ 100 × 8 = 3.5 × 8 = 28 litres.', 2],
    ],

    'SHS 2: Sequences and series' => [
        ['What is the 10th term of the arithmetic sequence 3, 7, 11, …?', ['43', '39', '40', '35'], 'B', 'Tₙ = a + (n − 1)d = 3 + 9 × 4 = 39.', 2],
        ['Find the sum of the first 20 terms of the arithmetic sequence 2, 5, 8, …', ['590', '630', '610', '1,220'], 'C', 'Sₙ = n/2 × [2a + (n − 1)d] = 10 × (4 + 19 × 3) = 10 × 61 = 610.', 3],
        ['What is the common ratio of the geometric sequence 81, 27, 9, …?', ['3', '1/3', '−3', '−1/3'], 'B', 'Each term is the previous one divided by 3: r = 27 ÷ 81 = 1/3.', 2],
        ['What is the 6th term of the geometric sequence 2, 6, 18, …?', ['162', '1,458', '36', '486'], 'D', 'Tₙ = arⁿ⁻¹ = 2 × 3⁵ = 2 × 243 = 486.', 3],
        ['Find the sum to infinity of the geometric series 12 + 6 + 3 + …', ['24', '18', '21', 'it has no sum'], 'A', 'r = 1/2, which is between −1 and 1, so S∞ = a ÷ (1 − r) = 12 ÷ 0.5 = 24.', 3],
    ],

    'SHS 2: Surface areas and volumes' => [
        ['Find the volume of a cylinder with a radius of 7 cm and a height of 10 cm. (Take π = 22/7.)', ['440 cm³', '1,540 cm³', '3,080 cm³', '154 cm³'], 'B', 'V = πr²h = 22/7 × 49 × 10 = 1,540 cm³.', 2],
        ['What is the total surface area of a cube with sides of 5 cm?', ['125 cm²', '100 cm²', '150 cm²', '25 cm²'], 'C', 'A cube has 6 square faces: 6 × 5² = 150 cm².', 1],
        ['Find the volume of a cone with a radius of 3 cm and a height of 7 cm. (Take π = 22/7.)', ['198 cm³', '22 cm³', '132 cm³', '66 cm³'], 'D', 'V = ⅓πr²h = ⅓ × 22/7 × 9 × 7 = 66 cm³.', 3],
        ['Find the curved surface area of a cylinder with a radius of 3.5 cm and a height of 10 cm. (Take π = 22/7.)', ['220 cm²', '110 cm²', '385 cm²', '297 cm²'], 'A', 'Curved area = 2πrh = 2 × 22/7 × 3.5 × 10 = 220 cm².', 3],
        ['What is the volume of a sphere of radius 3 cm, in terms of π?', ['12π cm³', '36π cm³', '108π cm³', '27π cm³'], 'B', 'V = 4/3 πr³ = 4/3 × π × 27 = 36π cm³.', 2],
    ],

    'SHS 2: Statistics and probability experiments' => [
        ['Find the mean of 3, 7, 7, 9 and 14.', ['7', '8', '9', '40'], 'B', 'The total is 40 and there are 5 numbers, so the mean is 40 ÷ 5 = 8.', 1],
        ['Find the median of 12, 5, 9, 15, 7 and 10.', ['9', '10', '9.5', '11'], 'C', 'In order: 5, 7, 9, 10, 12, 15. With six values, the median is the mean of the middle two: (9 + 10) ÷ 2 = 9.5.', 2],
        ['What is the mode of 2, 4, 4, 5, 5, 5, 6?', ['4', '6', '4.43', '5'], 'D', 'The mode is the most frequent value: 5 appears three times.', 1],
        ['A die is thrown 60 times and shows a 6 on 13 of them. What is the relative frequency of a 6?', ['13/60', '1/6', '13/6', '6/13'], 'A', 'Relative frequency = number of times it happened ÷ number of trials = 13/60. The theoretical probability is 1/6.', 2],
        ['What is the range of 18, 25, 11, 30 and 22?', ['30', '19', '11', '7'], 'B', 'Range = largest − smallest = 30 − 11 = 19.', 1],
        ['In a pie chart of 180 pupils, what angle does a group of 45 pupils get?', ['45°', '60°', '90°', '25°'], 'C', 'Angle = 45/180 × 360° = 90°.', 2],
    ],

    'SHS 2: Bearings, vectors and trigonometry' => [
        ['The bearing of B from A is 060°. What is the bearing of A from B?', ['060°', '120°', '300°', '240°'], 'D', 'A back bearing differs by 180°: 060° + 180° = 240°.', 2],
        ['If a = (2, 3) and b = (−1, 4), find a + 2b.', ['(1, 7)', '(0, 11)', '(3, −1)', '(0, 7)'], 'B', '2b = (−2, 8), so a + 2b = (2 − 2, 3 + 8) = (0, 11).', 2],
        ['For any angle θ, sin²θ + cos²θ equals', ['1', '0', '2', 'tan θ'], 'A', 'This identity comes from Pythagoras in a right-angled triangle with hypotenuse 1.', 2],
    ],

    'SHS 3: Logical reasoning and variation' => [
        ['y varies directly as x, and y = 12 when x = 4. Find y when x = 7.', ['21', '28', '16', '3'], 'A', 'y = kx with k = 12 ÷ 4 = 3, so y = 3 × 7 = 21.', 2],
        ['y varies inversely as x, and y = 6 when x = 4. Find y when x = 3.', ['4.5', '8', '2', '18'], 'B', 'y = k/x with k = 6 × 4 = 24, so y = 24 ÷ 3 = 8.', 2],
        ['p varies directly as the square of q, and p = 18 when q = 3. Find p when q = 5.', ['30', '25', '50', '10'], 'C', 'p = kq² with k = 18 ÷ 9 = 2, so p = 2 × 25 = 50.', 3],
        ['x varies jointly as y and z. x = 24 when y = 2 and z = 3. Find x when y = 5 and z = 4.', ['80', '60', '40', '120'], 'A', 'x = kyz with k = 24 ÷ 6 = 4, so x = 4 × 5 × 4 = 80.', 3],
        ['What is the negation of the statement "All the students passed"?', ['No student passed', 'Some students passed', 'All the students failed', 'Not all the students passed'], 'D', 'The negation is true exactly when the statement is false: at least one student did not pass.', 3],
        ['If p is true and q is false, the statement "p and q" (p ∧ q) is', ['false', 'true', 'both true and false', 'impossible to decide'], 'A', 'A conjunction is true only when both parts are true.', 2],
        ['What is the contrapositive of "If it rains, the ground is wet"?', ['If the ground is wet, it rained', 'If the ground is not wet, it did not rain', 'If it does not rain, the ground is not wet', 'It rains and the ground is not wet'], 'B', 'The contrapositive of "if p then q" is "if not q then not p", and it is logically equivalent to the original.', 3],
    ],

    'SHS 3: Algebraic reasoning' => [
        ['Solve 2x² + x − 6 = 0.', ['x = −3/2 or 2', 'x = 3 or −2', 'x = 3/2 or −2', 'x = 2 or 3'], 'C', '2x² + x − 6 = (2x − 3)(x + 2) = 0, so x = 3/2 or x = −2.', 3],
        ['What is the sum of the roots of x² − 9x + 14 = 0?', ['14', '9', '−9', '7'], 'B', 'For x² + bx + c = 0 the sum of the roots is −b = 9. (The roots are 2 and 7.)', 3],
        ['The discriminant of x² + 4x + 7 = 0 shows that the equation has', ['two equal real roots', 'two different real roots', 'no real roots', 'one root equal to zero'], 'C', 'b² − 4ac = 16 − 28 = −12, which is negative, so there are no real roots.', 3],
        ['If f(x) = x² + 1 and g(x) = 2x, find f(g(3)).', ['19', '10', '49', '37'], 'D', 'g(3) = 6, and f(6) = 36 + 1 = 37.', 3],
        ['Find the inverse of f(x) = 3x − 2.', ['(x + 2)/3', '(x − 2)/3', '3x + 2', '1/(3x − 2)'], 'A', 'Write y = 3x − 2 and make x the subject: x = (y + 2)/3. So f⁻¹(x) = (x + 2)/3.', 3],
        ['Evaluate log₂ 32.', ['16', '5', '6', '2.5'], 'B', '2⁵ = 32, so log₂ 32 = 5.', 2],
    ],

    'SHS 3: Circle theorems and constructions' => [
        ['The angle in a semicircle is', ['45°', '90°', '180°', '60°'], 'B', 'An angle subtended by a diameter at the circumference is always a right angle.', 1],
        ['An arc subtends an angle of 140° at the centre of a circle. What angle does it subtend at the circumference?', ['280°', '140°', '70°', '40°'], 'C', 'The angle at the centre is twice the angle at the circumference, so 140° ÷ 2 = 70°.', 2],
        ['The opposite angles of a cyclic quadrilateral add up to', ['90°', '360°', '270°', '180°'], 'D', 'Opposite angles of a quadrilateral inside a circle are supplementary.', 2],
        ['A tangent to a circle meets the radius at the point of contact at', ['90°', '45°', '60°', '180°'], 'A', 'A tangent is always perpendicular to the radius drawn to the point of contact.', 1],
        ['Angles in the same segment of a circle are', ['supplementary', 'complementary', 'equal', 'always 90°'], 'C', 'Angles subtended by the same arc at the circumference, on the same side, are equal.', 2],
        ['To construct an angle of 60° with a ruler and compasses, you construct', ['a square', 'an equilateral triangle', 'a perpendicular bisector', 'a right angle and then halve it'], 'B', 'Every angle of an equilateral triangle is 60°, and it can be drawn with compass arcs of equal radius.', 2],
    ],

    'SHS 3: Trigonometric graphs' => [
        ['What is the maximum value of y = 3 sin x?', ['1', '−3', '3', 'π'], 'C', 'sin x is never more than 1, so 3 sin x is at most 3.', 2],
        ['What is the period of y = sin x, in degrees?', ['90°', '180°', '270°', '360°'], 'D', 'The sine graph repeats every 360°.', 1],
        ['On the graph of y = cos x, the value 1 is reached at x =', ['0°', '90°', '180°', '270°'], 'A', 'cos 0° = 1. cos 90° = 0, cos 180° = −1 and cos 270° = 0.', 1],
        ['For 0° ≤ x ≤ 360°, sin x = 0.5 when x =', ['30° and 330°', '30° and 150°', '60° and 120°', '150° and 210°'], 'B', 'sin 30° = 0.5, and sine is also positive in the second quadrant: 180° − 30° = 150°.', 3],
        ['The graph of y = tan x has an asymptote (is undefined) at x =', ['0°', '45°', '90°', '180°'], 'C', 'tan x = sin x ÷ cos x, and cos 90° = 0.', 2],
    ],

    'SHS 3: Data handling and probability' => [
        ['A bag holds 4 red and 6 blue balls. Two are drawn without replacement. What is the probability that both are red?', ['4/25', '2/15', '2/5', '1/5'], 'B', 'P = 4/10 × 3/9 = 12/90 = 2/15.', 3],
        ['A and B are mutually exclusive, with P(A) = 0.5 and P(B) = 0.4. Find P(A or B).', ['0.2', '0.1', '0.45', '0.9'], 'D', 'For mutually exclusive events, P(A or B) = P(A) + P(B) = 0.9.', 2],
        ['In how many ways can 4 different books be arranged on a shelf?', ['24', '16', '4', '12'], 'A', '4! = 4 × 3 × 2 × 1 = 24.', 2],
        ['The standard deviation of a set of data measures', ['the middle value', 'the most common value', 'how spread out the data are about the mean', 'the total of the data'], 'C', 'A small standard deviation means the values are close to the mean; a large one means they are spread out.', 2],
        ['On a cumulative frequency curve, the median is read off at', ['half the total frequency', 'the highest frequency', 'the first class', 'the last class boundary'], 'A', 'The median is the middle value, so it sits at half of the total frequency.', 2],
        ['In how many ways can 2 prefects be chosen from 5 candidates?', ['20', '10', '25', '5'], 'B', 'Order does not matter, so use combinations: ⁵C₂ = (5 × 4) ÷ 2 = 10.', 3],
    ],
];
