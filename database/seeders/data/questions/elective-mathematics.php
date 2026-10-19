<?php

/*
| SHS Elective (Additional) Mathematics: WASSCE-style objective questions written for Otisam, not
| copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Algebra and functions' => [
        ['Find the sum of the roots of 2x² − 7x + 3 = 0.', ['−7/2', '3/2', '7/2', '7'], 'C', 'Sum of roots = −b/a = 7/2.', 2],
        ['Find the product of the roots of 2x² − 7x + 3 = 0.', ['−3/2', '3/2', '7/2', '3'], 'B', 'Product of roots = c/a = 3/2.', 2],
        ['For what values of k does x² + kx + 9 = 0 have equal roots?', ['k = ±3', 'k = ±6', 'k = ±9', 'k = 0'], 'B', 'Equal roots need b² − 4ac = 0: k² − 36 = 0, so k = ±6.', 3],
        ['Find the remainder when x³ − 2x² + 4 is divided by (x − 2).', ['0', '2', '4', '8'], 'C', 'By the remainder theorem, f(2) = 8 − 8 + 4 = 4.', 3],
        ['If f(x) = 3x − 2, find f⁻¹(x).', ['(x + 2)/3', '(x − 2)/3', '3x + 2', '1/(3x − 2)'], 'A', 'y = 3x − 2 ⇒ x = (y + 2)/3, so f⁻¹(x) = (x + 2)/3.', 2],
        ['Express log₃ 81 as a whole number.', ['3', '4', '9', '27'], 'B', '3⁴ = 81, so log₃ 81 = 4.', 1],
        ['Solve 3^(2x) = 27.', ['x = 1', 'x = 3/2', 'x = 2', 'x = 3'], 'B', '27 = 3³, so 2x = 3 and x = 3/2.', 2],
        ['Find the sum to infinity of the geometric series 8 + 4 + 2 + …', ['14', '16', '24', '∞'], 'B', 'S∞ = a/(1 − r) = 8/(1 − ½) = 16.', 3],
    ],

    'Matrices, vectors, binomial' => [
        ['Find the inverse of the matrix [[2, 1], [5, 3]].', ['[[3, −1], [−5, 2]]', '[[−3, 1], [5, −2]]', '[[3, 1], [5, 2]]', '[[2, −1], [−5, 3]]'], 'A', 'The determinant is 6 − 5 = 1, so the inverse is (1/1) × [[3, −1], [−5, 2]].', 3],
        ['If a = 3i + 4j, find |a|.', ['5', '7', '12', '25'], 'A', '|a| = √(3² + 4²) = √25 = 5.', 1],
        ['Find the dot product of a = 2i + 3j and b = 4i − j.', ['5', '8', '11', '−3'], 'A', 'a·b = 2 × 4 + 3 × (−1) = 8 − 3 = 5.', 2],
        ['Find the coefficient of x² in the expansion of (1 + 2x)⁵.', ['10', '20', '40', '80'], 'C', 'The term is ⁵C₂(2x)² = 10 × 4x² = 40x².', 3],
        ['Evaluate ⁶C₂.', ['12', '15', '30', '36'], 'B', '⁶C₂ = 6!/(2! 4!) = 15.', 2],
        ['Evaluate ⁵P₃.', ['10', '20', '60', '120'], 'C', '⁵P₃ = 5 × 4 × 3 = 60.', 2],
    ],

    'Trigonometry and coordinate geometry' => [
        ['Simplify sin²θ + cos²θ.', ['0', '1', '2', 'tan²θ'], 'B', 'This is the fundamental identity sin²θ + cos²θ = 1.', 1],
        ['Express 135° in radians.', ['π/4', '3π/4', '5π/4', '3π/2'], 'B', '135 × π/180 = 3π/4.', 2],
        ['Find the centre of the circle x² + y² − 4x + 6y − 3 = 0.', ['(2, −3)', '(−2, 3)', '(4, −6)', '(−4, 6)'], 'A', 'The centre is (−g, −f) with 2g = −4 and 2f = 6, giving (2, −3).', 3],
        ['Find the radius of the circle x² + y² − 4x + 6y − 3 = 0.', ['3', '4', '√13', '16'], 'B', 'r² = g² + f² − c = 4 + 9 + 3 = 16, so r = 4.', 3],
        ['Find the gradient of the line perpendicular to y = 2x + 5.', ['2', '−2', '1/2', '−1/2'], 'D', 'Perpendicular gradients multiply to −1: m = −1/2.', 2],
    ],

    'Calculus' => [
        ['Differentiate y = sin 3x with respect to x.', ['cos 3x', '3 sin 3x', '−3 cos 3x', '3 cos 3x'], 'D', 'By the chain rule, d/dx[sin 3x] = 3 cos 3x.', 3],
        ['Differentiate y = (2x + 1)³ with respect to x.', ['3(2x + 1)²', '6(2x + 1)', '(2x + 1)²', '6(2x + 1)²'], 'D', 'Chain rule: 3(2x + 1)² × 2 = 6(2x + 1)².', 3],
        ['Find dy/dx if y = x² eˣ.', ['2x eˣ', 'x² eˣ', 'eˣ(x² + 2x)', 'eˣ(2x + 1)'], 'C', 'Product rule: 2x eˣ + x² eˣ = eˣ(x² + 2x).', 4],
        ['Find the gradient of the curve y = x³ − 2x at x = 2.', ['4', '8', '10', '12'], 'C', 'dy/dx = 3x² − 2 = 12 − 2 = 10 at x = 2.', 2],
        ['Find the minimum value of y = x² − 6x + 10.', ['1', '3', '6', '10'], 'A', 'dy/dx = 2x − 6 = 0 at x = 3; y = 9 − 18 + 10 = 1.', 3],
        ['Evaluate ∫₀² 3x² dx.', ['4', '6', '8', '12'], 'C', '∫3x² dx = x³; [x³] from 0 to 2 = 8.', 2],
        ['Find ∫ (2x + 3) dx.', ['x² + 3x + c', '2 + c', 'x² + c', '2x² + 3x + c'], 'A', 'Integrate term by term: x² + 3x + c.', 1],
        ['Find the area between the curve y = 2x, the x-axis and the lines x = 0 and x = 3.', ['3', '6', '9', '18'], 'C', '∫₀³ 2x dx = [x²]₀³ = 9 square units.', 3],
    ],

    'Statistics and probability' => [
        ['A bag has 4 red and 6 green balls. Two are picked one after the other without replacement. Find the probability that both are red.', ['2/15', '4/25', '6/25', '1/5'], 'A', '4/10 × 3/9 = 12/90 = 2/15.', 4],
        ['Events A and B are independent with P(A) = 0.5 and P(B) = 0.4. Find P(A ∩ B).', ['0.1', '0.45', '0.9', '0.2'], 'D', 'For independent events P(A ∩ B) = P(A) × P(B) = 0.2.', 2],
        ['Find the variance of 2, 4, 6, 8 and 10.', ['4', '6', '8', '10'], 'C', 'The mean is 6; squared deviations 16, 4, 0, 4, 16 sum to 40; 40/5 = 8.', 3],
        ['The standard deviation of a set of data is 3. What is its variance?', ['√3', '3', '6', '9'], 'D', 'Variance = (standard deviation)² = 9.', 1],
    ],

    'Mechanics' => [
        ['A particle moves so that its displacement is s = t³ − 3t metres. Find its velocity at t = 2 s.', ['3 m/s', '6 m/s', '12 m/s', '9 m/s'], 'D', 'v = ds/dt = 3t² − 3 = 12 − 3 = 9 m/s.', 3],
        ['Two forces of 3 N and 4 N act at right angles on a particle. Find the magnitude of their resultant.', ['1 N', '12 N', '7 N', '5 N'], 'D', 'R = √(3² + 4²) = 5 N.', 2],
        ['A body of mass 2 kg moves with velocity 5 m/s. Find its kinetic energy.', ['10 J', '5 J', '50 J', '25 J'], 'D', 'KE = ½mv² = ½ × 2 × 25 = 25 J.', 1],
    ],

    'SHS 1: Binary operations, sets and the binomial theorem' => [
        ['A binary operation is defined by a * b = a + b − ab. Find 2 * 3.', ['5', '11', '−1', '1'], 'C', '2 * 3 = 2 + 3 − 2 × 3 = 5 − 6 = −1.', 2],
        ['Which binary operation on the real numbers is commutative?', ['a * b = a ÷ b', 'a * b = 2a + b', 'a * b = a − b', 'a * b = a + b + ab'], 'D', 'a + b + ab = b + a + ba for all a and b. The others change when a and b swap.', 2],
        ['n(A) = 20, n(B) = 15 and n(A ∩ B) = 8. Find n(A ∪ B).', ['43', '27', '35', '12'], 'B', 'n(A ∪ B) = n(A) + n(B) − n(A ∩ B) = 20 + 15 − 8 = 27.', 2],
        ['Find the coefficient of x² in the expansion of (1 + 2x)⁴.', ['8', '24', '16', '6'], 'B', 'The x² term is ⁴C₂ × (2x)² = 6 × 4x² = 24x².', 3],
        ['How many subsets does a set with 4 elements have?', ['8', '16', '4', '24'], 'B', 'A set with n elements has 2ⁿ subsets: 2⁴ = 16.', 2],
    ],

    'SHS 1: Surds, indices and logarithms' => [
        ['Evaluate 8^(2/3).', ['2', '16', '4', '5⅓'], 'C', '8^(1/3) = 2, and 2² = 4.', 2],
        ['Solve 2^(x + 1) = 32.', ['5', '4', '16', '3'], 'B', '32 = 2⁵, so x + 1 = 5 and x = 4.', 2],
        ['Evaluate log₂ 8 + log₂ 4.', ['12', '32', '6', '5'], 'D', 'log₂ 8 = 3 and log₂ 4 = 2, so the sum is 5. (It equals log₂ 32.)', 2],
        ['Simplify (√5 + √3)(√5 − √3).', ['√2', '8', '2', '2√15'], 'C', 'Difference of two squares: (√5)² − (√3)² = 5 − 3 = 2.', 2],
        ['Evaluate log₁₀ 100 − log₁₀ 10.', ['2', '10', '90', '1'], 'D', '2 − 1 = 1. (It equals log₁₀(100 ÷ 10).)', 1],
    ],

    'SHS 1: Sequences and functions' => [
        ['If f(x) = 2x + 1 and g(x) = x², find g(f(1)).', ['5', '3', '9', '4'], 'C', 'f(1) = 3, and g(3) = 9.', 2],
        ['What is the domain of f(x) = 1/(x − 3)?', ['x < 3 only', 'x > 3 only', 'all real numbers', 'all real numbers except 3'], 'D', 'The function is undefined where the denominator is zero, at x = 3.', 2],
        ['Find the sum of the first 10 terms of the geometric sequence 1, 2, 4, 8, …', ['1,024', '1,023', '512', '2,047'], 'B', 'Sₙ = a(rⁿ − 1)/(r − 1) = (2¹⁰ − 1)/1 = 1,023.', 3],
        ['Find the inverse of f(x) = (x − 4)/2.', ['(x + 4)/2', '2x − 4', '2x + 4', 'x/2 − 4'], 'C', 'Let y = (x − 4)/2, so x = 2y + 4. Hence f⁻¹(x) = 2x + 4.', 2],
    ],

    'SHS 1: Matrices' => [
        ['Find the determinant of the matrix (3 2; 1 4).', ['−10', '14', '12', '10'], 'D', 'det = 3 × 4 − 2 × 1 = 12 − 2 = 10.', 1],
        ['A 2 × 3 matrix is multiplied by a 3 × 2 matrix. What is the order of the product?', ['3 × 3', '2 × 2', '2 × 3', 'they cannot be multiplied'], 'B', 'The inner numbers match (3 and 3); the outer numbers give the order, 2 × 2.', 2],
        ['Find the inverse of the matrix (2 1; 5 3).', ['(3 1; 5 2)', '(2 −1; −5 3)', '(3 −1; −5 2)', '(−3 1; 5 −2)'], 'C', 'det = 6 − 5 = 1. Swap the leading diagonal, negate the other entries and divide by 1.', 3],
        ['A square matrix has no inverse when', ['its determinant is 1', 'it has negative entries', 'it is 2 × 2', 'its determinant is zero'], 'D', 'Such a matrix is called singular.', 2],
        ['Find (1 2; 3 4) + (0 1; 1 0).', ['(1 2; 3 4)', '(1 3; 4 4)', '(0 2; 3 0)', '(1 3; 3 4)'], 'B', 'Add corresponding entries: (1+0, 2+1; 3+1, 4+0).', 1],
    ],

    'SHS 1: Straight lines' => [
        ['Find the gradient of the line through (1, 2) and (3, 8).', ['6', '1/3', '3', '−3'], 'C', 'm = (8 − 2) ÷ (3 − 1) = 6 ÷ 2 = 3.', 1],
        ['What is the gradient of a line perpendicular to y = 2x + 1?', ['−2', '2', '1/2', '−1/2'], 'D', 'Perpendicular gradients multiply to −1: 2 × (−1/2) = −1.', 2],
        ['Find the distance between (1, 1) and (4, 5).', ['7', '5', '25', '√7'], 'B', 'd = √(3² + 4²) = √25 = 5.', 2],
        ['Find the midpoint of the line joining (2, 6) and (8, −2).', ['(3, 4)', '(10, 4)', '(5, 2)', '(6, −8)'], 'C', 'Midpoint = ((2 + 8)/2, (6 − 2)/2) = (5, 2).', 1],
    ],

    'SHS 1: Vectors' => [
        ['Find |3i − 4j|.', ['25', '7', '1', '5'], 'D', '√(3² + (−4)²) = √25 = 5.', 1],
        ['If a = 2i − j and b = 3i + 4j, find a · b.', ['10', '2', '−2', '5'], 'B', 'a · b = 2 × 3 + (−1) × 4 = 6 − 4 = 2.', 2],
        ['Find the unit vector in the direction of 3i + 4j.', ['(1/3)i + (1/4)j', '3i + 4j', '(3/5)i + (4/5)j', '(4/5)i + (3/5)j'], 'C', 'Divide by the magnitude, 5.', 2],
        ['Two non-zero vectors are perpendicular when', ['their sum is zero', 'their magnitudes are equal', 'they are parallel', 'their dot product is zero'], 'D', 'a · b = |a||b| cos 90° = 0.', 2],
    ],

    'SHS 1: Trigonometric functions' => [
        ['What is the exact value of sin 120°?', ['−√3/2', '√3/2', '1/2', '−1/2'], 'B', 'sin 120° = sin(180° − 60°) = sin 60° = √3/2.', 2],
        ['What is cos(−60°)?', ['√3/2', '−1/2', '1/2', '−√3/2'], 'C', 'Cosine is an even function: cos(−θ) = cos θ.', 2],
        ['Solve 2 sin θ = 1 for 0° ≤ θ ≤ 180°.', ['45° and 135°', '60° and 120°', '30° only', '30° and 150°'], 'D', 'sin θ = 1/2 gives 30°, and also 180° − 30° = 150°.', 2],
        ['Convert 135° to radians.', ['2π/3', '3π/4', '5π/6', 'π/4'], 'B', '135 × π/180 = 3π/4.', 2],
    ],

    'SHS 1: Limits and differentiation' => [
        ['Evaluate the limit of (x² − 4)/(x − 2) as x → 2.', ['2', '0', '4', 'it does not exist'], 'C', 'Factorise: (x − 2)(x + 2)/(x − 2) = x + 2, which tends to 4.', 3],
        ['Differentiate 3x⁴ with respect to x.', ['4x³', '3x³', '12x⁴', '12x³'], 'D', 'd/dx(axⁿ) = naxⁿ⁻¹ = 4 × 3x³.', 1],
        ['Differentiate 5x² − 3x + 7 with respect to x.', ['10x + 7', '10x − 3', '5x − 3', '10x'], 'B', 'The derivative of a constant is 0.', 1],
        ['Find the gradient of the curve y = x³ at x = 2.', ['6', '8', '12', '3'], 'C', 'dy/dx = 3x² = 3 × 4 = 12.', 2],
        ['Differentiate y = 1/x with respect to x.', ['−1/x', '1/x²', 'ln x', '−1/x²'], 'D', 'y = x⁻¹, so dy/dx = −x⁻² = −1/x².', 2],
    ],

    'SHS 1: Statistics' => [
        ['Find the variance of 2, 4 and 6.', ['8', '8/3', '4', '16/3'], 'B', 'Mean = 4. Squared deviations: 4, 0, 4. Variance = 8 ÷ 3.', 3],
        ['The standard deviation of a data set is', ['the mean of the data', 'the square of the variance', 'the square root of the variance', 'the range divided by 2'], 'C', 'It measures spread in the same units as the data.', 1],
        ['To estimate the mean of grouped data, each class is represented by its', ['width', 'upper boundary', 'frequency only', 'mid-point (class mark)'], 'D', 'Mean ≈ Σfx ÷ Σf, where x is the class mid-point.', 2],
        ['The mean of five numbers is 12. Four of them are 10, 14, 8 and 16. Find the fifth.', ['10', '12', '48', '60'], 'B', 'Total = 5 × 12 = 60. The four add up to 48, so the fifth is 12.', 2],
    ],

    'SHS 1: Permutations, combinations and probability' => [
        ['Evaluate ⁶P₂.', ['12', '15', '30', '36'], 'C', '⁶P₂ = 6 × 5 = 30.', 2],
        ['Evaluate ⁷C₃.', ['7', '210', '21', '35'], 'D', '⁷C₃ = (7 × 6 × 5) ÷ (3 × 2 × 1) = 35.', 2],
        ['In how many ways can the letters of the word GHANA be arranged?', ['120', '60', '24', '30'], 'B', 'There are 5 letters with A repeated twice: 5! ÷ 2! = 60.', 3],
        ['Two fair coins are tossed. What is the probability of at least one head?', ['1/4', '1/2', '3/4', '1'], 'C', 'P(no heads) = 1/4, so P(at least one head) = 1 − 1/4 = 3/4.', 2],
    ],

    'SHS 2: Sets and binomial expansions' => [
        ['How many terms are there in the expansion of (a + b)⁷?', ['6', '7', '14', '8'], 'D', '(a + b)ⁿ has n + 1 terms.', 1],
        ['What is the coefficient of the middle term in the expansion of (1 + x)⁶?', ['15', '20', '6', '30'], 'B', 'The middle (4th) term is ⁶C₃x³ = 20x³.', 2],
        ['Use the first two terms of the binomial expansion to estimate 1.02⁵.', ['1.20', '1.02', '1.10', '1.01'], 'C', '(1 + x)⁵ ≈ 1 + 5x = 1 + 5 × 0.02 = 1.10. (The exact value is about 1.104.)', 3],
    ],

    'SHS 2: Sequences, series and inequalities' => [
        ['Solve x² − 5x + 4 < 0.', ['x > 4', 'x < 1 or x > 4', '−4 < x < −1', '1 < x < 4'], 'D', '(x − 1)(x − 4) < 0 between the roots.', 3],
        ['Solve |x − 3| < 2.', ['x < 1', '1 < x < 5', '−5 < x < 5', 'x > 5'], 'B', '−2 < x − 3 < 2, so 1 < x < 5.', 3],
        ['Find 1 + 2 + 3 + … + 100.', ['10,100', '5,000', '5,050', '4,950'], 'C', 'Sₙ = n(n + 1)/2 = 100 × 101 ÷ 2 = 5,050.', 2],
    ],

    'SHS 2: Polynomial functions' => [
        ['Find the remainder when x³ − 2x + 5 is divided by (x − 2).', ['13', '5', '1', '9'], 'D', 'By the remainder theorem, f(2) = 8 − 4 + 5 = 9.', 2],
        ['Which is a factor of x³ − 6x² + 11x − 6?', ['x + 1', 'x − 1', 'x + 2', 'x − 5'], 'B', 'f(1) = 1 − 6 + 11 − 6 = 0, so (x − 1) is a factor.', 2],
        ['For 2x² − 8x + 3 = 0, what is the sum of the roots?', ['3/2', '−4', '4', '8'], 'C', 'Sum of roots = −b/a = 8/2 = 4.', 2],
        ['For 2x² − 8x + 3 = 0, what is the product of the roots?', ['3', '−4', '4', '3/2'], 'D', 'Product of roots = c/a = 3/2.', 2],
    ],

    'SHS 2: Circles and loci' => [
        ['Find the centre and radius of x² + y² − 4x + 6y − 12 = 0.', ['centre (−2, 3), radius 5', 'centre (2, −3), radius 5', 'centre (2, −3), radius 25', 'centre (4, −6), radius 12'], 'B', 'Complete the squares: (x − 2)² + (y + 3)² = 12 + 4 + 9 = 25.', 3],
        ['What is the equation of the circle with centre (0, 0) and radius 3?', ['x + y = 3', 'x² + y² = 3', 'x² + y² = 9', 'x² − y² = 9'], 'C', 'A circle centred at the origin has equation x² + y² = r².', 1],
        ['The locus of points equidistant from two fixed points A and B is', ['a parabola', 'a circle through A and B', 'the line AB', 'the perpendicular bisector of AB'], 'D', 'Every point on the perpendicular bisector is the same distance from A and B.', 2],
        ['The locus of points 4 cm from a fixed point is', ['a straight line', 'a circle of radius 4 cm', 'a square', 'two parallel lines'], 'B', 'In a plane, all points at a fixed distance from a point form a circle.', 1],
    ],

    'SHS 2: Vectors and matrices' => [
        ['A is (2, 4) and B is (6, 0). What is the position vector of the midpoint of AB?', ['(2, −2)', '(8, 4)', '(4, 2)', '(4, 4)'], 'C', 'Midpoint = ½(a + b) = ½(8, 4) = (4, 2).', 2],
        ['If a · b = ½|a||b|, what is the angle between a and b?', ['90°', '30°', '45°', '60°'], 'D', 'cos θ = ½, so θ = 60°.', 3],
        ['The matrix (0 −1; 1 0) represents', ['a reflection in the x-axis', 'a rotation of 90° anticlockwise about the origin', 'an enlargement by 2', 'a translation'], 'B', 'It maps (1, 0) to (0, 1) and (0, 1) to (−1, 0).', 3],
        ['The matrix (1 0; 0 −1) represents', ['a rotation of 180°', 'a reflection in the y-axis', 'a reflection in the x-axis', 'a shear'], 'C', 'It maps (x, y) to (x, −y).', 2],
    ],

    'SHS 2: Correlation' => [
        ['A rank correlation coefficient of +1 means', ['weak positive correlation', 'no correlation', 'perfect negative correlation', 'perfect positive correlation'], 'D', 'The two rankings are identical.', 1],
        ['A correlation coefficient of −0.9 shows', ['weak positive correlation', 'strong negative correlation', 'no correlation', 'strong positive correlation'], 'B', 'As one variable rises, the other tends to fall.', 1],
        ['For 5 pairs of ranks, Σd² = 4. Using Spearman\'s formula, find the rank correlation coefficient.', ['0.96', '0.2', '0.8', '−0.8'], 'C', 'r = 1 − 6Σd² ÷ n(n² − 1) = 1 − 24 ÷ 120 = 0.8.', 3],
    ],

    'SHS 2: Indices and logarithms' => [
        ['Solve log₃ x = 4.', ['7', '12', '64', '81'], 'D', 'x = 3⁴ = 81.', 2],
        ['Evaluate log₄ 8.', ['2', '3/2', '2/3', '1/2'], 'B', 'log₄ 8 = log₂ 8 ÷ log₂ 4 = 3 ÷ 2.', 3],
    ],

    'SHS 2: Trigonometric identities' => [
        ['Which identity is always true?', ['sin θ + cos θ = 1', '1 + sin²θ = cos²θ', 'tan θ = cos θ ÷ sin θ', '1 + tan²θ = sec²θ'], 'D', 'Divide sin²θ + cos²θ = 1 by cos²θ.', 2],
        ['sin 2θ is equal to', ['2 sin θ', '2 sin θ cos θ', 'sin²θ − cos²θ', '1 − 2 sin θ'], 'B', 'This is the double-angle formula for sine.', 2],
        ['cos²θ − sin²θ is equal to', ['1', 'sin 2θ', 'cos 2θ', 'tan 2θ'], 'C', 'This is one form of the double-angle formula for cosine.', 2],
    ],

    'SHS 2: Differentiation and integration' => [
        ['Differentiate sin x with respect to x.', ['tan x', '−cos x', '−sin x', 'cos x'], 'D', 'd/dx(sin x) = cos x, with x in radians.', 1],
        ['Differentiate x sin x with respect to x.', ['cos x', 'sin x + x cos x', 'x cos x', 'sin x − x cos x'], 'B', 'Product rule: (1)(sin x) + (x)(cos x).', 3],
        ['Differentiate (2x + 1)³ with respect to x.', ['6(2x + 1)³', '3(2x + 1)²', '6(2x + 1)²', '2(2x + 1)²'], 'C', 'Chain rule: 3(2x + 1)² × 2.', 3],
        ['Find ∫3x² dx.', ['x² + c', '6x + c', '3x³ + c', 'x³ + c'], 'D', 'Increase the power by 1 and divide by the new power: 3x³/3 = x³.', 1],
        ['Evaluate the integral of 2x from x = 0 to x = 2.', ['2', '4', '8', '0'], 'B', '[x²] from 0 to 2 = 4 − 0 = 4.', 2],
        ['Find ∫(4x³ − 2) dx.', ['x⁴ − 2 + c', '12x² + c', 'x⁴ − 2x + c', '4x⁴ − 2x + c'], 'C', 'Integrate term by term: 4x⁴/4 − 2x.', 2],
    ],

    'SHS 2: Applications of differentiation' => [
        ['At what value of x does y = x² − 6x + 5 have a stationary point?', ['5', '−3', '6', '3'], 'D', 'dy/dx = 2x − 6 = 0, so x = 3.', 2],
        ['The stationary point of y = x² − 6x + 5 is', ['a minimum', 'a maximum', 'a point of inflexion', 'not defined'], 'A', 'd²y/dx² = 2, which is positive, so it is a minimum.', 2],
        ['A particle moves so that s = t³ − 2t. Find its velocity at t = 2.', ['10', '4', '8', '12'], 'A', 'v = ds/dt = 3t² − 2 = 12 − 2 = 10.', 2],
    ],

    'SHS 2: Probability' => [
        ['P(A ∩ B) = 0.2 and P(B) = 0.5. Find P(A | B).', ['0.4', '0.1', '0.7', '0.25'], 'A', 'P(A | B) = P(A ∩ B) ÷ P(B) = 0.2 ÷ 0.5 = 0.4.', 2],
        ['A fair coin is tossed 3 times. What is the probability of exactly 2 heads?', ['3/8', '1/8', '1/2', '2/3'], 'A', '³C₂ × (1/2)³ = 3/8.', 3],
    ],

    'SHS 3: Logic' => [
        ['The implication "if p then q" (p → q) is false only when', ['p is true and q is false', 'p is false and q is true', 'both are false', 'both are true'], 'A', 'In every other case the implication is true.', 2],
        ['The converse of p → q is', ['q → p', '¬p → ¬q', '¬q → ¬p', 'p ∧ q'], 'A', '¬q → ¬p is the contrapositive; ¬p → ¬q is the inverse.', 2],
        ['The disjunction p ∨ q is false only when', ['both p and q are false', 'p is true', 'q is true', 'both are true'], 'A', 'An "or" statement needs just one true part to be true.', 1],
    ],

    'SHS 3: Matrices and determinants' => [
        ['Find the determinant of the 3 × 3 diagonal matrix with diagonal entries 1, 2 and 3.', ['6', '0', '3', '9'], 'A', 'The determinant of a diagonal matrix is the product of its diagonal entries.', 2],
        ['To solve the system AX = B when A is invertible, you calculate', ['X = A⁻¹B', 'X = BA⁻¹', 'X = AB', 'X = B − A'], 'A', 'Multiply both sides on the left by A⁻¹.', 2],
    ],

    'SHS 3: Parabolas' => [
        ['What is the focus of the parabola y² = 8x?', ['(2, 0)', '(8, 0)', '(0, 2)', '(4, 0)'], 'A', 'y² = 4ax with 4a = 8, so a = 2 and the focus is (a, 0).', 3],
        ['What is the directrix of the parabola y² = 8x?', ['x = −2', 'x = 2', 'y = −2', 'x = −8'], 'A', 'The directrix is x = −a = −2.', 3],
        ['What is the vertex of the parabola y = (x − 1)² + 3?', ['(1, 3)', '(−1, 3)', '(1, −3)', '(3, 1)'], 'A', 'y = (x − h)² + k has vertex (h, k).', 2],
    ],

    'SHS 3: Trigonometry' => [
        ['What is the greatest value of 3 sin x + 4 cos x?', ['5', '7', '1', '12'], 'A', '3 sin x + 4 cos x = 5 sin(x + α), where R = √(3² + 4²) = 5.', 3],
        ['What is the general solution of sin x = 0 (x in radians)?', ['x = nπ, n an integer', 'x = 2nπ only', 'x = nπ/2', 'x = π/2 + nπ'], 'A', 'sin x = 0 at every multiple of π.', 3],
        ['sin(A + B) is equal to', ['sin A cos B + cos A sin B', 'sin A + sin B', 'cos A cos B − sin A sin B', 'sin A sin B + cos A cos B'], 'A', 'This is the compound-angle formula for sine.', 2],
    ],

    'SHS 3: Integration' => [
        ['Find ∫eˣ dx.', ['eˣ + c', 'xeˣ + c', 'eˣ⁺¹ + c', 'ln x + c'], 'A', 'eˣ is its own derivative and its own integral.', 2],
        ['Find ∫(1/x) dx for x ≠ 0.', ['ln|x| + c', '−1/x² + c', 'x⁰ + c', '1/x² + c'], 'A', 'The derivative of ln|x| is 1/x.', 2],
        ['Find ∫cos x dx.', ['sin x + c', '−sin x + c', 'cos x + c', '−cos x + c'], 'A', 'd/dx(sin x) = cos x.', 1],
    ],

    'SHS 3: Applications of integration' => [
        ['Find the area under y = x² from x = 0 to x = 3.', ['9', '27', '3', '6'], 'A', '[x³/3] from 0 to 3 = 27/3 = 9 square units.', 2],
        ['The volume formed when the region under y = f(x) from a to b is rotated about the x-axis is', ['π ∫ y² dx from a to b', '∫ y dx', '2π ∫ y dx', 'π ∫ y dx'], 'A', 'Each thin slice is a disc of area πy².', 3],
        ['A particle starts at the origin with velocity v = 2t m/s. How far does it travel in the first 3 s?', ['9 m', '6 m', '3 m', '18 m'], 'A', 's = ∫₀³ 2t dt = [t²] from 0 to 3 = 9 m.', 2],
    ],

    'SHS 3: Statistics and the normal distribution' => [
        ['The normal distribution curve is', ['bell-shaped and symmetrical about the mean', 'skewed to the right', 'a straight line', 'U-shaped'], 'A', 'Mean, median and mode are equal.', 1],
        ['A score of 70 comes from a normal distribution with mean 60 and standard deviation 5. What is its z-score?', ['2', '10', '0.5', '14'], 'A', 'z = (x − μ) ÷ σ = (70 − 60) ÷ 5 = 2.', 2],
        ['About what percentage of a normal distribution lies within one standard deviation of the mean?', ['68%', '95%', '99.7%', '50%'], 'A', 'About 95% lies within two standard deviations and 99.7% within three.', 2],
    ],

    'SHS 3: Probability distributions' => [
        ['X follows a binomial distribution with n = 20 and p = 0.3. What is the mean of X?', ['6', '0.3', '14', '4.2'], 'A', 'Mean = np = 20 × 0.3 = 6.', 2],
        ['X follows a binomial distribution with n = 20 and p = 0.3. What is the variance of X?', ['4.2', '6', '0.21', '14'], 'A', 'Variance = np(1 − p) = 20 × 0.3 × 0.7 = 4.2.', 3],
        ['What is the expected score when a fair six-sided die is thrown?', ['3.5', '3', '6', '21'], 'A', 'E(X) = (1 + 2 + 3 + 4 + 5 + 6) ÷ 6 = 21 ÷ 6 = 3.5.', 2],
    ],
];
