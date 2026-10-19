<?php

/*
| SHS Technical Drawing: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Instruments and conventions
    ['The instrument used to draw horizontal lines on a drawing board is the', ['set square', 'T-square', 'protractor', 'compass'], 'B', 'The T-square slides along the edge of the board to give parallel horizontal lines.', 1],
    ['Which set square angles are standard?', ['30°/60° and 45°', '20°/70° and 50°', '15°/75° only', '35°/55° and 40°'], 'A', 'Standard set squares are 30°–60°–90° and 45°–45°–90°.', 1],
    ['An instrument used to draw irregular curves is the', ['French curve', 'divider', 'T-square', 'scale rule'], 'A', 'French curves guide smooth irregular curves.', 2],
    ['Dividers are used mainly to', ['draw circles', 'transfer and step off measurements', 'draw horizontal lines', 'measure angles'], 'B', 'Dividers have two pointed legs for transferring distances.', 2],
    ['The size of A4 paper is', ['210 mm × 297 mm', '297 mm × 420 mm', '148 mm × 210 mm', '420 mm × 594 mm'], 'A', 'A4 is 210 × 297 mm; A3 is 297 × 420 mm.', 2],
    ['A scale of 1 : 2 on a drawing means the drawing is', ['twice the actual size', 'half the actual size', 'the actual size', 'one-twentieth of the actual size'], 'B', 'Every 1 unit on paper represents 2 units on the object.', 1],
    ['A scale of 2 : 1 is an', ['enlargement scale', 'reduction scale', 'full-size scale', 'isometric scale'], 'A', 'The drawing is twice the size of the object.', 2],
    ['Thick continuous lines on an engineering drawing represent', ['hidden edges', 'visible outlines', 'centre lines', 'dimension lines'], 'B', 'Visible edges and outlines use thick continuous lines.', 1],
    ['Thin continuous lines are used for', ['visible outlines', 'dimension and projection lines', 'hidden edges', 'cutting planes only'], 'B', 'Dimension, projection, leader and hatching lines are thin continuous.', 2],
    ['Section lines (hatching) are usually drawn at', ['30°', '45°', '60°', '90°'], 'B', 'Hatching is normally at 45° to the main outline.', 1],

    // Geometrical constructions
    ['To bisect a line means to', ['divide it into two equal parts', 'draw it twice', 'extend it', 'draw a parallel line'], 'A', 'Bisecting finds the midpoint, usually with compass arcs.', 1],
    ['A polygon with eight sides is a', ['hexagon', 'heptagon', 'octagon', 'nonagon'], 'C', 'Octagon = eight sides.', 1],
    ['Each interior angle of a regular hexagon is', ['108°', '120°', '135°', '60°'], 'B', '(6 − 2) × 180 ÷ 6 = 120°.', 2],
    ['A line that touches a circle at only one point is a', ['chord', 'tangent', 'secant', 'diameter'], 'B', 'A tangent meets the circle at one point and is perpendicular to the radius there.', 1],
    ['The curve traced by a point on the circumference of a circle rolling along a straight line is a', ['cycloid', 'involute', 'ellipse', 'helix'], 'A', 'A cycloid is the locus of a point on a rolling circle.', 3],
    ['The curve traced by the end of a taut string unwinding from a circle is an', ['involute', 'ellipse', 'parabola', 'cycloid'], 'A', 'Involutes are used for gear tooth profiles.', 3],
    ['A section cut through a cone parallel to its base gives a', ['circle', 'ellipse', 'parabola', 'hyperbola'], 'A', 'A cut parallel to the base gives a circle.', 2],
    ['A section cut through a cone parallel to its slant side gives a', ['circle', 'ellipse', 'parabola', 'hyperbola'], 'C', 'Cutting parallel to the generator gives a parabola.', 3],

    // Projection
    ['In first angle orthographic projection, the plan is drawn', ['above the front elevation', 'on the same line as the front elevation', 'to the left of the front elevation', 'below the front elevation'], 'D', 'In first angle projection the plan goes below the front view.', 3],
    ['In third angle orthographic projection, the plan is drawn', ['inside the front elevation', 'below the front elevation', 'to the right only', 'above the front elevation'], 'D', 'In third angle projection the plan goes above the front view.', 3],
    ['In first angle projection, the end view seen from the left is drawn on the', ['left of the front elevation', 'top of the plan', 'right of the front elevation', 'bottom of the plan'], 'C', 'In first angle projection a view is placed on the side opposite to where it is seen from.', 4],
    ['The receding lines in isometric drawing are drawn at', ['90° to the horizontal', '45° to the horizontal', '60° to the horizontal', '30° to the horizontal'], 'D', 'Isometric axes are vertical and at 30° on each side.', 1],
    ['In cabinet oblique drawing, the receding lines are drawn at 45° and', ['full length', 'twice their length', 'half their true length', 'one-third their length'], 'C', 'Cabinet oblique halves depths to reduce distortion.', 3],
    ['A drawing showing the outer surfaces of a solid unfolded onto a flat sheet is a', ['perspective', 'section', 'elevation', 'development'], 'D', 'Developments are used to make boxes, ducts and containers from sheet material.', 2],
    ['The development of the curved surface of a cylinder is a', ['circle', 'triangle', 'rectangle', 'sector'], 'C', 'Unrolled, the curved surface is a rectangle of length πd and width h.', 2],
    ['The development of the curved surface of a right cone is a', ['rectangle', 'ellipse', 'triangle', 'sector of a circle'], 'D', 'The slant height becomes the radius of a sector.', 3],
    ['A drawing in which parallel lines appear to meet at vanishing points is a', ['orthographic projection', 'isometric drawing', 'perspective drawing', 'oblique drawing'], 'C', 'Perspective imitates how the eye sees depth.', 2],

    // Building and engineering drawing conventions
    ['On building drawings, the symbol for a door shows a', ['triangle', 'straight double line only', 'cross', 'quarter-circle swing arc'], 'D', 'The arc shows the direction the door opens.', 2],
    ['A drawing showing the layout of rooms as seen from above, cut at about 1 m above floor level, is a', ['site plan', 'elevation', 'floor plan', 'section'], 'C', 'A floor plan is a horizontal section through the building.', 2],
    ['In engineering drawing, the abbreviation "PCD" stands for', ['parallel cut depth', 'perpendicular centre distance', 'projected circle drawing', 'pitch circle diameter'], 'D', 'PCD is the diameter of the circle through hole centres.', 3],
    ['The conventional representation of an external screw thread shows the root diameter with', ['dashed lines only', 'thick lines', 'thin lines', 'chain lines'], 'C', 'The crest uses thick lines and the root thin lines.', 4],
];
