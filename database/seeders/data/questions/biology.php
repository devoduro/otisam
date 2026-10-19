<?php

/*
| SHS Biology: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Cell biology' => [
        ['Which organelle is found in plant cells but not in animal cells?', ['nucleus', 'mitochondrion', 'chloroplast', 'ribosome'], 'C', 'Chloroplasts carry out photosynthesis and are found only in plant cells and some protists.', 1],
        ['The cell organelle responsible for protein synthesis is the', ['ribosome', 'lysosome', 'vacuole', 'cell wall'], 'A', 'Ribosomes assemble proteins from amino acids.', 2],
        ['The movement of water molecules from a dilute solution to a concentrated solution through a selectively permeable membrane is', ['diffusion', 'osmosis', 'active transport', 'plasmolysis'], 'B', 'Osmosis is the diffusion of water across a selectively permeable membrane.', 2],
        ['A plant cell placed in a concentrated salt solution will', ['burst', 'become turgid', 'become plasmolysed', 'remain unchanged'], 'C', 'Water leaves the cell by osmosis, and the cytoplasm shrinks away from the wall.', 3],
        ['The type of cell division that produces gametes is', ['mitosis', 'meiosis', 'binary fission', 'budding'], 'B', 'Meiosis halves the chromosome number to make gametes.', 2],
        ['How many chromosomes are in a normal human body cell?', ['23', '44', '46', '48'], 'C', 'Human body cells are diploid with 46 chromosomes (23 pairs).', 1],
    ],

    'Classification' => [
        ['Which kingdom do mushrooms belong to?', ['Plantae', 'Fungi', 'Monera', 'Animalia'], 'B', 'Mushrooms are fungi: they feed on dead matter and have no chlorophyll.', 1],
        ['The scientific naming of organisms using two names (genus and species) is called', ['taxonomy', 'binomial nomenclature', 'phylogeny', 'classification'], 'B', 'Linnaeus\'s binomial system, for example Homo sapiens.', 2],
        ['Which of the following is an arthropod?', ['earthworm', 'snail', 'grasshopper', 'tapeworm'], 'C', 'Arthropods have jointed legs and an exoskeleton.', 1],
        ['Animals that maintain a constant body temperature are described as', ['poikilothermic', 'homoiothermic (endothermic)', 'ectothermic', 'cold-blooded'], 'B', 'Birds and mammals are homoiothermic (warm-blooded).', 2],
    ],

    'Nutrition' => [
        ['The raw materials for photosynthesis are', ['oxygen and glucose', 'carbon dioxide and water', 'starch and oxygen', 'nitrogen and water'], 'B', '6CO₂ + 6H₂O → C₆H₁₂O₆ + 6O₂ in the presence of light and chlorophyll.', 1],
        ['The chemical used to test for starch is', ['Benedict\'s solution', 'iodine solution', 'Millon\'s reagent', 'Sudan III'], 'B', 'Iodine turns blue-black with starch.', 1],
        ['Bile is produced by the', ['gall bladder', 'liver', 'pancreas', 'stomach'], 'B', 'Bile is made in the liver and stored in the gall bladder; it emulsifies fats.', 2],
        ['Digested food is absorbed mainly in the', ['stomach', 'ileum', 'colon', 'oesophagus'], 'B', 'The ileum has villi that give a large surface area for absorption.', 2],
        ['An organism that lives on or in another organism and harms it is a', ['saprophyte', 'predator', 'symbiont', 'parasite'], 'D', 'A parasite gets food from its host and harms it.', 1],
    ],

    'Transport, respiration, excretion' => [
        ['Which blood cells help in the clotting of blood?', ['red blood cells', 'white blood cells', 'platelets', 'plasma'], 'C', 'Platelets start the clotting process at a wound.', 1],
        ['The chamber of the heart that pumps blood to the whole body is the', ['right atrium', 'left atrium', 'right ventricle', 'left ventricle'], 'D', 'The thick-walled left ventricle pumps oxygenated blood into the aorta.', 2],
        ['The end products of anaerobic respiration in yeast are', ['ethanol and carbon dioxide', 'lactic acid only', 'water and carbon dioxide', 'oxygen and glucose'], 'A', 'Yeast ferments glucose to ethanol and CO₂.', 2],
        ['Lactic acid builds up in human muscles during', ['anaerobic respiration', 'aerobic respiration', 'photosynthesis', 'digestion'], 'A', 'Without enough oxygen, muscles respire anaerobically and produce lactic acid.', 2],
        ['Urea is formed in the', ['kidney', 'lungs', 'bladder', 'liver'], 'D', 'The liver breaks down excess amino acids (deamination) to form urea.', 3],
        ['Water moves up the stem of a plant mainly through the', ['xylem', 'phloem', 'cortex', 'pith'], 'A', 'Xylem vessels carry water and mineral salts.', 1],
        ['The transport of manufactured food in plants is called', ['transpiration', 'osmosis', 'guttation', 'translocation'], 'D', 'Translocation moves sugars through the phloem.', 2],
    ],

    'Coordination and reproduction' => [
        ['The part of the brain that controls balance and coordination of movement is the', ['cerebellum', 'cerebrum', 'medulla oblongata', 'hypothalamus'], 'A', 'The cerebellum coordinates muscle movement and balance.', 2],
        ['A quick, automatic response to a stimulus, such as pulling the hand from a hot object, is a', ['conditioned reflex', 'tropism', 'voluntary action', 'reflex action'], 'D', 'A reflex action is rapid and does not need conscious thought.', 1],
        ['The growth of a plant shoot towards light is called', ['phototropism', 'geotropism', 'hydrotropism', 'thigmotropism'], 'A', 'Phototropism is a growth response to light, controlled by auxin.', 1],
        ['The hormone that controls the "fight or flight" response is', ['insulin', 'thyroxine', 'adrenaline', 'oestrogen'], 'C', 'Adrenaline raises heart rate and blood sugar in emergencies.', 2],
        ['In humans, fertilisation normally takes place in the', ['uterus', 'ovary', 'oviduct (fallopian tube)', 'vagina'], 'C', 'The sperm meets the ovum in the oviduct.', 2],
        ['Fruits that are dispersed by wind are usually', ['fleshy and brightly coloured', 'covered with hooks', 'heavy with hard shells', 'light with wings or hairs'], 'D', 'Wings or parachute hairs let wind carry them far.', 2],
    ],

    'Genetics and evolution' => [
        ['A cross between a homozygous tall plant (TT) and a homozygous short plant (tt) gives offspring that are', ['all tall', 'all short', '3 tall : 1 short', '1 tall : 1 short'], 'A', 'All offspring are Tt, and tall is dominant.', 2],
        ['A test cross is done by crossing an organism with one that is', ['homozygous dominant', 'heterozygous', 'homozygous recessive', 'a mutant'], 'C', 'Crossing with the homozygous recessive reveals whether the unknown is homozygous or heterozygous.', 3],
        ['Sickle-cell anaemia is an example of', ['an infectious disease', 'a viral disease', 'a deficiency disease', 'a genetic (inherited) disorder'], 'D', 'It is inherited through a recessive allele for abnormal haemoglobin.', 2],
        ['A person with blood group AB has the genotype', ['IᴬIᴮ', 'IᴬIᴬ', 'IᴮIᴮ', 'ii'], 'A', 'A and B are codominant, so group AB has IᴬIᴮ.', 3],
        ['The theory of evolution by natural selection was proposed by', ['Gregor Mendel', 'Robert Hooke', 'Louis Pasteur', 'Charles Darwin'], 'D', 'Darwin proposed natural selection in 1859.', 1],
    ],

    'Ecology' => [
        ['In the food chain grass → grasshopper → lizard → hawk, the lizard is a', ['producer', 'primary consumer', 'secondary consumer', 'decomposer'], 'C', 'The lizard eats a primary consumer (the grasshopper), so it is a secondary consumer.', 2],
        ['The total number of organisms of one species living in an area is the', ['population', 'community', 'ecosystem', 'habitat'], 'A', 'A population is all members of one species in a given area.', 1],
        ['The relationship between Rhizobium bacteria and leguminous plants is', ['parasitism', 'predation', 'commensalism', 'mutualism'], 'D', 'The bacteria fix nitrogen for the plant and get food and shelter in return.', 2],
    ],

    'SHS 1: Biology, scientific method and microscopes' => [
        ['A light microscope has a ×10 eyepiece and a ×10 objective. What is the total magnification?', ['×20', '×100', '×1,000', '×10'], 'B', 'Total magnification = eyepiece × objective = 10 × 10 = 100.', 1],
        ['Compared with a light microscope, an electron microscope', ['magnifies less', 'uses visible light', 'can only view living cells', 'has a much higher resolution'], 'D', 'Electron beams have very short wavelengths, so they reveal much finer detail such as organelle structure.', 2],
        ['Which part of a light microscope is turned to bring the specimen into sharp focus at high power?', ['eyepiece cover', 'mirror', 'stage clips', 'fine adjustment knob'], 'D', 'The coarse knob gives rough focus; the fine knob sharpens the image.', 1],
        ['The branch of biology that studies tiny organisms such as bacteria is', ['ecology', 'microbiology', 'anatomy', 'botany'], 'B', 'Microbiology covers bacteria, viruses, fungi and protozoa.', 1],
    ],

    'SHS 1: Fish farming, processing and conservation' => [
        ['Which fish are most commonly farmed in ponds and cages in Ghana?', ['sardines and herrings', 'sharks and tuna', 'tilapia and catfish', 'whales and dolphins'], 'C', 'Tilapia and African catfish grow fast and suit fresh-water ponds and Lake Volta cages.', 1],
        ['The rearing of fish under controlled conditions is called', ['sericulture', 'apiculture', 'horticulture', 'aquaculture'], 'D', 'Apiculture is beekeeping and horticulture is growing fruits and vegetables.', 1],
        ['The Chorkor smoker improves local fish processing because it', ['freezes fish', 'smokes fish evenly with less fuel', 'adds salt automatically', 'cans the fish'], 'B', 'It holds stacked trays over one fire, using less wood and smoking the fish evenly.', 2],
        ['A "closed season" for fishing helps conservation by', ['reducing fish prices', 'increasing catches during breeding', 'allowing fish to breed and stocks to recover', 'killing predators'], 'C', 'Fishing pauses during the main breeding period.', 1],
    ],

    'SHS 1: Cells and their organelles' => [
        ['Proteins are made on the', ['cell wall', 'mitochondria', 'vacuoles', 'ribosomes'], 'D', 'Ribosomes join amino acids into proteins, using instructions from mRNA.', 1],
        ['Which structure is found in plant cells but not in animal cells?', ['cell membrane', 'nucleus', 'cell wall', 'cytoplasm'], 'C', 'Plant cells also have chloroplasts and a large permanent vacuole.', 1],
        ['The plant cell wall is made mainly of', ['chitin', 'cellulose', 'protein', 'fat'], 'B', 'Fungal cell walls contain chitin.', 1],
        ['Which organelle controls the activities of the cell?', ['chloroplast', 'vacuole', 'nucleus', 'ribosome'], 'C', 'The nucleus contains the DNA that directs the cell.', 1],
    ],

    'SHS 1: Organisms and their classification' => [
        ['Bacteria belong to the kingdom', ['Protoctista', 'Fungi', 'Plantae', 'Monera (Prokaryotae)'], 'D', 'Bacteria have no true nucleus, so they are prokaryotes.', 2],
        ['The system of naming each organism with a genus and a species name was developed by', ['Charles Darwin', 'Carolus Linnaeus', 'Gregor Mendel', 'Robert Hooke'], 'B', 'In binomial nomenclature, humans are Homo sapiens.', 1],
        ['In the name Homo sapiens, "Homo" is the', ['family', 'species', 'genus', 'order'], 'C', 'The first name is the genus, written with a capital letter.', 1],
        ['Mushrooms belong to the kingdom', ['Plantae', 'Fungi', 'Animalia', 'Monera'], 'B', 'Fungi do not photosynthesise; they absorb food from their surroundings.', 1],
    ],

    'SHS 1: Ecology' => [
        ['In a food chain, the organisms that make their own food are the', ['carnivores', 'primary consumers', 'decomposers', 'producers'], 'D', 'Green plants make food by photosynthesis.', 1],
        ['About what percentage of the energy at one trophic level passes to the next?', ['50%', '10%', '90%', '100%'], 'B', 'Most energy is lost as heat in respiration, in movement and in waste.', 2],
        ['Organisms that break down dead matter and return nutrients to the soil are', ['herbivores', 'producers', 'decomposers', 'parasites'], 'C', 'Bacteria and fungi are the main decomposers.', 1],
    ],

    'SHS 1: Diseases and infections' => [
        ['Sleeping sickness is carried by the', ['blackfly', 'housefly', 'Anopheles mosquito', 'tsetse fly'], 'D', 'The tsetse fly transmits the Trypanosoma parasite.', 2],
        ['Bilharzia (schistosomiasis) is spread through', ['mosquito bites', 'water snails in infected fresh water', 'coughing', 'eating rice'], 'B', 'Larvae released by the snails enter the skin of people in the water.', 2],
        ['Cholera is caused by the bacterium', ['Mycobacterium tuberculosis', 'Plasmodium falciparum', 'Vibrio cholerae', 'Salmonella typhi'], 'C', 'It causes severe watery diarrhoea and is spread through dirty water.', 2],
        ['River blindness (onchocerciasis) is spread by the', ['housefly', 'tsetse fly', 'sandfly', 'blackfly'], 'D', 'Blackflies breed in fast-flowing rivers.', 2],
    ],

    'SHS 1: Mammalian systems' => [
        ['Which enzyme starts the digestion of protein in the stomach?', ['amylase', 'pepsin', 'lipase', 'maltase'], 'B', 'Pepsin works in the acid conditions of the stomach.', 2],
        ['Bile helps digestion by', ['killing all bacteria', 'digesting proteins', 'emulsifying fats into small droplets', 'absorbing water'], 'C', 'Smaller droplets give lipase a larger surface to work on.', 2],
        ['Digested food is absorbed into the blood mainly through the', ['oesophagus', 'stomach wall', 'large intestine', 'villi of the small intestine'], 'D', 'Villi give a huge surface area and are rich in capillaries.', 1],
        ['The pulmonary artery carries', ['oxygenated blood to the body', 'deoxygenated blood from the heart to the lungs', 'blood to the liver', 'lymph'], 'B', 'It is the only artery that carries deoxygenated blood.', 2],
        ['Mammals have a double circulation, meaning blood passes through the heart', ['only when exercising', 'once in each circuit', 'twice in each complete circuit', 'three times'], 'C', 'One circuit goes to the lungs and the other to the rest of the body.', 2],
    ],

    'SHS 1: Plant systems' => [
        ['Water and minerals move up a plant through the', ['epidermis', 'phloem', 'cambium', 'xylem'], 'D', 'Phloem carries sugars from the leaves to other parts.', 1],
        ['The movement of sugars through the phloem is', ['transpiration', 'translocation', 'osmosis', 'guttation'], 'B', 'Transpiration is the loss of water vapour from the leaves.', 2],
        ['Transpiration happens faster when the air is', ['very humid', 'cold and still', 'hot, dry and windy', 'dark and wet'], 'C', 'These conditions increase evaporation from the leaves.', 2],
        ['Gases enter and leave a leaf mainly through the', ['veins', 'xylem', 'cuticle', 'stomata'], 'D', 'Guard cells open and close the stomata.', 1],
    ],

    'SHS 2: Mosses and ferns' => [
        ['Mosses are anchored to the ground by', ['true roots', 'rhizoids', 'tap roots', 'tubers'], 'B', 'Mosses have no true roots, stems or leaves with vascular tissue.', 2],
        ['In ferns, spores are produced in clusters called sori on the', ['flowers', 'roots', 'underside of the fronds', 'seeds'], 'C', 'Ferns have no flowers or seeds; they reproduce by spores.', 2],
        ['In the life cycle of a moss, the dominant, green, leafy plant is the', ['seedling', 'sporophyte', 'prothallus', 'gametophyte'], 'D', 'In ferns the main plant is the sporophyte instead.', 3],
        ['Mosses grow mainly in damp places because', ['they cannot photosynthesise', 'their sperm need water to swim to the eggs', 'they need deep soil', 'they are parasites'], 'B', 'Water is needed for fertilisation and the plants lose water easily.', 2],
    ],

    'SHS 2: Improving crops and animals' => [
        ['Crossing two different varieties to combine their good qualities is', ['irrigation', 'pruning', 'hybridisation (cross-breeding)', 'grafting only'], 'C', 'For example, crossing a high-yielding maize with a drought-resistant one.', 2],
        ['Choosing the best animals to breed from, generation after generation, is', ['vaccination', 'cloning only', 'mutation', 'selective breeding'], 'D', 'Selective breeding improves traits such as milk yield or growth rate.', 1],
        ['A crop variety bred to resist a disease mainly helps farmers by', ['making the crop grow slower', 'reducing losses and the need for chemicals', 'increasing pests', 'reducing yield'], 'B', 'Resistant varieties give more reliable harvests.', 1],
    ],

    'SHS 2: Cell theory and membrane transport' => [
        ['Which statement belongs to the cell theory?', ['only animals have cells', 'cells arise from non-living matter', 'all living things are made of cells', 'cells never divide'], 'C', 'It also states that the cell is the basic unit of life and that cells come from existing cells.', 1],
        ['Active transport differs from diffusion because it', ['only happens in dead cells', 'needs no energy', 'only moves water', 'moves substances against a concentration gradient using energy (ATP)'], 'D', 'Root hair cells use active transport to take in mineral ions.', 2],
        ['A white blood cell engulfing a bacterium is an example of', ['exocytosis', 'endocytosis (phagocytosis)', 'osmosis', 'diffusion'], 'B', 'The membrane wraps around the particle and takes it into the cell.', 3],
        ['Releasing hormones or enzymes from a cell in vesicles that fuse with the membrane is', ['plasmolysis', 'endocytosis', 'exocytosis', 'osmosis'], 'C', 'Exocytosis moves materials out of the cell.', 3],
    ],

    'SHS 2: DNA and protein synthesis' => [
        ['The double-helix model of DNA was proposed in 1953 by', ['Pasteur and Koch', 'Mendel and Darwin', 'Hooke and Schleiden', 'Watson and Crick'], 'D', 'Rosalind Franklin\'s X-ray images were key evidence.', 1],
        ['DNA replication is described as semi-conservative because', ['both strands are new', 'each new DNA molecule keeps one original strand', 'it happens only in half of all cells', 'it keeps no original strands'], 'B', 'Each strand acts as a template for a new partner strand.', 3],
        ['The making of messenger RNA (mRNA) from a DNA template is', ['replication', 'translation', 'transcription', 'mutation'], 'C', 'Transcription happens in the nucleus; translation happens at the ribosomes.', 2],
        ['In RNA, the base that replaces thymine is', ['cytosine', 'adenine', 'guanine', 'uracil'], 'D', 'RNA uses A, U, G and C.', 2],
        ['A group of three bases on mRNA that codes for one amino acid is a', ['gene', 'codon', 'chromosome', 'nucleotide'], 'B', 'Each codon matches an anticodon on a tRNA molecule.', 2],
    ],

    'SHS 2: Insects and their economic importance' => [
        ['The life cycle of a butterfly is', ['egg → adult', 'egg → nymph → adult', 'egg → larva (caterpillar) → pupa → adult', 'larva → egg → adult'], 'C', 'This is complete metamorphosis.', 1],
        ['The housefly spreads diseases such as typhoid by', ['stinging', 'biting people', 'laying eggs in blood', 'carrying germs on its legs and body from filth to food'], 'D', 'It is a mechanical vector: it carries germs but is not a host.', 2],
        ['The grain weevil is a pest because it', ['pollinates crops', 'damages stored cereal grains', 'eats other pests', 'produces honey'], 'B', 'Larvae feed inside the grains, spoiling stored maize and rice.', 1],
        ['In a honeybee colony, the sterile females that collect nectar and care for the young are the', ['queens', 'drones', 'workers', 'larvae'], 'C', 'Drones are males; the queen lays the eggs.', 2],
    ],

    'SHS 2: Habitats and adaptations' => [
        ['Mangrove trees in estuaries have pneumatophores, which are', ['flowers', 'leaves that store water', 'spines', 'roots that grow upwards to take in air'], 'D', 'The waterlogged mud has little oxygen, so these breathing roots grow up into the air.', 3],
        ['Cactus leaves are reduced to spines mainly to', ['attract insects', 'reduce water loss', 'store food', 'make more seeds'], 'B', 'Desert plants (xerophytes) have features that save water.', 1],
        ['Plants such as orchids that grow on other plants to reach light in a rainforest are', ['saprophytes', 'parasites', 'epiphytes', 'xerophytes'], 'C', 'Epiphytes use their host only for support.', 2],
        ['A typical feature of the savannah is', ['very heavy rainfall all year', 'tall, dense evergreen forest', 'permanent ice', 'grassland with scattered trees and a long dry season'], 'D', 'Northern Ghana has savannah vegetation.', 1],
        ['Fish are adapted for breathing in water by having', ['lungs', 'gills', 'spiracles', 'stomata'], 'B', 'Gills have a large surface area and a rich blood supply for gaseous exchange.', 1],
    ],

    'SHS 2: Immunisation and vaccination' => [
        ['A vaccine usually contains', ['red blood cells', 'antibiotics', 'weakened, killed or parts of a pathogen', 'vitamins only'], 'C', 'It trains the immune system to recognise the real pathogen.', 1],
        ['Injecting ready-made antibodies, such as antivenom after a snakebite, gives', ['no protection', 'active immunity', 'lifelong immunity', 'passive immunity'], 'D', 'Passive immunity works at once but does not last.', 2],
        ['The BCG vaccine protects against', ['malaria', 'tuberculosis', 'measles', 'cholera'], 'B', 'BCG is given to babies in Ghana soon after birth.', 2],
    ],

    'SHS 3: Biotechnology in everyday life' => [
        ['Bread dough rises because yeast', ['produces salt', 'adds oxygen', 'produces carbon dioxide during fermentation', 'kills bacteria'], 'C', 'The gas bubbles are trapped in the dough.', 1],
        ['Growing many identical plants from small pieces of tissue in a sterile nutrient medium is', ['crop rotation', 'cross-pollination', 'grafting', 'tissue culture'], 'D', 'It is used to produce disease-free plantain and cassava plantlets.', 2],
        ['Human insulin for diabetics is now produced by bacteria using', ['fermentation of cassava', 'genetic engineering (recombinant DNA technology)', 'tissue culture', 'selective breeding'], 'B', 'The human insulin gene is inserted into bacterial DNA.', 2],
        ['In treating drinking water, alum is added mainly to', ['add flavour', 'kill all germs', 'make suspended particles clump and settle', 'raise the temperature'], 'C', 'This is coagulation; chlorination later kills germs.', 2],
    ],

    'SHS 3: Inheritance and blood groups' => [
        ['Mendel\'s first law, the law of segregation, states that', ['all offspring look like one parent', 'genes for different traits are always linked', 'acquired traits are inherited', 'the two alleles of a gene separate into different gametes'], 'D', 'Each gamete carries only one allele of each gene.', 2],
        ['A monohybrid cross between two heterozygous tall plants (Tt × Tt) gives a phenotype ratio of', ['1 : 1', '3 tall : 1 short', '1 : 2 : 1 tall', 'all tall'], 'B', 'TT, Tt, Tt and tt: three show the dominant trait.', 2],
        ['A test cross is made with an individual that is', ['heterozygous', 'homozygous dominant', 'homozygous recessive', 'a different species'], 'C', 'The offspring show whether the unknown parent is TT or Tt.', 3],
        ['A father with blood group A (genotype AO) and a mother with group B (BO) can have children with blood groups', ['O only', 'A or B only', 'AB only', 'A, B, AB or O'], 'D', 'The cross AO × BO gives AB, AO, BO and OO.', 3],
        ['In the ABO blood group system, alleles A and B are', ['codominant', 'recessive to O', 'both recessive', 'sex-linked'], 'A', 'Both show in a person with genotype AB.', 3],
    ],

    'SHS 3: Variation and evolution' => [
        ['Height in humans is an example of', ['continuous variation', 'discontinuous variation', 'no variation', 'a blood group'], 'A', 'Height takes any value within a range; blood group falls into distinct classes.', 2],
        ['Which is an example of discontinuous variation?', ['ABO blood group', 'body mass', 'height', 'hand span'], 'A', 'A person is either A, B, AB or O, with no values in between.', 2],
        ['Charles Darwin explained evolution by', ['natural selection', 'the inheritance of acquired characteristics', 'spontaneous generation', 'special creation only'], 'A', 'Individuals best suited to their environment survive and pass on their genes.', 1],
        ['Fossils provide evidence for evolution because they', ['show how organisms have changed over long periods', 'are always living', 'are made of plastic', 'never change'], 'A', 'Older rock layers hold simpler forms of life.', 1],
    ],

    'SHS 3: Cell division' => [
        ['Mitosis produces', ['two cells genetically identical to the parent cell', 'four haploid cells', 'gametes only', 'cells with no nucleus'], 'A', 'Mitosis is used for growth and repair.', 2],
        ['Meiosis produces', ['four haploid cells that are genetically different', 'two identical diploid cells', 'one large cell', 'only skin cells'], 'A', 'Meiosis forms gametes with half the chromosome number.', 2],
        ['Crossing over, which mixes genes between chromosomes, happens during', ['meiosis', 'mitosis', 'fertilisation', 'binary fission'], 'A', 'It takes place in prophase I and adds to variation.', 3],
        ['A human sperm cell contains how many chromosomes?', ['23', '46', '92', '22'], 'A', 'Gametes are haploid; fertilisation restores 46.', 1],
    ],

    'SHS 3: Tilapia, frog, wall gecko and domestic fowl' => [
        ['The tadpole of a frog changes into an adult through', ['metamorphosis', 'mitosis only', 'pollination', 'hibernation'], 'A', 'The tadpole loses its tail and gills and develops legs and lungs.', 1],
        ['The lateral line of a tilapia helps it to', ['detect vibrations and movement in water', 'breathe', 'digest food', 'lay eggs'], 'A', 'It is a sense organ running along each side of the body.', 2],
        ['The wall gecko can walk on walls and ceilings because of', ['special pads on its toes', 'wings', 'suction from its mouth', 'a long tail'], 'A', 'Tiny hair-like structures on the pads grip surfaces.', 2],
        ['In the domestic fowl, food is ground up in the', ['gizzard', 'crop', 'beak', 'cloaca'], 'A', 'The muscular gizzard, often with grit, grinds the food; the crop stores it.', 2],
    ],

    'SHS 3: Health: BMI and emerging diseases' => [
        ['A learner has a mass of 70 kg and a height of 1.75 m. What is the BMI?', ['22.9', '40.0', '25.0', '17.5'], 'A', 'BMI = 70 ÷ 1.75² = 70 ÷ 3.0625 ≈ 22.9, which is in the healthy range (18.5–24.9).', 3],
        ['COVID-19 is caused by', ['a coronavirus (SARS-CoV-2)', 'a bacterium', 'a fungus', 'a protozoan'], 'A', 'It spreads mainly through droplets and aerosols from infected people.', 1],
        ['A disease that passes from animals to humans, such as Ebola, is called', ['a zoonosis', 'a deficiency disease', 'a hereditary disease', 'an allergy'], 'A', 'Many emerging diseases are zoonoses.', 2],
    ],

    'SHS 3: Mammalian body systems' => [
        ['The hormone that lowers blood sugar is', ['insulin', 'glucagon', 'adrenaline', 'thyroxine'], 'A', 'Insulin from the pancreas makes cells take up glucose; glucagon raises blood sugar.', 2],
        ['Adrenaline prepares the body for', ['"fight or flight"', 'sleep', 'digestion', 'growth only'], 'A', 'It raises heart rate, breathing rate and blood sugar.', 1],
        ['The male sex hormone produced by the testes is', ['testosterone', 'oestrogen', 'progesterone', 'insulin'], 'A', 'It controls male secondary sexual characteristics.', 1],
        ['A freely movable joint such as the knee, lubricated by a fluid, is a', ['synovial joint', 'fixed joint', 'cartilaginous joint', 'suture'], 'A', 'Synovial fluid reduces friction between the bones.', 2],
        ['Breathing in happens when the diaphragm', ['contracts and the ribs move up and out', 'relaxes and the ribs move down', 'stops moving', 'pushes air out'], 'A', 'The chest volume increases and the pressure inside falls.', 2],
    ],

    'SHS 3: Reproduction in flowering plants' => [
        ['Growing new cassava plants from stem cuttings is an example of', ['vegetative propagation', 'pollination', 'germination by seed', 'fertilisation'], 'A', 'The new plants are genetically identical to the parent.', 1],
        ['Insect-pollinated flowers usually have', ['bright petals, scent and nectar', 'small dull petals and no scent', 'feathery stigmas outside the flower', 'huge amounts of light pollen'], 'A', 'These features attract insects; wind-pollinated flowers are dull with feathery stigmas.', 2],
        ['Ginger reproduces naturally by means of', ['rhizomes (underground stems)', 'bulbs', 'runners', 'seeds only'], 'A', 'Each piece of rhizome with a bud can grow into a new plant.', 2],
    ],
];
