<?php

/*
| SHS Core Mathematics: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers. Topics follow the WASSCE Core Mathematics syllabus: number and numeration, algebra,
| mensuration, plane geometry, coordinate geometry, trigonometry, statistics and probability.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Number and numeration
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

    // Algebra
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

    // Mensuration
    ['Find the area of a sector of a circle of radius 7 cm that subtends an angle of 90° at the centre. (Take π = 22/7)', ['11 cm²', '154 cm²', '77 cm²', '38.5 cm²'], 'D', 'Area = (θ/360) × πr² = ¼ × 22/7 × 49 = 38.5 cm².', 3],
    ['Find the volume of a cylinder of radius 7 cm and height 10 cm. (Take π = 22/7)', ['1,540 cm³', '440 cm³', '3,080 cm³', '15,400 cm³'], 'A', 'V = πr²h = 22/7 × 49 × 10 = 1,540 cm³.', 2],
    ['Find the total surface area of a cube of side 4 cm.', ['16 cm²', '64 cm²', '96 cm²', '128 cm²'], 'C', 'A cube has 6 faces each 4 × 4 = 16 cm², so 6 × 16 = 96 cm².', 2],

    // Plane geometry
    ['An arc subtends an angle of 100° at the centre of a circle. What angle does it subtend at the circumference?', ['50°', '100°', '130°', '200°'], 'A', 'The angle at the centre is twice the angle at the circumference: 100° ÷ 2 = 50°.', 2],
    ['What is the size of an angle in a semicircle?', ['45°', '60°', '90°', '180°'], 'C', 'The angle subtended by a diameter at the circumference is a right angle.', 1],
    ['One angle of a cyclic quadrilateral is 70°. Find the angle opposite it.', ['20°', '70°', '110°', '290°'], 'C', 'Opposite angles of a cyclic quadrilateral add up to 180°: 180 − 70 = 110°.', 2],

    // Coordinate geometry
    ['Find the distance between the points (1, 2) and (4, 6).', ['3', '4', '5', '7'], 'C', 'd = √[(4 − 1)² + (6 − 2)²] = √(9 + 16) = √25 = 5.', 2],
    ['Find the midpoint of the line joining (2, 4) and (6, 10).', ['(4, 7)', '(8, 14)', '(2, 3)', '(3, 7)'], 'A', 'Midpoint = ((2 + 6)/2, (4 + 10)/2) = (4, 7).', 1],
    ['Find the equation of the line with gradient 2 that passes through (0, −3).', ['y = 2x + 3', 'y = −3x + 2', 'y = 2x − 3', 'y = 3x − 2'], 'C', 'y = mx + c with m = 2 and c = −3 (the y-intercept).', 2],

    // Trigonometry
    ['If sin θ = 3/5 and θ is acute, find cos θ.', ['3/4', '5/3', '5/4', '4/5'], 'D', 'Using a 3-4-5 triangle: opposite 3, hypotenuse 5, adjacent 4, so cos θ = 4/5.', 2],
    ['Find the value of tan 45°.', ['0', '1/√3', '√3', '1'], 'D', 'In a right isosceles triangle the opposite and adjacent sides are equal, so tan 45° = 1.', 1],
    ['A ladder 10 m long leans against a wall and makes an angle of 60° with the ground. How high up the wall does it reach?', ['5 m', '20 m', '10√3 m', '5√3 m'], 'D', 'Height = 10 sin 60° = 10 × √3/2 = 5√3 m.', 3],

    // Statistics and probability
    ['Find the mean of the distribution: x = 1, 2, 3 with frequencies 2, 3, 5.', ['2.3', '2.0', '2.5', '3.0'], 'A', 'Σfx = 2 + 6 + 15 = 23 and Σf = 10, so the mean = 23/10 = 2.3.', 2],
    ['Find the range of 12, 7, 15, 3 and 9.', ['9', '18', '15', '12'], 'D', 'Range = highest − lowest = 15 − 3 = 12.', 1],
    ['Two fair coins are tossed together. What is the probability of getting two heads?', ['1/4', '1/3', '1/2', '3/4'], 'A', 'Outcomes: HH, HT, TH, TT. Only HH is two heads: 1/4.', 2],
    ['A fair die is thrown once. What is the probability of getting a number greater than 4?', ['1/6', '2/3', '1/2', '1/3'], 'D', 'Numbers greater than 4 are 5 and 6: 2/6 = 1/3.', 1],
];
