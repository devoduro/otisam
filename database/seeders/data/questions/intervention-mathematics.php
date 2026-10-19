<?php

/*
| SHS Intervention Mathematics (2023 curriculum support course): objective questions written for Otisam, not copied from any paper.
| A remedial course, so the questions stay at a foundation level: number, fractions, percentages, simple algebra and measurement.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Number' => [
        ['Work out −7 + 12.', ['−19', '19', '−5', '5'], 'D', 'Start at −7 on the number line and move 12 steps to the right: you reach 5.', 1],
        ['Which of these numbers is the smallest?', ['−8', '−3', '0', '2'], 'A', 'On the number line −8 is furthest to the left, so it is the smallest.', 1],
        ['Find the LCM of 4 and 6.', ['24', '2', '10', '12'], 'D', 'Multiples of 4: 4, 8, 12… Multiples of 6: 6, 12… The lowest common multiple is 12.', 2],
        ['Find the HCF of 12 and 18.', ['3', '6', '2', '36'], 'B', 'Factors of 12: 1, 2, 3, 4, 6, 12. Factors of 18: 1, 2, 3, 6, 9, 18. The highest common factor is 6.', 2],
        ['Which of these is a prime number?', ['9', '15', '21', '17'], 'D', '17 has only two factors, 1 and 17. 9 = 3 × 3, 15 = 3 × 5 and 21 = 3 × 7.', 1],
        ['Find the value of 2³.', ['6', '5', '8', '9'], 'C', '2³ = 2 × 2 × 2 = 8.', 1],
        ['Find the square root of 81.', ['8', '18', '40.5', '9'], 'D', '9 × 9 = 81, so √81 = 9.', 1],
        ['Write 4,500 in standard form.', ['45 × 10²', '4.5 × 10²', '4.5 × 10³', '0.45 × 10⁴'], 'C', 'In standard form the first number is between 1 and 10: 4,500 = 4.5 × 1,000 = 4.5 × 10³.', 2],
        ['Round 3.476 to 2 decimal places.', ['3.47', '3.5', '3.48', '3.40'], 'C', 'The third decimal digit is 6, which is 5 or more, so round 3.47 up to 3.48.', 2],
    ],

    'Fractions, decimals and percentages' => [
        ['Work out 3/4 + 1/8.', ['4/12', '7/8', '1/2', '4/8'], 'B', 'Change 3/4 to eighths: 6/8. Then 6/8 + 1/8 = 7/8.', 2],
        ['Work out 3/5 ÷ 3/10.', ['2', '9/50', '1/2', '6/5'], 'A', 'Dividing by a fraction is multiplying by its reciprocal: 3/5 × 10/3 = 30/15 = 2.', 3],
        ['Write 0.25 as a fraction in its lowest terms.', ['1/4', '2/5', '1/25', '25/10'], 'A', '0.25 = 25/100, and dividing top and bottom by 25 gives 1/4.', 1],
        ['Write 2/5 as a percentage.', ['40%', '25%', '20%', '52%'], 'A', '2/5 × 100% = 40%.', 1],
        ['Work out 1.2 × 0.3.', ['3.6', '0.036', '36', '0.36'], 'D', '12 × 3 = 36. There are two decimal places in total, so the answer is 0.36.', 2],
        ['Find 15% of 200.', ['15', '20', '30', '3'], 'C', '15/100 × 200 = 30.', 1],
    ],

    'Ratio, money and rates' => [
        ['Share GH¢60 between Ama and Kofi in the ratio 2 : 3. How much does Kofi get?', ['GH¢24', 'GH¢36', 'GH¢30', 'GH¢40'], 'B', 'There are 2 + 3 = 5 parts, so one part is GH¢60 ÷ 5 = GH¢12. Kofi gets 3 × 12 = GH¢36.', 2],
        ['A trader buys an item for GH¢40 and sells it for GH¢50. Find the profit percent.', ['10%', '20%', '25%', '50%'], 'C', 'Profit = GH¢10. Profit percent = 10/40 × 100% = 25%. Always divide by the cost price.', 3],
        ['Find the simple interest on GH¢500 at 10% per annum for 2 years.', ['GH¢100', 'GH¢50', 'GH¢1,000', 'GH¢600'], 'A', 'I = PRT/100 = 500 × 10 × 2 ÷ 100 = GH¢100.', 2],
        ['A car travels 120 km in 2 hours. What is its average speed?', ['240 km/h', '60 km/h', '122 km/h', '118 km/h'], 'B', 'Speed = distance ÷ time = 120 ÷ 2 = 60 km/h.', 1],
    ],

    'Algebra' => [
        ['Solve x + 9 = 15.', ['24', '6', '−6', '9'], 'B', 'Subtract 9 from both sides: x = 15 − 9 = 6.', 1],
        ['Solve 3x = 21.', ['7', '18', '24', '63'], 'A', 'Divide both sides by 3: x = 21 ÷ 3 = 7.', 1],
        ['Simplify 5a + 3a − 2a.', ['10a', '6a', '8a', '6'], 'B', 'Collect the like terms: 5 + 3 − 2 = 6, so the answer is 6a.', 1],
        ['Expand 3(x + 4).', ['3x + 4', '3x + 7', 'x + 12', '3x + 12'], 'D', 'Multiply each term in the bracket by 3: 3 × x + 3 × 4 = 3x + 12.', 2],
        ['If a = 3 and b = 4, find the value of 2a + b.', ['10', '14', '9', '24'], 'A', '2a + b = 2 × 3 + 4 = 6 + 4 = 10.', 1],
    ],

    'Measurement and geometry' => [
        ['Find the perimeter of a rectangle 8 cm long and 5 cm wide.', ['13 cm', '40 cm', '26 cm', '20 cm'], 'C', 'Perimeter = 2 × (8 + 5) = 2 × 13 = 26 cm.', 1],
        ['Find the area of a rectangle 9 cm long and 4 cm wide.', ['13 cm²', '26 cm²', '32 cm²', '36 cm²'], 'D', 'Area = length × width = 9 × 4 = 36 cm².', 1],
        ['How many centimetres are in 2.5 metres?', ['25', '2,500', '250', '0.25'], 'C', '1 m = 100 cm, so 2.5 m = 2.5 × 100 = 250 cm.', 1],
        ['Two angles on a straight line are 110° and x. Find x.', ['60°', '80°', '70°', '250°'], 'C', 'Angles on a straight line add up to 180°, so x = 180° − 110° = 70°.', 2],
        ['Two angles of a triangle are 50° and 60°. Find the third angle.', ['80°', '70°', '90°', '110°'], 'B', 'The angles of a triangle add up to 180°: 180° − 50° − 60° = 70°.', 2],
        ['A lesson starts at 9:45 a.m. and lasts 1 hour 30 minutes. When does it end?', ['10:75 a.m.', '11:15 a.m.', '10:15 a.m.', '11:45 a.m.'], 'B', '9:45 + 1 hour = 10:45, and 10:45 + 30 minutes = 11:15 a.m.', 2],
    ],

    'Data and chance' => [
        ['Find the mean of 4, 6, 8, 10 and 12.', ['8', '6', '10', '40'], 'A', 'Add them: 4 + 6 + 8 + 10 + 12 = 40. Divide by 5 numbers: 40 ÷ 5 = 8.', 1],
        ['A fair die is thrown once. What is the probability of getting a 6?', ['1/2', '1/3', '1/12', '1/6'], 'D', 'There are 6 equally likely outcomes and only one of them is a 6, so the probability is 1/6.', 1],
    ],

    'Place value and rounding' => [
        ['What is the value of the digit 7 in 47,325?', ['7', '700', '7,000', '70,000'], 'C', 'The 7 is in the thousands place, so it stands for 7,000.', 1],
        ['Round 3,648 to the nearest hundred.', ['3,600', '3,650', '3,700', '4,000'], 'A', 'The tens digit is 4, which is less than 5, so round down to 3,600.', 1],
        ['Write "twenty thousand and fifty" in figures.', ['2,050', '200,050', '20,500', '20,050'], 'D', 'Twenty thousand is 20,000; add fifty to get 20,050.', 1],
        ['Which list is in order from smallest to largest?', ['0.5, 0.45, 0.405', '0.5, 0.405, 0.45', '0.45, 0.5, 0.405', '0.405, 0.45, 0.5'], 'D', 'Compare the tenths first: 0.405 and 0.45 both have 4 tenths, and 0.405 < 0.45 < 0.5.', 2],
        ['Round 6.287 to one decimal place.', ['6.2', '6.29', '6.28', '6.3'], 'D', 'The hundredths digit is 8, so the tenths digit 2 rounds up to 3.', 1],
    ],

    'Order of operations and negative numbers' => [
        ['Work out 8 + 4 × 3.', ['36', '24', '15', '20'], 'D', 'Multiply before adding: 4 × 3 = 12, then 8 + 12 = 20.', 1],
        ['Work out (15 − 5) ÷ 2.', ['5', '12.5', '10', '7.5'], 'A', 'Brackets first: 15 − 5 = 10, then 10 ÷ 2 = 5.', 1],
        ['The temperature is −3 °C at night and rises by 8 °C by noon. What is the temperature at noon?', ['−11 °C', '11 °C', '5 °C', '−5 °C'], 'C', '−3 + 8 = 5 °C.', 2],
        ['Work out −4 × −6.', ['−24', '10', '−10', '24'], 'D', 'A negative number times a negative number gives a positive number.', 2],
    ],

    'Working with fractions' => [
        ['Which fraction is equal to 2/3?', ['4/9', '2/6', '3/2', '6/9'], 'D', 'Multiply top and bottom by 3: 2/3 = 6/9.', 1],
        ['Simplify 12/18.', ['2/3', '6/9', '3/4', '4/6'], 'A', 'Divide top and bottom by 6, the highest common factor.', 1],
        ['Work out 1/2 + 1/3.', ['2/5', '1/6', '5/6', '2/6'], 'C', 'Use sixths: 3/6 + 2/6 = 5/6.', 2],
        ['Work out 3/4 of 20.', ['15', '5', '12', '80'], 'A', '20 ÷ 4 = 5, and 5 × 3 = 15.', 1],
        ['Write 2½ as an improper fraction.', ['5/2', '3/2', '7/2', '5/4'], 'A', '2½ = 4/2 + 1/2 = 5/2.', 1],
    ],

    'Ratio and percentages in daily life' => [
        ['A drink is made from concentrate and water in the ratio 1 : 4. How much water is needed for 200 ml of concentrate?', ['50 ml', '800 ml', '400 ml', '1,000 ml'], 'B', 'Water is 4 times the concentrate: 4 × 200 = 800 ml.', 2],
        ['What is 10% of GH¢350?', ['GH¢3.50', 'GH¢35', 'GH¢340', 'GH¢10'], 'B', 'To find 10%, divide by 10: 350 ÷ 10 = 35.', 1],
        ['A shirt costs GH¢80. Its price goes up by 25%. What is the new price?', ['GH¢100', 'GH¢105', 'GH¢20', 'GH¢85'], 'A', '25% of 80 = 20, so the new price is 80 + 20 = GH¢100.', 2],
        ['Write 3 out of 4 as a percentage.', ['34%', '43%', '75%', '25%'], 'C', '3/4 × 100% = 75%.', 1],
    ],

    'Simple equations and patterns' => [
        ['Find the next number in the pattern 5, 9, 13, 17, …', ['19', '20', '21', '22'], 'C', 'The pattern adds 4 each time: 17 + 4 = 21.', 1],
        ['Solve x − 7 = 12.', ['5', '19', '−5', '84'], 'B', 'Add 7 to both sides: x = 12 + 7 = 19.', 1],
        ['Solve x/4 = 6.', ['24', '1.5', '10', '2'], 'A', 'Multiply both sides by 4: x = 24.', 1],
        ['A pen costs p cedis. Which expression gives the cost of 5 pens?', ['p + 5', '5p', 'p/5', '5 − p'], 'B', 'Five pens cost 5 times the price of one: 5 × p = 5p.', 1],
    ],

    'Shapes, angles and measurement' => [
        ['An angle of 90° is called', ['an acute angle', 'a right angle', 'an obtuse angle', 'a reflex angle'], 'B', 'A right angle is a quarter turn, like the corner of a book.', 1],
        ['An angle of 130° is', ['acute', 'right', 'obtuse', 'reflex'], 'C', 'Obtuse angles are between 90° and 180°.', 1],
        ['Find the area of a square with sides of 7 cm.', ['14 cm²', '28 cm²', '49 cm²', '7 cm²'], 'C', 'Area = side × side = 7 × 7 = 49 cm².', 1],
        ['Find the volume of a box 5 cm long, 4 cm wide and 3 cm high.', ['12 cm³', '60 cm³', '47 cm³', '20 cm³'], 'B', 'Volume = 5 × 4 × 3 = 60 cm³.', 2],
        ['How many grams are in 2.5 kg?', ['25 g', '250 g', '2,500 g', '25,000 g'], 'C', '1 kg = 1,000 g, so 2.5 kg = 2,500 g.', 1],
    ],

    'Collecting and presenting data' => [
        ['A tally shows |||| |||| ||| (two groups of five and three more). What number does it show?', ['10', '13', '3', '15'], 'B', 'Each group of five plus three: 5 + 5 + 3 = 13.', 1],
        ['The ages of five children are 9, 11, 10, 9 and 12. What is the median age?', ['9', '10', '10.2', '11'], 'B', 'In order: 9, 9, 10, 11, 12. The middle value is 10.', 2],
        ['On a bar chart, the height of each bar shows', ['the frequency', 'the colour', 'the year', 'the name of the chart'], 'A', 'The taller the bar, the more often that category occurs.', 1],
    ],

    'Chance and probability' => [
        ['An event that is certain to happen has a probability of', ['0', '1/2', '1', '10'], 'C', 'Probabilities run from 0 (impossible) to 1 (certain).', 1],
        ['A bag holds 3 red and 7 green sweets. One is picked at random. What is the probability that it is red?', ['3/7', '3/10', '7/10', '1/3'], 'B', 'There are 10 sweets and 3 are red, so the probability is 3/10.', 2],
        ['Which word best describes the chance of the sun rising in the east tomorrow?', ['impossible', 'unlikely', 'even chance', 'certain'], 'D', 'The sun always rises in the east.', 1],
    ],
];
