<?php

/*
| SHS Elective (Additional) Mathematics: WASSCE-style objective questions written for Otisam, not
| copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Algebra and functions
    ['Find the sum of the roots of 2x² − 7x + 3 = 0.', ['−7/2', '3/2', '7/2', '7'], 'C', 'Sum of roots = −b/a = 7/2.', 2],
    ['Find the product of the roots of 2x² − 7x + 3 = 0.', ['−3/2', '3/2', '7/2', '3'], 'B', 'Product of roots = c/a = 3/2.', 2],
    ['For what values of k does x² + kx + 9 = 0 have equal roots?', ['k = ±3', 'k = ±6', 'k = ±9', 'k = 0'], 'B', 'Equal roots need b² − 4ac = 0: k² − 36 = 0, so k = ±6.', 3],
    ['Find the remainder when x³ − 2x² + 4 is divided by (x − 2).', ['0', '2', '4', '8'], 'C', 'By the remainder theorem, f(2) = 8 − 8 + 4 = 4.', 3],
    ['If f(x) = 3x − 2, find f⁻¹(x).', ['(x + 2)/3', '(x − 2)/3', '3x + 2', '1/(3x − 2)'], 'A', 'y = 3x − 2 ⇒ x = (y + 2)/3, so f⁻¹(x) = (x + 2)/3.', 2],
    ['Express log₃ 81 as a whole number.', ['3', '4', '9', '27'], 'B', '3⁴ = 81, so log₃ 81 = 4.', 1],
    ['Solve 3^(2x) = 27.', ['x = 1', 'x = 3/2', 'x = 2', 'x = 3'], 'B', '27 = 3³, so 2x = 3 and x = 3/2.', 2],
    ['Find the sum to infinity of the geometric series 8 + 4 + 2 + …', ['14', '16', '24', '∞'], 'B', 'S∞ = a/(1 − r) = 8/(1 − ½) = 16.', 3],

    // Matrices, vectors, binomial
    ['Find the inverse of the matrix [[2, 1], [5, 3]].', ['[[3, −1], [−5, 2]]', '[[−3, 1], [5, −2]]', '[[3, 1], [5, 2]]', '[[2, −1], [−5, 3]]'], 'A', 'The determinant is 6 − 5 = 1, so the inverse is (1/1) × [[3, −1], [−5, 2]].', 3],
    ['If a = 3i + 4j, find |a|.', ['5', '7', '12', '25'], 'A', '|a| = √(3² + 4²) = √25 = 5.', 1],
    ['Find the dot product of a = 2i + 3j and b = 4i − j.', ['5', '8', '11', '−3'], 'A', 'a·b = 2 × 4 + 3 × (−1) = 8 − 3 = 5.', 2],
    ['Find the coefficient of x² in the expansion of (1 + 2x)⁵.', ['10', '20', '40', '80'], 'C', 'The term is ⁵C₂(2x)² = 10 × 4x² = 40x².', 3],
    ['Evaluate ⁶C₂.', ['12', '15', '30', '36'], 'B', '⁶C₂ = 6!/(2! 4!) = 15.', 2],
    ['Evaluate ⁵P₃.', ['10', '20', '60', '120'], 'C', '⁵P₃ = 5 × 4 × 3 = 60.', 2],

    // Trigonometry and coordinate geometry
    ['Simplify sin²θ + cos²θ.', ['0', '1', '2', 'tan²θ'], 'B', 'This is the fundamental identity sin²θ + cos²θ = 1.', 1],
    ['Express 135° in radians.', ['π/4', '3π/4', '5π/4', '3π/2'], 'B', '135 × π/180 = 3π/4.', 2],
    ['Find the centre of the circle x² + y² − 4x + 6y − 3 = 0.', ['(2, −3)', '(−2, 3)', '(4, −6)', '(−4, 6)'], 'A', 'The centre is (−g, −f) with 2g = −4 and 2f = 6, giving (2, −3).', 3],
    ['Find the radius of the circle x² + y² − 4x + 6y − 3 = 0.', ['3', '4', '√13', '16'], 'B', 'r² = g² + f² − c = 4 + 9 + 3 = 16, so r = 4.', 3],
    ['Find the gradient of the line perpendicular to y = 2x + 5.', ['2', '−2', '1/2', '−1/2'], 'D', 'Perpendicular gradients multiply to −1: m = −1/2.', 2],

    // Calculus
    ['Differentiate y = sin 3x with respect to x.', ['cos 3x', '3 sin 3x', '−3 cos 3x', '3 cos 3x'], 'D', 'By the chain rule, d/dx[sin 3x] = 3 cos 3x.', 3],
    ['Differentiate y = (2x + 1)³ with respect to x.', ['3(2x + 1)²', '6(2x + 1)', '(2x + 1)²', '6(2x + 1)²'], 'D', 'Chain rule: 3(2x + 1)² × 2 = 6(2x + 1)².', 3],
    ['Find dy/dx if y = x² eˣ.', ['2x eˣ', 'x² eˣ', 'eˣ(x² + 2x)', 'eˣ(2x + 1)'], 'C', 'Product rule: 2x eˣ + x² eˣ = eˣ(x² + 2x).', 4],
    ['Find the gradient of the curve y = x³ − 2x at x = 2.', ['4', '8', '10', '12'], 'C', 'dy/dx = 3x² − 2 = 12 − 2 = 10 at x = 2.', 2],
    ['Find the minimum value of y = x² − 6x + 10.', ['1', '3', '6', '10'], 'A', 'dy/dx = 2x − 6 = 0 at x = 3; y = 9 − 18 + 10 = 1.', 3],
    ['Evaluate ∫₀² 3x² dx.', ['4', '6', '8', '12'], 'C', '∫3x² dx = x³; [x³] from 0 to 2 = 8.', 2],
    ['Find ∫ (2x + 3) dx.', ['x² + 3x + c', '2 + c', 'x² + c', '2x² + 3x + c'], 'A', 'Integrate term by term: x² + 3x + c.', 1],
    ['Find the area between the curve y = 2x, the x-axis and the lines x = 0 and x = 3.', ['3', '6', '9', '18'], 'C', '∫₀³ 2x dx = [x²]₀³ = 9 square units.', 3],

    // Statistics and probability
    ['A bag has 4 red and 6 green balls. Two are picked one after the other without replacement. Find the probability that both are red.', ['2/15', '4/25', '6/25', '1/5'], 'A', '4/10 × 3/9 = 12/90 = 2/15.', 4],
    ['Events A and B are independent with P(A) = 0.5 and P(B) = 0.4. Find P(A ∩ B).', ['0.1', '0.45', '0.9', '0.2'], 'D', 'For independent events P(A ∩ B) = P(A) × P(B) = 0.2.', 2],
    ['Find the variance of 2, 4, 6, 8 and 10.', ['4', '6', '8', '10'], 'C', 'The mean is 6; squared deviations 16, 4, 0, 4, 16 sum to 40; 40/5 = 8.', 3],
    ['The standard deviation of a set of data is 3. What is its variance?', ['√3', '3', '6', '9'], 'D', 'Variance = (standard deviation)² = 9.', 1],

    // Mechanics
    ['A particle moves so that its displacement is s = t³ − 3t metres. Find its velocity at t = 2 s.', ['3 m/s', '6 m/s', '12 m/s', '9 m/s'], 'D', 'v = ds/dt = 3t² − 3 = 12 − 3 = 9 m/s.', 3],
    ['Two forces of 3 N and 4 N act at right angles on a particle. Find the magnitude of their resultant.', ['1 N', '12 N', '7 N', '5 N'], 'D', 'R = √(3² + 4²) = 5 N.', 2],
    ['A body of mass 2 kg moves with velocity 5 m/s. Find its kinetic energy.', ['10 J', '5 J', '50 J', '25 J'], 'D', 'KE = ½mv² = ½ × 2 × 25 = 25 J.', 1],
];
