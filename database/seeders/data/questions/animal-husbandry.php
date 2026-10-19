<?php

/*
| SHS Animal Husbandry: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Breeds and classification' => [
        ['An animal with a four-chambered stomach that chews the cud is a', ['monogastric', 'ruminant', 'carnivore', 'bird'], 'B', 'Ruminants such as cattle, sheep and goats chew the cud.', 1],
        ['Which of these farm animals is monogastric?', ['cattle', 'goat', 'pig', 'sheep'], 'C', 'Pigs have a simple, single-chambered stomach.', 1],
        ['The largest compartment of the ruminant stomach is the', ['reticulum', 'rumen', 'omasum', 'abomasum'], 'B', 'Microbes in the rumen ferment fibrous feed.', 2],
        ['The compartment of the ruminant stomach known as the "true stomach" is the', ['rumen', 'reticulum', 'omasum', 'abomasum'], 'D', 'The abomasum secretes digestive enzymes like a simple stomach.', 2],
        ['The West African Shorthorn is a breed of', ['sheep', 'cattle', 'goat', 'pig'], 'B', 'It is a small, trypanotolerant cattle breed.', 2],
        ['The Large White is a breed of', ['pig', 'cattle', 'sheep', 'poultry'], 'A', 'Large White is a popular pork and bacon breed.', 1],
        ['Animals able to survive in tsetse-infested areas without severe illness are described as', ['trypanotolerant', 'monogastric', 'prolific', 'polled'], 'A', 'Breeds like N\'Dama and West African Shorthorn tolerate trypanosomiasis.', 3],
    ],

    'Terms and reproduction' => [
        ['A young female cow that has not yet calved is a', ['heifer', 'steer', 'bull', 'calf'], 'A', 'A heifer is a young female that has not had a calf.', 1],
        ['A castrated male pig is a', ['boar', 'barrow', 'gilt', 'sow'], 'B', 'A barrow is castrated; a boar is an intact male.', 2],
        ['A young female pig that has not yet farrowed is a', ['sow', 'gilt', 'barrow', 'boar'], 'B', 'A gilt becomes a sow after her first litter.', 2],
        ['The act of giving birth in pigs is called', ['kidding', 'lambing', 'farrowing', 'calving'], 'C', 'Pigs farrow; goats kid; sheep lamb; cows calve.', 1],
        ['The act of giving birth in goats is called', ['kidding', 'farrowing', 'calving', 'foaling'], 'A', 'Goats give birth to kids.', 1],
        ['The period when a female animal is ready to accept the male for mating is', ['gestation', 'oestrus (heat)', 'lactation', 'parturition'], 'B', 'Oestrus is the heat period.', 2],
        ['The gestation period of a cow is about', ['114 days', '150 days', '283 days', '21 days'], 'C', 'Cows carry their calves for about 9 months (283 days).', 2],
        ['The gestation period of a pig is about', ['3 months, 3 weeks and 3 days (114 days)', '150 days', '9 months', '21 days'], 'A', 'Sows are pregnant for about 114 days.', 2],
        ['Placing semen into the female reproductive tract by a technician is', ['natural mating', 'artificial insemination', 'castration', 'culling'], 'B', 'Artificial insemination spreads good genes widely.', 1],
        ['Mating of closely related animals is', ['crossbreeding', 'inbreeding', 'outbreeding', 'grading up'], 'B', 'Inbreeding can bring out harmful recessive traits.', 2],
    ],

    'Feeds and nutrition' => [
        ['Feeds that are high in fibre and low in energy, such as hay, are', ['concentrates', 'roughages', 'supplements', 'additives'], 'B', 'Roughages are bulky and fibrous.', 1],
        ['Feeds that are rich in energy or protein and low in fibre, such as maize and fishmeal, are', ['roughages', 'pasture', 'silage', 'concentrates'], 'D', 'Concentrates supply nutrients in a compact form.', 1],
        ['Green fodder preserved by fermentation in the absence of air is', ['hay', 'mash', 'bran', 'silage'], 'D', 'Silage is made in silos or sealed pits.', 2],
        ['A ration that supplies all the nutrients an animal needs in the right amounts is a', ['balanced ration', 'maintenance ration only', 'roughage', 'creep feed'], 'A', 'Balanced rations support growth and production.', 1],
        ['Feed given to piglets in an area the sow cannot reach is', ['creep feed', 'silage', 'hay', 'layer mash'], 'A', 'Creep feeding supplements milk and eases weaning.', 3],
        ['Lack of iron in housed piglets leads to', ['piglet anaemia', 'milk fever', 'bloat', 'foot rot'], 'A', 'Piglets on concrete floors need iron injections.', 3],
    ],

    'Poultry' => [
        ['Chickens raised mainly for egg production are', ['broilers', 'cockerels', 'layers', 'capons'], 'C', 'Layers are bred to produce many eggs.', 1],
        ['The system in which poultry are kept in cages, usually one to three birds per cage, is the', ['deep litter system', 'fold system', 'free range system', 'battery cage system'], 'D', 'Battery cages give high stocking density and clean eggs.', 2],
        ['Removing the tip of a bird\'s beak to prevent pecking is', ['debeaking', 'dubbing', 'candling', 'culling'], 'A', 'Debeaking reduces feather pecking and cannibalism.', 2],
        ['Examining eggs against a light to check for cracks or fertility is', ['grading', 'brooding', 'candling', 'incubation'], 'C', 'Candling shows cracks, air cells and developing embryos.', 2],
        ['The incubation period of chicken eggs is about', ['7 days', '42 days', '28 days', '21 days'], 'D', 'Chicken eggs hatch in about 21 days.', 1],
    ],

    'Health' => [
        ['Newcastle disease mainly affects', ['cattle', 'pigs', 'poultry', 'goats'], 'C', 'Newcastle disease is a serious viral disease of poultry.', 1],
        ['Trypanosomiasis in cattle is spread by the', ['tick', 'housefly', 'mosquito', 'tsetse fly'], 'D', 'Tsetse flies transmit trypanosomes.', 1],
        ['Coccidiosis in poultry is caused by a', ['virus', 'bacterium', 'protozoan', 'worm'], 'C', 'Eimeria protozoa cause coccidiosis, often with bloody droppings.', 3],
        ['Ticks on cattle can be controlled by', ['artificial insemination', 'feeding more salt', 'castration', 'dipping or spraying with acaricides'], 'D', 'Acaricide dips and sprays kill ticks.', 2],
        ['African swine fever is a disease of', ['cattle', 'sheep', 'pigs', 'poultry'], 'C', 'It is a deadly viral disease of pigs with no vaccine.', 2],
    ],
];
