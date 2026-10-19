<?php

/*
| SHS Integrated Science: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers. Themes: diversity of matter, cycles, systems, energy, interactions of matter.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Matter
    ['An atom has 11 protons and 12 neutrons. What is its mass number?', ['11', '12', '23', '1'], 'C', 'Mass number = protons + neutrons = 11 + 12 = 23.', 2],
    ['The electronic configuration of an element with atomic number 12 is', ['2, 8, 2', '2, 10', '2, 8, 1, 1', '2, 2, 8'], 'A', 'Fill shells 2, then 8, then the remaining 2: 2, 8, 2.', 2],
    ['Isotopes of an element have the same number of', ['neutrons', 'protons', 'nucleons', 'mass number'], 'B', 'Isotopes have the same number of protons but different numbers of neutrons.', 2],
    ['Which of the following is a physical change?', ['burning of paper', 'rusting of iron', 'melting of ice', 'souring of milk'], 'C', 'Melting changes only the state; no new substance forms.', 1],
    ['The process used to obtain pure water from sea water is', ['filtration', 'distillation', 'decantation', 'chromatography'], 'B', 'Distillation boils the water and condenses the vapour, leaving the salts behind.', 2],
    ['Paper chromatography is used to separate', ['sand and water', 'the dyes in ink', 'iron and sulphur', 'oil and water'], 'B', 'Different dyes travel different distances up the paper.', 2],
    ['Which gas turns lime water milky?', ['oxygen', 'hydrogen', 'carbon dioxide', 'nitrogen'], 'C', 'CO₂ reacts with calcium hydroxide to form insoluble calcium carbonate.', 1],
    ['A substance that speeds up a chemical reaction without itself being used up is a', ['catalyst', 'reactant', 'product', 'solvent'], 'A', 'A catalyst lowers the activation energy and is unchanged at the end.', 2],
    ['The reaction between an acid and a base to form salt and water is called', ['oxidation', 'neutralisation', 'decomposition', 'fermentation'], 'B', 'Acid + base → salt + water is neutralisation.', 1],

    // Cycles
    ['The process by which nitrogen gas in the air is changed into nitrates in the soil by bacteria is', ['denitrification', 'nitrogen fixation', 'ammonification', 'transpiration'], 'B', 'Nitrogen-fixing bacteria such as Rhizobium in legume root nodules convert N₂ into nitrogen compounds.', 3],
    ['Which organisms break down dead plants and animals and return nutrients to the soil?', ['producers', 'decomposers', 'herbivores', 'carnivores'], 'B', 'Decomposers such as bacteria and fungi recycle nutrients.', 1],
    ['In the water cycle, water vapour cools to form clouds by', ['evaporation', 'condensation', 'precipitation', 'infiltration'], 'B', 'Cooling vapour condenses into tiny droplets that form clouds.', 1],
    ['The first stage in the germination of a seed is the uptake of water called', ['imbibition', 'osmosis only', 'transpiration', 'pollination'], 'A', 'A dry seed absorbs water (imbibition), which activates its enzymes.', 3],
    ['The type of reproduction that involves only one parent is', ['sexual reproduction', 'asexual reproduction', 'fertilisation', 'pollination'], 'B', 'Asexual reproduction (budding, fission, vegetative propagation) needs only one parent.', 1],

    // Systems
    ['The enzyme in saliva that digests starch is', ['pepsin', 'amylase', 'lipase', 'trypsin'], 'B', 'Salivary amylase breaks starch down into maltose.', 2],
    ['Which part of the human eye controls the amount of light entering it?', ['retina', 'cornea', 'lens', 'iris'], 'D', 'The iris changes the size of the pupil.', 2],
    ['Insulin is produced by the', ['liver', 'thyroid gland', 'kidney', 'pancreas'], 'D', 'Insulin from the pancreas lowers blood sugar.', 2],
    ['The red blood cells transport oxygen because they contain', ['plasma', 'antibodies', 'platelets', 'haemoglobin'], 'D', 'Haemoglobin binds oxygen in the lungs and releases it in tissues.', 1],
    ['Which blood group is known as the universal donor?', ['A', 'B', 'AB', 'O'], 'D', 'Group O red cells have no A or B antigens, so they can be given to any ABO group.', 3],
    ['The male reproductive cell is the', ['sperm', 'ovum', 'zygote', 'embryo'], 'A', 'Sperm is the male gamete; the ovum is the female gamete.', 1],

    // Energy and forces
    ['A body of mass 10 kg is lifted through a height of 2 m. Calculate the potential energy gained. (g = 10 m/s²)', ['20 J', '100 J', '200 J', '2000 J'], 'C', 'PE = mgh = 10 × 10 × 2 = 200 J.', 2],
    ['Calculate the power of a machine that does 600 J of work in 20 s.', ['30 W', '12 W', '580 W', '12,000 W'], 'A', 'Power = work ÷ time = 600 ÷ 20 = 30 W.', 2],
    ['Three resistors of 2 Ω, 3 Ω and 5 Ω are connected in series. Find the total resistance.', ['1 Ω', '5 Ω', '10 Ω', '30 Ω'], 'C', 'In series, R = 2 + 3 + 5 = 10 Ω.', 2],
    ['Two 6 Ω resistors are connected in parallel. Find the total resistance.', ['3 Ω', '6 Ω', '12 Ω', '36 Ω'], 'A', '1/R = 1/6 + 1/6 = 1/3, so R = 3 Ω.', 3],
    ['The device used to convert mechanical energy into electrical energy is a', ['motor', 'battery', 'transformer', 'generator'], 'D', 'A generator (dynamo) changes mechanical energy into electricity. A motor does the reverse.', 2],
    ['Sound cannot travel through', ['water', 'air', 'steel', 'a vacuum'], 'D', 'Sound needs a medium; there are no particles in a vacuum to vibrate.', 1],
    ['The bending of light as it passes from air into water is called', ['reflection', 'diffraction', 'refraction', 'dispersion'], 'C', 'Light changes speed and direction at the boundary: refraction.', 2],
    ['The splitting of white light into its seven colours by a prism is called', ['dispersion', 'refraction', 'reflection', 'interference'], 'A', 'Dispersion separates white light into a spectrum.', 2],

    // Agriculture, health and the environment
    ['Which of the following is a method of controlling soil erosion?', ['continuous cropping', 'bush burning', 'contour ploughing', 'overgrazing'], 'C', 'Ploughing along contours slows water running down slopes.', 2],
    ['Which of the following is a vector of the disease river blindness (onchocerciasis)?', ['housefly', 'Anopheles mosquito', 'tsetse fly', 'blackfly (Simulium)'], 'D', 'River blindness is spread by blackflies that breed in fast-flowing rivers.', 3],
    ['Sleeping sickness is spread by the', ['tsetse fly', 'Aedes mosquito', 'housefly', 'blackfly'], 'A', 'The tsetse fly transmits Trypanosoma, which causes sleeping sickness.', 2],
    ['Cholera is mainly spread through', ['mosquito bites', 'shaking hands', 'contaminated food and water', 'the air'], 'C', 'Cholera bacteria spread through food and water contaminated with faeces.', 1],
    ['The build-up of gases such as carbon dioxide that trap heat in the atmosphere causes', ['acid rain only', 'earthquakes', 'the ozone layer to thicken', 'the greenhouse effect and global warming'], 'D', 'Greenhouse gases trap infrared radiation and warm the earth.', 2],
    ['Which of the following is a biological method of pest control?', ['using ladybirds to eat aphids', 'spraying insecticides', 'burning the farm', 'hand-picking only'], 'A', 'Biological control uses natural enemies of the pest.', 2],
];
