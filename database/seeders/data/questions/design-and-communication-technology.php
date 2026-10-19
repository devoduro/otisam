<?php

/*
| SHS Design and Communication Technology (2023 curriculum elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Design and Communication Technology curriculum.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Pictorial drawing' => [
        ['In isometric drawing, the receding axes are drawn at what angle to the horizontal?', ['45°', '30°', '60°', '90°'], 'B', 'Isometric drawing uses a vertical axis and two axes at 30° to the horizontal.', 1],
        ['In oblique drawing, the receding lines are usually drawn at', ['30°', '15°', '45°', '90°'], 'C', 'The front face is drawn true shape and the depth lines go back at 45°.', 1],
        ['In cabinet oblique drawing, the receding depth is drawn at', ['full size', 'a quarter size', 'twice the size', 'half size'], 'D', 'Halving the depth makes cabinet oblique look less stretched than cavalier oblique, which uses full depth.', 2],
        ['A drawing with two vanishing points on the horizon line is', ['one-point perspective', 'isometric projection', 'two-point perspective', 'oblique projection'], 'C', 'Two-point perspective shows an object from a corner, with two sets of lines meeting at two vanishing points.', 1],
        ['A drawing that shows the parts of an assembly pulled apart but kept in line is', ['a sectional view', 'an exploded view', 'a plan', 'a perspective sketch'], 'B', 'An exploded view shows how the parts fit together and in what order.', 1],
        ['Adding tone, colour and texture to a drawing to make it look realistic is', ['dimensioning', 'rendering', 'projecting', 'scaling'], 'B', 'Rendering makes a drawing look like the real material and form.', 1],
    ],

    'Orthographic projection and conventions' => [
        ['In third-angle projection, the plan is placed', ['above the front view', 'below the front view', 'to the right of the plan', 'behind the end view'], 'A', 'In third-angle projection the plan goes above the front view. In first-angle it goes below.', 2],
        ['Hidden edges in an orthographic drawing are shown with', ['thick continuous lines', 'short dashed lines', 'chain lines (long and short dashes)', 'wavy lines'], 'B', 'Hidden detail is drawn with thin short dashes.', 1],
        ['Centre lines are drawn as', ['thin chain lines (long dash, short dash)', 'thick dashed lines', 'zigzag lines', 'double lines'], 'A', 'A thin chain line marks centres of circles and lines of symmetry.', 1],
        ['In a sectional view, the cut surfaces are shown with', ['thick dashed lines', 'dots', 'solid black fill', 'thin hatching lines at 45°'], 'D', 'Cut surfaces are hatched with evenly spaced thin lines, usually at 45°.', 2],
        ['On engineering drawings in Ghana, dimensions are normally given in', ['metres', 'inches', 'centimetres', 'millimetres'], 'D', 'Following ISO practice, dimensions are in millimetres and the unit is not written after each one.', 1],
        ['A drawing made at a scale of 1 : 5 shows a part 40 mm long. What is its real length?', ['8 mm', '45 mm', '200 mm', '400 mm'], 'C', 'At 1 : 5 the real object is 5 times bigger: 40 × 5 = 200 mm.', 2],
        ['A table 600 mm long is drawn at a scale of 1 : 10. How long is it on the drawing?', ['6 mm', '60 mm', '600 mm', '6,000 mm'], 'B', 'Divide by 10: 600 ÷ 10 = 60 mm.', 2],
    ],

    'Geometry' => [
        ['A line that touches a circle at only one point is a', ['chord', 'diameter', 'tangent', 'secant'], 'C', 'A tangent touches the circle once and is at right angles to the radius at that point.', 1],
        ['Each interior angle of a regular hexagon is', ['60°', '120°', '108°', '135°'], 'B', 'Interior angles of a hexagon add up to (6 − 2) × 180° = 720°, so each is 720° ÷ 6 = 120°.', 2],
        ['The golden ratio is approximately', ['1.618', '3.142', '2.718', '1.414'], 'A', 'The golden ratio is about 1.618. 3.142 is π, 2.718 is e and 1.414 is √2.', 2],
        ['A rectangular sign measures 1.2 m by 0.5 m. What area of acrylic sheet is needed?', ['1.7 m²', '0.6 m²', '6 m²', '0.06 m²'], 'B', 'Area = 1.2 × 0.5 = 0.6 m².', 2],
    ],

    'Design process and human factors' => [
        ['The study of designing products to suit the people who use them is', ['aesthetics', 'ergonomics', 'economics', 'tessellation'], 'B', 'Ergonomics looks at comfort, safety and ease of use.', 1],
        ['Measurements of the human body used in design are called', ['biometrics', 'geometry', 'anthropometrics', 'statistics'], 'C', 'Anthropometric data, such as hand size or seat height, help products fit their users.', 2],
        ['In design, aesthetics refers to', ['the cost of a product', 'the strength of its materials', 'how a product looks and appeals to the senses', 'the speed of manufacture'], 'C', 'Aesthetics covers form, colour, texture and the overall appeal of a product.', 1],
        ['A list of requirements that a design solution must meet is a', ['portfolio', 'prototype', 'specification', 'sketch'], 'C', 'The specification sets measurable targets against which the final design is checked.', 1],
        ['A working model built to test a design before full production is a', ['prototype', 'blueprint', 'template', 'logo'], 'A', 'A prototype lets the designer test function, fit and appearance before making many.', 1],
        ['During product analysis, the question "Who is this product for?" is about the', ['target user (market)', 'manufacturing cost only', 'colour of the product only', 'weight of the materials'], 'A', 'Knowing the target user shapes size, style, price and features.', 1],
        ['Testing a finished product against its specification is called', ['evaluation', 'ideation', 'research', 'marketing'], 'A', 'Evaluation checks how well the product meets each point of the specification and suggests improvements.', 1],
        ['Which of the R words means using a product again, for the same or another purpose?', ['reduce', 'reuse', 'recycle', 'refuse'], 'B', 'Reuse keeps the item in use. Recycling breaks it down into raw material for something new.', 1],
    ],

    'Materials and technology' => [
        ['A plastic that can be reheated and reshaped many times is a', ['thermosetting plastic', 'thermoplastic', 'composite', 'ceramic'], 'B', 'Thermoplastics soften when heated and harden when cooled, again and again.', 1],
        ['Which of these is a thermosetting plastic?', ['polyethylene', 'PVC', 'melamine formaldehyde', 'acrylic'], 'C', 'Melamine sets permanently when first heated and cannot be reshaped. The others are thermoplastics.', 3],
        ['MDF (medium-density fibreboard) is an example of', ['a hardwood', 'a manufactured board', 'a softwood', 'a metal alloy'], 'B', 'MDF is made from wood fibres glued and pressed into sheets.', 1],
        ['CAD stands for', ['Computer Automated Drilling', 'Central Art Department', 'Coded Assembly Drawing', 'Computer-Aided Design'], 'D', 'CAD software is used to draw and model designs on a computer.', 1],
        ['Which of these is an example of CAM equipment?', ['laser cutter', 'pencil', 'T-square', 'tracing paper'], 'A', 'CAM (computer-aided manufacture) machines such as laser cutters and CNC routers make parts directly from CAD files.', 1],
    ],

    'Graphic communication' => [
        ['A typeface with small strokes at the ends of its letters is', ['a sans-serif typeface', 'a monospace stencil', 'a serif typeface', 'a dingbat'], 'C', 'Serifs are the small finishing strokes. Sans-serif means "without serifs".', 2],
        ['A symbol designed to identify a company or brand is a', ['watermark', 'margin', 'grid', 'logo'], 'D', 'A logo gives a business a recognisable visual identity.', 1],
    ],

    'Conceptual drawing' => [
        ['Small, quick sketches used to explore many design ideas are', ['working drawings', 'thumbnail sketches', 'assembly drawings', 'sectional views'], 'B', 'They help a designer try out ideas fast.', 1],
        ['Notes written beside design sketches to explain ideas are', ['hatching', 'dimensions', 'annotations', 'leaders only'], 'C', 'Annotations explain materials, functions and reasons for choices.', 1],
        ['Light construction lines in a sketch are called', ['centre lines', 'object lines', 'hidden lines', 'crating (guide lines)'], 'D', 'Drawing a box (crate) first helps get proportions right.', 2],
    ],

    'SHS 2: Geometry' => [
        ['When a regular hexagon is drawn inside a circle, each side equals', ['the diameter', 'the radius of the circle', 'half the radius', 'the circumference'], 'B', 'A regular hexagon is made of six equilateral triangles.', 2],
        ['Each interior angle of a regular pentagon is', ['120°', '72°', '108°', '90°'], 'C', 'Sum of interior angles = 540°, and 540 ÷ 5 = 108°.', 2],
        ['The curve traced by a point on a string as it is unwound from a circle is', ['a parabola', 'a cycloid', 'an ellipse', 'an involute'], 'D', 'A cycloid is traced by a point on a circle rolling along a line.', 3],
        ['An ellipse has two axes, called the', ['x and z axes', 'major and minor axes', 'centre lines only', 'tangent and normal'], 'B', 'It can be drawn by the trammel or concentric-circles method.', 2],
        ['The path traced by a point on the rim of a wheel rolling along a straight line is', ['a helix', 'an involute', 'a cycloid', 'a spiral'], 'C', 'It is a classic loci construction.', 3],
    ],

    'Extended drawing' => [
        ['An auxiliary view is drawn mainly to show', ['the price', 'hidden detail', 'the colour of the object', 'the true shape of an inclined surface'], 'D', 'It is projected at right angles to the inclined surface.', 3],
        ['The flat pattern that folds up to make a cylinder or box is its', ['section', 'development (net)', 'elevation', 'perspective'], 'B', 'Developments are used to cut sheet material.', 2],
        ['What length of sheet is needed to roll a cylinder 70 mm in diameter? (Take π = 22/7.)', ['110 mm', '140 mm', '220 mm', '440 mm'], 'C', 'The length equals the circumference: πd = 22/7 × 70 = 220 mm.', 2],
        ['In isometric drawing, circles are drawn as', ['ellipses', 'true circles', 'squares', 'straight lines'], 'A', 'The four-centre method gives a good approximation.', 2],
    ],

    'SHS 3: Graphic communication' => [
        ['The space between lines of text is called', ['leading', 'kerning', 'tracking', 'margin'], 'A', 'Kerning adjusts the space between individual pairs of letters.', 2],
        ['A layout grid helps a designer to', ['arrange text and images consistently on a page', 'choose colours', 'print in 3D', 'measure angles'], 'A', 'Grids give pages order and balance.', 1],
        ['Signs that use simple pictures understood without words, such as a toilet sign, are', ['pictograms', 'logos', 'graphs', 'captions'], 'A', 'They work across languages.', 1],
    ],
];
