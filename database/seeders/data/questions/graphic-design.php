<?php

/*
| SHS Graphic Design: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Design basics' => [
        ['Graphic design is mainly concerned with', ['visual communication', 'building houses', 'sewing clothes', 'cooking'], 'A', 'Graphic design communicates messages through images and text.', 1],
        ['A symbol that identifies a company or organisation is a', ['logo', 'poster', 'banner', 'leaflet'], 'A', 'Logos give a brand a visual identity.', 1],
        ['A logo made only of letters, usually initials, is a', ['monogram', 'pictogram', 'mascot', 'emblem'], 'A', 'Monograms combine letters into a mark.', 2],
        ['A simple picture used to represent an object or idea, such as on toilet doors, is a', ['pictogram', 'typeface', 'serif', 'collage'], 'A', 'Pictograms communicate across languages.', 1],
        ['The first rough sketches of design ideas are', ['thumbnails', 'final artwork', 'proofs', 'plates'], 'A', 'Thumbnails are small quick sketches.', 1],
        ['A more detailed drawing showing how the final design will look is a', ['comprehensive (comp) or layout', 'thumbnail', 'proof', 'negative'], 'A', 'Comps are prepared for client approval.', 2],
        ['The arrangement of text and images on a page is the', ['layout', 'typeface', 'register', 'bleed'], 'A', 'Layout organises elements for clarity and appeal.', 1],
    ],

    'Typography' => [
        ['The small strokes at the ends of letters in fonts such as Times New Roman are', ['serifs', 'ascenders', 'descenders', 'kerns'], 'A', 'Serif fonts have small finishing strokes.', 1],
        ['A typeface without serifs, such as Arial, is described as', ['script', 'sans serif', 'italic', 'gothic only'], 'B', '"Sans" means without.', 1],
        ['The part of a lowercase letter that rises above the x-height, as in "b" and "d", is the', ['counter', 'descender', 'ascender', 'baseline'], 'C', 'Ascenders extend above the x-height.', 2],
        ['The part of a lowercase letter that drops below the baseline, as in "g" and "p", is the', ['stem', 'ascender', 'serif', 'descender'], 'D', 'Descenders go below the baseline.', 2],
        ['Adjusting the space between two particular letters is', ['leading', 'kerning', 'tracking', 'justification'], 'B', 'Kerning adjusts specific letter pairs.', 3],
        ['The vertical space between lines of text is', ['tracking', 'kerning', 'leading', 'gutter'], 'C', 'Leading comes from the strips of lead once used in metal type.', 3],
        ['Text that is aligned evenly on both left and right margins is', ['flush left only', 'centred', 'ragged right', 'justified'], 'D', 'Justified text has straight edges on both sides.', 2],
        ['Type size is usually measured in', ['inches', 'points', 'pixels only', 'centimetres'], 'B', 'One point is about 1/72 inch.', 2],
    ],

    'Printing processes' => [
        ['The printing process based on the principle that oil and water do not mix is', ['screen printing', 'letterpress', 'lithography (offset)', 'gravure'], 'C', 'Offset lithography uses this principle.', 2],
        ['Printing by forcing ink through a mesh with a stencil is', ['flexography', 'offset printing', 'letterpress', 'screen printing'], 'D', 'Screen printing is common for T-shirts and banners.', 1],
        ['Printing from a raised surface is', ['intaglio', 'relief printing (letterpress)', 'planography', 'stencil printing'], 'B', 'Relief printing uses raised areas, as in linocut and woodcut.', 2],
        ['Printing from an image cut into the surface of a plate is', ['planographic', 'relief', 'intaglio', 'stencil'], 'C', 'In intaglio, ink is held in the grooves.', 3],
        ['The four process colours used in full-colour printing are', ['black, white, grey and brown', 'red, green, blue and white', 'red, yellow, blue and green', 'cyan, magenta, yellow and black (CMYK)'], 'D', 'CMYK inks combine to print full-colour images.', 2],
        ['The colours used on computer and TV screens are', ['CMYK', 'RGB (red, green, blue)', 'RYB', 'black and white only'], 'B', 'Screens mix red, green and blue light.', 2],
        ['Extending an image beyond the trim edge so no white edge remains after cutting is called', ['margin', 'register', 'bleed', 'gutter'], 'C', 'Bleed allows for small cutting errors.', 3],
        ['The correct alignment of different colour plates in printing is', ['trapping', 'imposition', 'bleed', 'registration'], 'D', 'Poor registration gives blurred, misaligned colours.', 3],
    ],

    'Digital design and advertising' => [
        ['Images made of pixels, such as photographs, are', ['vector images', 'raster (bitmap) images', 'fonts', 'outlines'], 'B', 'Raster images lose quality when enlarged too much.', 2],
        ['Images made of mathematical paths that can be enlarged without losing quality are', ['JPEG photos', 'raster images', 'vector images', 'scanned images'], 'C', 'Vectors suit logos and illustrations.', 2],
        ['Which software is mainly used for editing photographs?', ['Notepad', 'Microsoft Excel', 'Adobe Acrobat Reader', 'Adobe Photoshop'], 'D', 'Photoshop is a raster image editor.', 1],
        ['Which software is mainly used for creating vector illustrations and logos?', ['Microsoft Word', 'Adobe Illustrator or CorelDRAW', 'Audacity', 'Excel'], 'B', 'Illustrator and CorelDRAW are vector programs.', 1],
        ['The resolution normally recommended for printed images is', ['72 dpi', '150 dpi', '300 dpi', '1,000 dpi'], 'C', '300 dpi gives sharp results for most print work.', 3],
        ['A large printed notice displayed in public to advertise an event is a', ['label', 'letterhead', 'business card', 'poster'], 'D', 'Posters attract attention from a distance.', 1],
        ['A short memorable phrase used in advertising is a', ['logo', 'slogan', 'caption', 'headline only'], 'B', 'Slogans help people remember a brand.', 1],
        ['The design of the outer covering of a product to protect and promote it is', ['textile design', 'interior design', 'packaging design', 'fashion design'], 'C', 'Packaging protects the product and attracts buyers.', 1],
    ],
];
