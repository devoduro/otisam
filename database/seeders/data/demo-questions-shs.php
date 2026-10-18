<?php

/*
| Demo practice questions in the style of WAEC WASSCE objective papers.
|
| Written for Otisam. These are NOT copied from WAEC papers or other websites: WAEC owns its past
| questions, which belong in the library as licensed or free-to-share documents only.
|
| subject slug => list of [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    'core-mathematics' => [
        ['Simplify 2⅓ − 1¾.', ['⅞', '7/12', '5/12', '1⅞'], 'B', '2⅓ = 28/12 and 1¾ = 21/12, so the difference is 7/12.', 2],
        ['Solve for x: 3x − 7 = 2x + 5.', ['−12', '−2', '2', '12'], 'D', 'Subtract 2x from both sides: x − 7 = 5, so x = 12.', 1],
        ['Find the value of log₂ 32.', ['4', '5', '6', '16'], 'B', '2⁵ = 32, so log₂ 32 = 5.', 2],
        ['A trader bought an item for GH₵ 80.00 and sold it for GH₵ 100.00. Find the percentage profit.', ['20%', '25%', '80%', '125%'], 'B', 'Profit = GH₵ 20.00. Percentage profit = 20/80 × 100% = 25%.', 2],
        ['The angles of a triangle are x°, 2x° and 3x°. Find the largest angle.', ['30°', '60°', '90°', '120°'], 'C', 'x + 2x + 3x = 180, so 6x = 180 and x = 30. The largest angle is 3x = 90°.', 2],
        ['Find the mean of 4, 7, 9, 10 and 15.', ['7', '9', '10', '11'], 'B', 'Sum = 45 and there are 5 numbers, so the mean is 45 ÷ 5 = 9.', 1],
        ['Factorise completely: x² − 9.', ['(x − 3)²', '(x + 3)²', '(x − 3)(x + 3)', 'x(x − 9)'], 'C', 'x² − 9 is a difference of two squares: x² − 3² = (x − 3)(x + 3).', 2],
        ['Express 0.000452 in standard form.', ['4.52 × 10⁻³', '4.52 × 10⁻⁴', '45.2 × 10⁻⁵', '4.52 × 10⁴'], 'B', 'Move the decimal point 4 places to the right to get 4.52, so the power is 10⁻⁴.', 2],
    ],

    'english-language' => [
        ['Choose the option nearest in meaning to the word in capitals: The headmaster was FURIOUS about the late arrivals.', ['pleased', 'very angry', 'surprised', 'worried'], 'B', '"Furious" means extremely angry.', 1],
        ['Choose the option opposite in meaning to the word in capitals: The new teacher is very GENEROUS.', ['kind', 'wealthy', 'mean', 'humble'], 'C', 'The opposite of generous (willing to give) is mean (unwilling to give or share).', 1],
        ['Choose the option that best completes the sentence: Neither Kofi nor his sisters ___ at home yesterday.', ['was', 'were', 'is', 'has been'], 'B', 'With "neither … nor", the verb agrees with the nearer subject, "sisters", which is plural: "were".', 3],
        ['Choose the option that best completes the sentence: She has been living in Kumasi ___ 2019.', ['for', 'since', 'from', 'by'], 'B', '"Since" is used with a point in time; "for" is used with a length of time.', 2],
        ['Choose the correctly spelt word.', ['accomodation', 'accommodation', 'acommodation', 'accommadation'], 'B', 'Accommodation has a double "c" and a double "m".', 2],
        ['Choose the option that best explains the expression in capitals: Ama decided to BURY THE HATCHET with her friend.', ['hide a weapon', 'end a quarrel', 'start a farm', 'keep a secret'], 'B', 'To "bury the hatchet" means to make peace and stop quarrelling.', 2],
    ],

    'integrated-science' => [
        ['Which of the following is the basic unit of life?', ['tissue', 'organ', 'cell', 'system'], 'C', 'The cell is the smallest structural and functional unit of living things.', 1],
        ['The SI unit of force is the', ['joule', 'newton', 'watt', 'pascal'], 'B', 'Force is measured in newtons (N). The joule is the unit of energy, the watt of power and the pascal of pressure.', 1],
        ['Which gas is given off during photosynthesis?', ['carbon dioxide', 'nitrogen', 'oxygen', 'hydrogen'], 'C', 'Plants take in carbon dioxide and water, and release oxygen as a by-product.', 1],
        ['A solution with a pH of 3 is', ['strongly alkaline', 'neutral', 'weakly alkaline', 'acidic'], 'D', 'pH values below 7 are acidic; 7 is neutral; above 7 is alkaline.', 2],
        ['Malaria is transmitted by the bite of an infected female', ['Aedes mosquito', 'Anopheles mosquito', 'Culex mosquito', 'tsetse fly'], 'B', 'The female Anopheles mosquito carries the Plasmodium parasite that causes malaria.', 2],
        ['Which of the following is a renewable source of energy?', ['crude oil', 'coal', 'solar energy', 'natural gas'], 'C', 'Solar energy is replaced naturally and will not run out; fossil fuels are non-renewable.', 1],
    ],

    'social-studies' => [
        ['Ghana attained independence on', ['1st July, 1960', '6th March, 1957', '24th February, 1966', '7th January, 1993'], 'B', 'Ghana became independent from Britain on 6th March, 1957.', 1],
        ['The arm of government responsible for interpreting the law is the', ['Executive', 'Legislature', 'Judiciary', 'Electoral Commission'], 'C', 'The Judiciary interprets the law; the Legislature makes it and the Executive implements it.', 1],
        ['Which of the following is a major cause of land degradation in Ghana?', ['afforestation', 'illegal mining (galamsey)', 'crop rotation', 'contour ploughing'], 'B', 'Illegal mining destroys vegetation and topsoil and pollutes water bodies. The other options conserve land.', 2],
        ['A responsibility of every Ghanaian citizen is to', ['pay taxes', 'vote in every election abroad', 'own land', 'join a political party'], 'A', 'Paying taxes is a civic responsibility that funds public services.', 1],
        ['The process by which a young person learns the values and norms of society is called', ['adolescence', 'socialisation', 'urbanisation', 'migration'], 'B', 'Socialisation is learning the accepted ways of behaving in a society.', 2],
        ['Which of the following is NOT a function of the family?', ['procreation', 'socialisation of children', 'making national laws', 'provision of basic needs'], 'C', 'Making national laws is a function of Parliament, not the family.', 1],
    ],

    'physics' => [
        ['A car travels 120 km in 2 hours. What is its average speed?', ['30 km/h', '60 km/h', '120 km/h', '240 km/h'], 'B', 'Average speed = distance ÷ time = 120 ÷ 2 = 60 km/h.', 1],
        ['A body of mass 5 kg accelerates at 2 m/s². Find the resultant force on it.', ['2.5 N', '7 N', '10 N', '20 N'], 'C', 'F = ma = 5 × 2 = 10 N.', 1],
        ['Which of the following is a vector quantity?', ['mass', 'speed', 'displacement', 'temperature'], 'C', 'Displacement has both magnitude and direction. The others have magnitude only.', 2],
        ['A resistor of 6 Ω is connected to a 12 V battery. What current flows?', ['0.5 A', '2 A', '6 A', '72 A'], 'B', 'By Ohm\'s law, I = V/R = 12/6 = 2 A.', 2],
        ['The image formed by a plane mirror is', ['real and inverted', 'virtual and upright', 'real and magnified', 'virtual and diminished'], 'B', 'A plane mirror forms a virtual, upright image of the same size as the object, laterally inverted.', 2],
        ['Calculate the kinetic energy of a 2 kg ball moving at 3 m/s.', ['3 J', '6 J', '9 J', '18 J'], 'C', 'KE = ½mv² = ½ × 2 × 3² = 9 J.', 2],
    ],

    'chemistry' => [
        ['The atomic number of an element is the number of', ['neutrons in its nucleus', 'protons in its nucleus', 'protons and neutrons', 'electron shells'], 'B', 'Atomic number = number of protons in the nucleus.', 1],
        ['Which of the following is a noble gas?', ['nitrogen', 'chlorine', 'argon', 'oxygen'], 'C', 'Argon is in Group 18 (the noble gases), with a full outer shell.', 1],
        ['What is the relative molecular mass of H₂O? (H = 1, O = 16)', ['17', '18', '32', '34'], 'B', '2 × 1 + 16 = 18.', 1],
        ['The bond formed when electrons are transferred from one atom to another is', ['covalent', 'ionic', 'metallic', 'hydrogen'], 'B', 'Electron transfer produces oppositely charged ions held together by an ionic (electrovalent) bond.', 2],
        ['Which of these is an alkane?', ['C₂H₄', 'C₂H₂', 'C₃H₈', 'C₆H₆'], 'C', 'Alkanes have the general formula CₙH₂ₙ₊₂. For n = 3: C₃H₈ (propane).', 3],
        ['How many moles are in 11 g of carbon dioxide? (C = 12, O = 16)', ['0.25', '0.5', '2', '4'], 'A', 'Molar mass of CO₂ = 44 g/mol, so 11 ÷ 44 = 0.25 mol.', 3],
    ],

    'biology' => [
        ['Which organelle is the site of aerobic respiration?', ['nucleus', 'ribosome', 'mitochondrion', 'chloroplast'], 'C', 'Mitochondria release energy from food by aerobic respiration.', 1],
        ['The functional unit of the kidney is the', ['neuron', 'nephron', 'alveolus', 'villus'], 'B', 'Each kidney contains about a million nephrons, which filter blood and form urine.', 2],
        ['Which blood vessel carries blood away from the heart?', ['vein', 'venule', 'artery', 'capillary'], 'C', 'Arteries carry blood away from the heart; veins return it.', 1],
        ['In a monohybrid cross between two heterozygous tall plants (Tt × Tt), the ratio of tall to short offspring is', ['1 : 1', '2 : 1', '3 : 1', '4 : 0'], 'C', 'Offspring are TT, Tt, Tt and tt: three tall to one short.', 3],
        ['An organism that makes its own food from simple substances is a', ['consumer', 'decomposer', 'producer', 'parasite'], 'C', 'Producers, such as green plants, are autotrophs: they make food by photosynthesis.', 1],
        ['Which of the following is a function of the xylem?', ['transport of food', 'transport of water and mineral salts', 'gaseous exchange', 'support of flowers only'], 'B', 'Xylem carries water and mineral salts from the roots upward; phloem carries food.', 2],
    ],

    'elective-mathematics' => [
        ['Differentiate y = 3x² + 5x − 7 with respect to x.', ['3x + 5', '6x + 5', '6x − 7', '3x² + 5'], 'B', 'd/dx(3x²) = 6x, d/dx(5x) = 5 and the derivative of a constant is 0.', 2],
        ['Evaluate the limit of (x² − 4)/(x − 2) as x approaches 2.', ['0', '2', '4', 'undefined'], 'C', '(x² − 4)/(x − 2) = x + 2 for x ≠ 2, so the limit is 2 + 2 = 4.', 3],
        ['Find the determinant of the matrix [[3, 2], [1, 4]].', ['5', '10', '12', '14'], 'B', '|A| = (3 × 4) − (2 × 1) = 12 − 2 = 10.', 2],
        ['In how many ways can 3 books be arranged on a shelf?', ['3', '6', '9', '27'], 'B', '3! = 3 × 2 × 1 = 6.', 1],
        ['Find ∫ 4x³ dx.', ['12x² + c', 'x⁴ + c', '4x⁴ + c', 'x³ + c'], 'B', '∫ 4x³ dx = 4 × x⁴/4 + c = x⁴ + c.', 2],
        ['If f(x) = 2x + 1 and g(x) = x², find f(g(3)).', ['19', '49', '18', '7'], 'A', 'g(3) = 9, then f(9) = 2 × 9 + 1 = 19.', 3],
    ],

    'economics' => [
        ['The basic economic problem arises because of', ['unlimited resources', 'scarcity of resources', 'government policy', 'high prices'], 'B', 'Wants are unlimited but resources are scarce, so choices must be made.', 1],
        ['Opportunity cost is best defined as', ['the money cost of a good', 'the alternative forgone', 'the cost of production', 'the price of a substitute'], 'B', 'It is the next best alternative given up when a choice is made.', 1],
        ['When the price of a normal good falls, other things being equal, quantity demanded will', ['fall', 'rise', 'remain constant', 'become zero'], 'B', 'This is the law of demand: price and quantity demanded move in opposite directions.', 1],
        ['Which of the following is a factor of production?', ['money', 'land', 'profit', 'interest'], 'B', 'The factors are land, labour, capital and entrepreneurship. Profit and interest are rewards to factors.', 2],
        ['A persistent general rise in the price level is known as', ['deflation', 'inflation', 'devaluation', 'recession'], 'B', 'Inflation is a sustained increase in the general level of prices.', 1],
        ['The central bank of Ghana is the', ['Ghana Commercial Bank', 'Bank of Ghana', 'National Investment Bank', 'Agricultural Development Bank'], 'B', 'The Bank of Ghana issues currency and manages monetary policy.', 1],
    ],

    'government' => [
        ['A system of government in which power is shared between a central government and regional units is', ['unitary', 'federal', 'monarchical', 'totalitarian'], 'B', 'Federalism constitutionally divides power between the centre and the component units.', 2],
        ['The rule of law implies that', ['the president is above the law', 'everyone is equal before the law', 'judges make the law', 'only citizens obey the law'], 'B', 'Under the rule of law no one is above the law and all are treated equally by it.', 1],
        ['Ghana\'s 1992 Constitution established the', ['First Republic', 'Second Republic', 'Third Republic', 'Fourth Republic'], 'D', 'The 1992 Constitution ushered in the Fourth Republic on 7th January, 1993.', 2],
        ['Which of the following is a feature of a presidential system?', ['collective responsibility', 'fusion of powers', 'separation of powers', 'a ceremonial head of state'], 'C', 'Presidential systems separate the executive from the legislature.', 2],
        ['Franchise means the', ['right to vote', 'right to form a party', 'freedom of speech', 'right to own property'], 'A', 'Franchise (suffrage) is the right to vote in elections.', 1],
        ['A pressure group differs from a political party because a pressure group', ['contests elections', 'seeks to form government', 'seeks to influence government policy', 'has a manifesto for the whole nation'], 'C', 'Pressure groups influence policy without seeking to control government.', 2],
    ],

    'geography' => [
        ['The imaginary line that divides the earth into the northern and southern hemispheres is the', ['Greenwich Meridian', 'Equator', 'Tropic of Cancer', 'International Date Line'], 'B', 'The Equator is at 0° latitude.', 1],
        ['How many degrees of longitude make a one-hour difference in time?', ['1°', '10°', '15°', '24°'], 'C', '360° ÷ 24 hours = 15° per hour.', 2],
        ['The largest man-made lake in Ghana is Lake', ['Bosumtwi', 'Volta', 'Kpong', 'Tamale'], 'B', 'Lake Volta was formed by the Akosombo Dam. Bosumtwi is a natural crater lake.', 1],
        ['Which type of rock is formed from cooled molten magma?', ['sedimentary', 'metamorphic', 'igneous', 'organic'], 'C', 'Igneous rocks form when magma or lava cools and solidifies.', 1],
        ['The dry, dusty wind that blows from the Sahara into Ghana between November and March is the', ['monsoon', 'harmattan', 'trade wind', 'sea breeze'], 'B', 'The harmattan is the north-east trade wind from the Sahara.', 1],
        ['On a map of scale 1 : 50 000, a distance of 4 cm represents', ['0.2 km', '2 km', '20 km', '200 km'], 'B', '4 × 50 000 cm = 200 000 cm = 2 km.', 3],
    ],

    'financial-accounting' => [
        ['The accounting equation is', ['Assets = Capital − Liabilities', 'Assets = Capital + Liabilities', 'Capital = Assets + Liabilities', 'Liabilities = Assets + Capital'], 'B', 'Assets are financed by owners (capital) and outsiders (liabilities).', 1],
        ['Which of the following is a current asset?', ['motor vehicle', 'inventory', 'building', 'machinery'], 'B', 'Inventory is expected to be turned into cash within a year.', 1],
        ['A trial balance is prepared to', ['find the profit', 'check the arithmetical accuracy of the ledger', 'show the financial position', 'record cash sales'], 'B', 'It checks that total debits equal total credits.', 2],
        ['Goods costing GH₵ 400 were sold for GH₵ 500. The gross profit is', ['GH₵ 100', 'GH₵ 400', 'GH₵ 500', 'GH₵ 900'], 'A', 'Gross profit = sales − cost of sales = 500 − 400 = GH₵ 100.', 1],
        ['Depreciation is charged on', ['current assets', 'non-current assets', 'liabilities', 'capital'], 'B', 'Depreciation spreads the cost of non-current assets over their useful life.', 2],
        ['The book of original entry for credit purchases is the', ['sales journal', 'purchases journal', 'cash book', 'general journal'], 'B', 'Credit purchases of goods for resale are first recorded in the purchases journal (day book).', 2],
    ],

    'christian-religious-studies' => [
        ['God called Abram to leave his country and go to a land He would show him. Abram was then living in', ['Egypt', 'Haran', 'Canaan', 'Babylon'], 'B', 'Genesis 12:1–4: Abram left Haran at the age of seventy-five.', 3],
        ['Who led the Israelites across the River Jordan into the Promised Land?', ['Moses', 'Aaron', 'Joshua', 'Caleb'], 'C', 'After Moses died, Joshua led the people across the Jordan (Joshua 3).', 2],
        ['The first king of Israel was', ['David', 'Saul', 'Solomon', 'Samuel'], 'B', 'Samuel anointed Saul as Israel\'s first king (1 Samuel 10).', 1],
        ['In the parable of the Good Samaritan, Jesus taught about', ['prayer', 'love of one\'s neighbour', 'forgiveness of debts', 'the kingdom of heaven'], 'B', 'Luke 10:25–37 answers the question "Who is my neighbour?".', 1],
        ['The coming of the Holy Spirit on the disciples took place on the day of', ['Passover', 'Pentecost', 'Atonement', 'Tabernacles'], 'B', 'Acts 2: the Holy Spirit came on the day of Pentecost.', 1],
        ['Who was the first Christian martyr?', ['Peter', 'James', 'Stephen', 'Paul'], 'C', 'Stephen was stoned to death (Acts 7).', 2],
    ],

    'information-and-communication-technology' => [
        ['Which of the following is an input device?', ['monitor', 'printer', 'keyboard', 'speaker'], 'C', 'A keyboard sends data into the computer; the others output it.', 1],
        ['The brain of the computer is the', ['RAM', 'hard disk', 'CPU', 'motherboard'], 'C', 'The Central Processing Unit carries out instructions and controls the other parts.', 1],
        ['1 kilobyte is equal to', ['100 bytes', '1000 bits', '1024 bytes', '1024 megabytes'], 'C', 'In binary units, 1 KB = 2¹⁰ = 1024 bytes.', 2],
        ['Which of these is an example of system software?', ['Microsoft Word', 'Windows', 'Excel', 'a web browser'], 'B', 'An operating system such as Windows is system software; the others are applications.', 1],
        ['A program that can copy itself and damage files on a computer is a', ['firewall', 'virus', 'browser', 'compiler'], 'B', 'A virus is malicious software that spreads and can corrupt data.', 1],
        ['In a spreadsheet, the intersection of a row and a column is called a', ['cell', 'range', 'formula', 'sheet'], 'A', 'Each box, such as B3, is a cell.', 1],
    ],
];
