<?php

/*
| SHS General Agriculture: WASSCE-style objective questions written for Otisam, not copied from
| WAEC papers. Covers soil science, crop production, crop protection, farm mechanisation and
| agricultural economics.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Soil science
    ['The soil type with the largest particles and the poorest water-holding capacity is', ['clay', 'loam', 'sand', 'silt'], 'C', 'Sand has large particles and big pores, so water drains away quickly.', 1],
    ['The best soil for most crops is', ['sandy soil', 'clay soil', 'loamy soil', 'gravel'], 'C', 'Loam mixes sand, silt and clay with humus: good drainage and good water holding.', 1],
    ['Decomposed organic matter in the soil is called', ['humus', 'gravel', 'parent rock', 'subsoil'], 'A', 'Humus improves soil structure, fertility and water holding.', 1],
    ['The process by which soluble nutrients are washed down below the reach of plant roots is', ['erosion', 'leaching', 'weathering', 'capillarity'], 'B', 'Heavy rain carries nutrients down the soil profile.', 2],
    ['Soil acidity can be corrected by applying', ['sulphur', 'lime', 'ammonium sulphate', 'urea'], 'B', 'Lime (calcium carbonate) raises soil pH.', 2],
    ['The three major nutrients (macronutrients) needed by crops are', ['nitrogen, phosphorus and potassium', 'iron, zinc and copper', 'calcium, sodium and chlorine', 'carbon, oxygen and hydrogen'], 'A', 'NPK fertilisers supply these major nutrients.', 1],
    ['Yellowing of older leaves (chlorosis) in maize is usually a sign of lack of', ['nitrogen', 'calcium', 'boron', 'zinc'], 'A', 'Nitrogen is mobile and moves to new leaves, so older leaves yellow first.', 3],
    ['Phosphorus is especially important in crops for', ['leaf colour only', 'root development and early maturity', 'preventing lodging only', 'reducing water loss'], 'B', 'Phosphorus promotes root growth, flowering and maturity.', 2],
    ['A fertiliser labelled 15-15-15 contains', ['15% each of N, P₂O₅ and K₂O', '15 kg of nitrogen only', '45% potassium', '15% water'], 'A', 'The numbers give the percentages of N, P₂O₅ and K₂O.', 3],
    ['Which of the following is an organic manure?', ['urea', 'poultry droppings', 'muriate of potash', 'sulphate of ammonia'], 'B', 'Animal droppings are organic; the others are inorganic fertilisers.', 1],

    // Crop production
    ['Growing two or more crops on the same piece of land at the same time is', ['monocropping', 'mixed cropping', 'crop rotation', 'shifting cultivation'], 'B', 'Mixed cropping grows several crops together.', 1],
    ['Growing different crops on the same land in a planned order over seasons is', ['mixed cropping', 'crop rotation', 'intercropping', 'mulching'], 'B', 'Crop rotation maintains fertility and breaks pest cycles.', 1],
    ['A main reason for including legumes in a crop rotation is that they', ['use up all the nitrogen', 'add nitrogen to the soil', 'need no water', 'attract pests'], 'B', 'Root nodule bacteria fix atmospheric nitrogen.', 2],
    ['Covering the soil surface with dry grass or leaves is', ['mulching', 'thinning', 'pruning', 'staking'], 'A', 'Mulch conserves moisture, controls weeds and reduces erosion.', 1],
    ['Removing excess seedlings to give the rest enough space is', ['thinning', 'transplanting', 'supplying', 'earthing up'], 'A', 'Thinning reduces competition among plants.', 1],
    ['Replacing seeds that failed to germinate is', ['thinning', 'supplying', 'pruning', 'rogueing'], 'B', 'Supplying (gap filling) gives a full plant population.', 2],
    ['Which crop is propagated mainly by stem cuttings?', ['maize', 'rice', 'groundnut', 'cassava'], 'D', 'Cassava is planted from stem cuttings.', 1],
    ['Cocoa is propagated vegetatively by', ['budding and grafting', 'seeds only', 'tubers', 'suckers'], 'A', 'Budding and grafting give uniform, high-yielding trees.', 3],
    ['Plantain and banana are propagated by', ['seeds', 'bulbs', 'vines', 'suckers'], 'D', 'Suckers grow from the base of the parent plant.', 1],

    // Crop protection
    ['The pest that bores into maize stems and reduces yield is the', ['stem borer', 'aphid', 'weevil', 'grasshopper only'], 'A', 'Stem borer larvae tunnel inside the stem.', 2],
    ['The swollen shoot disease of cocoa is caused by a', ['fungus', 'bacterium', 'virus', 'nematode'], 'C', 'Cocoa swollen shoot virus is spread by mealybugs.', 2],
    ['The black pod disease of cocoa is caused by a', ['virus', 'insect', 'bacterium', 'fungus (Phytophthora)'], 'D', 'Phytophthora fungi cause pods to rot black.', 3],
    ['The insect that spreads the cocoa swollen shoot virus is the', ['locust', 'capsid', 'mealybug', 'termite'], 'C', 'Mealybugs carry the virus from tree to tree.', 2],
    ['Controlling pests by using their natural enemies is', ['chemical control', 'mechanical control', 'cultural control', 'biological control'], 'D', 'Biological control uses predators, parasites or diseases of the pest.', 1],
    ['A chemical used to kill weeds is a', ['fungicide', 'insecticide', 'herbicide', 'nematicide'], 'C', 'Herbicides kill weeds.', 1],
    ['Storage pests such as weevils in grain can be reduced by', ['mixing grain with sand and water', 'storing grain wet', 'storing grain on bare damp floors', 'drying grain properly before storage'], 'D', 'Dry grain resists insects and moulds.', 1],

    // Mechanisation and economics
    ['A tractor implement used for primary tillage is the', ['sprayer', 'planter', 'disc plough', 'harvester'], 'C', 'Ploughs do the first, deep cultivation of the soil.', 2],
    ['A harrow is used to', ['plant seeds', 'harvest crops', 'spray chemicals', 'break clods into a fine tilth after ploughing'], 'D', 'Harrowing is secondary tillage.', 2],
    ['Which of the following is a problem of farm mechanisation in Ghana?', ['too many trained mechanics', 'cheap spare parts', 'small and scattered farm holdings', 'flat land everywhere'], 'C', 'Small, fragmented plots make tractors uneconomical.', 2],
    ['The government agency that buys and markets cocoa in Ghana is', ['NADMO', 'GSA', 'GIPC', 'COCOBOD'], 'D', 'The Ghana Cocoa Board regulates and markets cocoa.', 1],
    ['Land, labour, capital and management are the', ['types of farming', 'farm records', 'factors of agricultural production', 'farm tools'], 'C', 'These are the factors of production in farming.', 1],
    ['A record of all money received and spent on a farm is the', ['inventory record', 'diary', 'stock record', 'cash analysis (farm account) book'], 'D', 'Farm accounts track income and expenditure.', 2],
];
