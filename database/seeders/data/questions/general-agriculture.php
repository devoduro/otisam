<?php

/*
| SHS General Agriculture: WASSCE-style objective questions written for Otisam, not copied from
| WAEC papers. Covers soil science, crop production, crop protection, farm mechanisation and
| agricultural economics.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Soil science' => [
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
    ],

    'Crop production' => [
        ['Growing two or more crops on the same piece of land at the same time is', ['monocropping', 'mixed cropping', 'crop rotation', 'shifting cultivation'], 'B', 'Mixed cropping grows several crops together.', 1],
        ['Growing different crops on the same land in a planned order over seasons is', ['mixed cropping', 'crop rotation', 'intercropping', 'mulching'], 'B', 'Crop rotation maintains fertility and breaks pest cycles.', 1],
        ['A main reason for including legumes in a crop rotation is that they', ['use up all the nitrogen', 'add nitrogen to the soil', 'need no water', 'attract pests'], 'B', 'Root nodule bacteria fix atmospheric nitrogen.', 2],
        ['Covering the soil surface with dry grass or leaves is', ['mulching', 'thinning', 'pruning', 'staking'], 'A', 'Mulch conserves moisture, controls weeds and reduces erosion.', 1],
        ['Removing excess seedlings to give the rest enough space is', ['thinning', 'transplanting', 'supplying', 'earthing up'], 'A', 'Thinning reduces competition among plants.', 1],
        ['Replacing seeds that failed to germinate is', ['thinning', 'supplying', 'pruning', 'rogueing'], 'B', 'Supplying (gap filling) gives a full plant population.', 2],
        ['Which crop is propagated mainly by stem cuttings?', ['maize', 'rice', 'groundnut', 'cassava'], 'D', 'Cassava is planted from stem cuttings.', 1],
        ['Cocoa is propagated vegetatively by', ['budding and grafting', 'seeds only', 'tubers', 'suckers'], 'A', 'Budding and grafting give uniform, high-yielding trees.', 3],
        ['Plantain and banana are propagated by', ['seeds', 'bulbs', 'vines', 'suckers'], 'D', 'Suckers grow from the base of the parent plant.', 1],
    ],

    'Crop protection' => [
        ['The pest that bores into maize stems and reduces yield is the', ['stem borer', 'aphid', 'weevil', 'grasshopper only'], 'A', 'Stem borer larvae tunnel inside the stem.', 2],
        ['The swollen shoot disease of cocoa is caused by a', ['fungus', 'bacterium', 'virus', 'nematode'], 'C', 'Cocoa swollen shoot virus is spread by mealybugs.', 2],
        ['The black pod disease of cocoa is caused by a', ['virus', 'insect', 'bacterium', 'fungus (Phytophthora)'], 'D', 'Phytophthora fungi cause pods to rot black.', 3],
        ['The insect that spreads the cocoa swollen shoot virus is the', ['locust', 'capsid', 'mealybug', 'termite'], 'C', 'Mealybugs carry the virus from tree to tree.', 2],
        ['Controlling pests by using their natural enemies is', ['chemical control', 'mechanical control', 'cultural control', 'biological control'], 'D', 'Biological control uses predators, parasites or diseases of the pest.', 1],
        ['A chemical used to kill weeds is a', ['fungicide', 'insecticide', 'herbicide', 'nematicide'], 'C', 'Herbicides kill weeds.', 1],
        ['Storage pests such as weevils in grain can be reduced by', ['mixing grain with sand and water', 'storing grain wet', 'storing grain on bare damp floors', 'drying grain properly before storage'], 'D', 'Dry grain resists insects and moulds.', 1],
    ],

    'Mechanisation and economics' => [
        ['A tractor implement used for primary tillage is the', ['sprayer', 'planter', 'disc plough', 'harvester'], 'C', 'Ploughs do the first, deep cultivation of the soil.', 2],
        ['A harrow is used to', ['plant seeds', 'harvest crops', 'spray chemicals', 'break clods into a fine tilth after ploughing'], 'D', 'Harrowing is secondary tillage.', 2],
        ['Which of the following is a problem of farm mechanisation in Ghana?', ['too many trained mechanics', 'cheap spare parts', 'small and scattered farm holdings', 'flat land everywhere'], 'C', 'Small, fragmented plots make tractors uneconomical.', 2],
        ['The government agency that buys and markets cocoa in Ghana is', ['NADMO', 'GSA', 'GIPC', 'COCOBOD'], 'D', 'The Ghana Cocoa Board regulates and markets cocoa.', 1],
        ['Land, labour, capital and management are the', ['types of farming', 'farm records', 'factors of agricultural production', 'farm tools'], 'C', 'These are the factors of production in farming.', 1],
        ['A record of all money received and spent on a farm is the', ['inventory record', 'diary', 'stock record', 'cash analysis (farm account) book'], 'D', 'Farm accounts track income and expenditure.', 2],
    ],

    'SHS 1: The meaning and importance of agriculture' => [
        ['Agriculture includes', ['only crop farming', 'crop production, animal rearing, fisheries and forestry', 'only fishing', 'only selling food'], 'B', 'It covers every activity that produces food, fibre and other products from plants and animals.', 1],
        ['Which is a role of agriculture in national development?', ['printing books', 'producing electricity', 'employing a large share of the workforce', 'repairing cars'], 'C', 'Farming is a major source of jobs in Ghana.', 1],
        ['Growing crops and rearing animals mainly for sale is', ['shifting cultivation only', 'subsistence agriculture', 'nomadic herding only', 'commercial agriculture'], 'D', 'Commercial farmers produce for markets and profit.', 1],
    ],

    'SHS 1: Farm tools and machinery' => [
        ['Which tool is used to spray pesticides on a small farm?', ['cutlass', 'knapsack sprayer', 'wheelbarrow', 'rake'], 'B', 'It is carried on the back and pumped by hand.', 1],
        ['Cleaning and oiling farm tools after use mainly', ['blunts the blades', 'makes them heavier', 'prevents rust and makes them last longer', 'attracts pests'], 'C', 'Good care reduces the cost of replacing tools.', 1],
        ['A tractor-drawn disc plough is used to', ['carry water', 'harvest maize', 'spray weeds', 'turn over and break up the soil'], 'D', 'Ploughing is the first step in primary tillage.', 1],
        ['Which tool is best for moving soil, manure and farm produce over short distances?', ['hoe', 'wheelbarrow', 'pruning saw', 'sickle'], 'B', 'A wheelbarrow carries heavy loads with little effort.', 1],
    ],

    'SHS 1: Principles of crop production' => [
        ['Removing excess seedlings so that the rest have room to grow is', ['mulching', 'transplanting', 'thinning', 'staking'], 'C', 'Thinning reduces competition for light, water and nutrients.', 1],
        ['Covering the soil around crops with dry grass or leaves is', ['pruning', 'ridging', 'harrowing', 'mulching'], 'D', 'Mulch keeps moisture in, controls weeds and adds organic matter as it rots.', 1],
        ['The amount of seed sown per unit area of land is the', ['germination rate', 'seed rate', 'plant population only', 'yield'], 'B', 'It depends on seed size, spacing and viability.', 2],
        ['Supporting yam vines with sticks so they climb is called', ['mulching', 'thinning', 'staking', 'grafting'], 'C', 'Staking exposes more leaves to sunlight and raises yields.', 1],
    ],

    'SHS 1: Animal production and management' => [
        ['Cattle, sheep and goats are ruminants because they', ['lay eggs', 'eat only meat', 'have no teeth', 'have a four-chambered stomach and chew the cud'], 'D', 'The rumen holds microbes that digest cellulose.', 2],
        ['N\'Dama cattle are valued in West Africa because they', ['give the most milk in the world', 'tolerate trypanosomiasis', 'need no water', 'cannot be eaten'], 'B', 'They can survive in tsetse-infested areas.', 3],
        ['Removing the testes of male animals not wanted for breeding is', ['weaning', 'culling', 'castration', 'flushing'], 'C', 'Castrated males are calmer and fatten better.', 2],
        ['Separating young animals from their mothers\' milk is', ['tagging', 'castration', 'dipping', 'weaning'], 'D', 'The young are then fed solid food.', 1],
    ],

    'SHS 1: Soil, forests and climate change' => [
        ['Planting trees on land that has not been forest before is', ['deforestation', 'afforestation', 'reforestation', 'bush fallowing'], 'B', 'Reforestation replants trees where forest was cut down.', 2],
        ['Forests protect farmland mainly by', ['removing soil nutrients', 'increasing floods', 'reducing soil erosion and regulating rainfall', 'raising temperatures'], 'C', 'Tree roots hold soil and forests add moisture to the air.', 1],
        ['Leaving farmland uncultivated for some years to regain fertility is', ['crop rotation', 'continuous cropping', 'irrigation', 'bush fallowing'], 'D', 'Fallowing becomes difficult when land is scarce.', 1],
    ],

    'SHS 1: Crop and animal health' => [
        ['Combining biological, cultural and chemical methods to keep pests below damaging levels is', ['monocropping', 'integrated pest management (IPM)', 'bush burning', 'over-spraying'], 'B', 'IPM reduces reliance on pesticides.', 2],
        ['A chemical used to control fungal diseases of crops is a', ['insecticide', 'herbicide', 'fungicide', 'rodenticide'], 'C', 'Herbicides kill weeds and insecticides kill insects.', 1],
        ['Ticks on cattle are controlled by', ['castration', 'deworming tablets only', 'feeding more grass', 'dipping or spraying with an acaricide'], 'D', 'Ticks are external parasites and spread diseases.', 2],
        ['Giving animals drugs to remove internal worms is', ['dipping', 'deworming', 'vaccination', 'branding'], 'B', 'Worms reduce growth and productivity.', 1],
    ],

    'SHS 1: Agricultural economics and extension' => [
        ['The four factors of production are', ['buyers, sellers, banks and markets', 'seed, water, sun and soil', 'land, labour, capital and entrepreneurship', 'crops, animals, tools and roads'], 'C', 'Every farm business combines these four.', 1],
        ['Agricultural extension officers mainly', ['build roads', 'buy all farm produce', 'collect taxes', 'teach farmers new and improved practices'], 'D', 'They link research findings to farmers.', 1],
        ['When the supply of tomatoes rises sharply in the main season, prices usually', ['rise', 'fall', 'stay the same', 'double'], 'B', 'More supply with the same demand lowers the price.', 2],
    ],

    'SHS 2: Agricultural industries' => [
        ['Turning cocoa beans into cocoa butter and powder is an example of', ['subsistence farming', 'primary production', 'agro-processing', 'shifting cultivation'], 'C', 'Agro-processing adds value to raw produce.', 1],
        ['A feed mill supports livestock production by', ['building pens', 'treating sick animals', 'selling eggs', 'making balanced feed for animals'], 'D', 'Good feed is a large part of the cost of poultry and pigs.', 1],
    ],

    'SHS 2: Surveying and mapping in agriculture' => [
        ['A ranging pole is used in farm surveying to', ['measure soil pH', 'mark points and keep measurements in a straight line', 'water crops', 'dig holes'], 'B', 'Ranging poles help line up chain or tape measurements.', 2],
        ['A farm map drawn at a scale of 1 : 1,000 shows a field 5 cm long. How long is the real field?', ['500 m', '5 m', '50 m', '0.5 m'], 'C', '5 cm × 1,000 = 5,000 cm = 50 m.', 2],
        ['GPS is useful in farm surveying because it', ['kills pests', 'ploughs the land', 'measures rainfall', 'gives the exact position of points on the land'], 'D', 'GPS helps map field boundaries and areas.', 1],
    ],

    'SHS 2: Irrigation, drainage and post-harvest implements' => [
        ['Drainage is needed on farmland that', ['is very dry', 'is waterlogged', 'has sandy soil', 'is on a hilltop'], 'B', 'Removing excess water lets air reach the roots.', 1],
        ['A maize sheller is used to', ['grind maize into flour', 'plant maize', 'remove the grains from the cob', 'spray maize'], 'C', 'Shelling by machine is much faster than by hand.', 1],
        ['Separating grains from the stalks and husks, for example in rice, is', ['ploughing', 'winnowing only', 'milling', 'threshing'], 'D', 'Winnowing then blows away the light chaff.', 2],
    ],

    'SHS 2: Natural resource conservation' => [
        ['Ploughing across a slope rather than up and down it is', ['deep ploughing', 'contour ploughing', 'zero tillage', 'ridging'], 'B', 'The furrows slow run-off and reduce erosion.', 2],
        ['Wildlife reserves and forest reserves are created mainly to', ['mine gold', 'clear land for farms', 'conserve plants and animals', 'build towns'], 'C', 'They protect biodiversity and water sources.', 1],
        ['Which practice conserves soil moisture?', ['mulching', 'bush burning', 'overgrazing', 'removing all cover crops'], 'A', 'Mulch shades the soil and reduces evaporation.', 1],
    ],

    'SHS 3: Good agricultural practices and waste management' => [
        ['Good Agricultural Practices (GAP) aim to produce food that is', ['safe, of good quality and produced sustainably', 'cheap at any cost', 'grown with as many chemicals as possible', 'never inspected'], 'A', 'GAP covers safe chemical use, hygiene, worker welfare and the environment.', 2],
        ['Animal dung and crop waste can be digested without air to produce', ['biogas (mainly methane)', 'petrol', 'oxygen', 'cement'], 'A', 'Biogas is used for cooking and lighting; the leftover slurry is a good fertiliser.', 2],
        ['Turning crop residues and manure into rich organic manure by decay is', ['composting', 'burning', 'leaching', 'liming'], 'A', 'Compost improves soil structure and fertility.', 1],
    ],

    'SHS 3: Processing, storage and food safety' => [
        ['Maize and groundnuts stored with too much moisture can develop', ['aflatoxins from moulds', 'more protein', 'extra vitamins', 'better taste'], 'A', 'Aspergillus moulds produce aflatoxins, which can cause liver cancer.', 2],
        ['Before storage, grains should be dried to a moisture content of about', ['12–13%', '50%', '30%', '80%'], 'A', 'Dry grain resists moulds and insects.', 2],
        ['Hermetic (airtight) storage bags protect grain mainly by', ['cutting off the oxygen that insects need', 'adding chemicals', 'keeping the grain wet', 'letting air flow through'], 'A', 'Insects die without oxygen, so no insecticide is needed.', 2],
        ['Meat inspection at a slaughterhouse is done mainly to', ['make sure the meat is safe to eat', 'raise the price', 'add flavour', 'colour the meat'], 'A', 'Inspectors check for disease before the meat reaches consumers.', 1],
    ],

    'SHS 3: Soil water and soil erosion' => [
        ['The soil water most available to plant roots is', ['capillary water', 'gravitational water', 'hygroscopic water', 'water vapour only'], 'A', 'Gravitational water drains away, and hygroscopic water is held too tightly.', 3],
        ['The two main agents of soil erosion are', ['water and wind', 'earthworms and ants', 'sunlight and shade', 'manure and compost'], 'A', 'Bare soil is easily washed or blown away.', 1],
        ['Erosion that removes a thin, even layer of topsoil over a wide area is', ['sheet erosion', 'gully erosion', 'rill erosion', 'wind erosion'], 'A', 'Rills are small channels; gullies are deep channels.', 2],
    ],

    'SHS 3: Weeds and production economics' => [
        ['A weed is', ['a plant growing where it is not wanted', 'any wild animal', 'a crop disease', 'a fertiliser'], 'A', 'Weeds compete with crops for light, water and nutrients.', 1],
        ['The law of diminishing returns states that adding more of one input while others are fixed eventually', ['gives smaller and smaller increases in output', 'always doubles output', 'has no effect', 'reduces costs to zero'], 'A', 'For example, adding more and more fertiliser to a fixed plot gives less extra yield each time.', 3],
    ],
];
