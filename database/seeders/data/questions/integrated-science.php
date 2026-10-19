<?php

/*
| SHS Integrated Science: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers. Themes: diversity of matter, cycles, systems, energy, interactions of matter.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Matter' => [
        ['An atom has 11 protons and 12 neutrons. What is its mass number?', ['11', '12', '23', '1'], 'C', 'Mass number = protons + neutrons = 11 + 12 = 23.', 2],
        ['The electronic configuration of an element with atomic number 12 is', ['2, 8, 2', '2, 10', '2, 8, 1, 1', '2, 2, 8'], 'A', 'Fill shells 2, then 8, then the remaining 2: 2, 8, 2.', 2],
        ['Isotopes of an element have the same number of', ['neutrons', 'protons', 'nucleons', 'mass number'], 'B', 'Isotopes have the same number of protons but different numbers of neutrons.', 2],
        ['Which of the following is a physical change?', ['burning of paper', 'rusting of iron', 'melting of ice', 'souring of milk'], 'C', 'Melting changes only the state; no new substance forms.', 1],
        ['The process used to obtain pure water from sea water is', ['filtration', 'distillation', 'decantation', 'chromatography'], 'B', 'Distillation boils the water and condenses the vapour, leaving the salts behind.', 2],
        ['Paper chromatography is used to separate', ['sand and water', 'the dyes in ink', 'iron and sulphur', 'oil and water'], 'B', 'Different dyes travel different distances up the paper.', 2],
        ['Which gas turns lime water milky?', ['oxygen', 'hydrogen', 'carbon dioxide', 'nitrogen'], 'C', 'CO₂ reacts with calcium hydroxide to form insoluble calcium carbonate.', 1],
        ['A substance that speeds up a chemical reaction without itself being used up is a', ['catalyst', 'reactant', 'product', 'solvent'], 'A', 'A catalyst lowers the activation energy and is unchanged at the end.', 2],
        ['The reaction between an acid and a base to form salt and water is called', ['oxidation', 'neutralisation', 'decomposition', 'fermentation'], 'B', 'Acid + base → salt + water is neutralisation.', 1],
    ],

    'Cycles' => [
        ['The process by which nitrogen gas in the air is changed into nitrates in the soil by bacteria is', ['denitrification', 'nitrogen fixation', 'ammonification', 'transpiration'], 'B', 'Nitrogen-fixing bacteria such as Rhizobium in legume root nodules convert N₂ into nitrogen compounds.', 3],
        ['Which organisms break down dead plants and animals and return nutrients to the soil?', ['producers', 'decomposers', 'herbivores', 'carnivores'], 'B', 'Decomposers such as bacteria and fungi recycle nutrients.', 1],
        ['In the water cycle, water vapour cools to form clouds by', ['evaporation', 'condensation', 'precipitation', 'infiltration'], 'B', 'Cooling vapour condenses into tiny droplets that form clouds.', 1],
        ['The first stage in the germination of a seed is the uptake of water called', ['imbibition', 'osmosis only', 'transpiration', 'pollination'], 'A', 'A dry seed absorbs water (imbibition), which activates its enzymes.', 3],
        ['The type of reproduction that involves only one parent is', ['sexual reproduction', 'asexual reproduction', 'fertilisation', 'pollination'], 'B', 'Asexual reproduction (budding, fission, vegetative propagation) needs only one parent.', 1],
    ],

    'Systems' => [
        ['The enzyme in saliva that digests starch is', ['pepsin', 'amylase', 'lipase', 'trypsin'], 'B', 'Salivary amylase breaks starch down into maltose.', 2],
        ['Which part of the human eye controls the amount of light entering it?', ['retina', 'cornea', 'lens', 'iris'], 'D', 'The iris changes the size of the pupil.', 2],
        ['Insulin is produced by the', ['liver', 'thyroid gland', 'kidney', 'pancreas'], 'D', 'Insulin from the pancreas lowers blood sugar.', 2],
        ['The red blood cells transport oxygen because they contain', ['plasma', 'antibodies', 'platelets', 'haemoglobin'], 'D', 'Haemoglobin binds oxygen in the lungs and releases it in tissues.', 1],
        ['Which blood group is known as the universal donor?', ['A', 'B', 'AB', 'O'], 'D', 'Group O red cells have no A or B antigens, so they can be given to any ABO group.', 3],
        ['The male reproductive cell is the', ['sperm', 'ovum', 'zygote', 'embryo'], 'A', 'Sperm is the male gamete; the ovum is the female gamete.', 1],
    ],

    'Energy and forces' => [
        ['A body of mass 10 kg is lifted through a height of 2 m. Calculate the potential energy gained. (g = 10 m/s²)', ['20 J', '100 J', '200 J', '2000 J'], 'C', 'PE = mgh = 10 × 10 × 2 = 200 J.', 2],
        ['Calculate the power of a machine that does 600 J of work in 20 s.', ['30 W', '12 W', '580 W', '12,000 W'], 'A', 'Power = work ÷ time = 600 ÷ 20 = 30 W.', 2],
        ['Three resistors of 2 Ω, 3 Ω and 5 Ω are connected in series. Find the total resistance.', ['1 Ω', '5 Ω', '10 Ω', '30 Ω'], 'C', 'In series, R = 2 + 3 + 5 = 10 Ω.', 2],
        ['Two 6 Ω resistors are connected in parallel. Find the total resistance.', ['3 Ω', '6 Ω', '12 Ω', '36 Ω'], 'A', '1/R = 1/6 + 1/6 = 1/3, so R = 3 Ω.', 3],
        ['The device used to convert mechanical energy into electrical energy is a', ['motor', 'battery', 'transformer', 'generator'], 'D', 'A generator (dynamo) changes mechanical energy into electricity. A motor does the reverse.', 2],
        ['Sound cannot travel through', ['water', 'air', 'steel', 'a vacuum'], 'D', 'Sound needs a medium; there are no particles in a vacuum to vibrate.', 1],
        ['The bending of light as it passes from air into water is called', ['reflection', 'diffraction', 'refraction', 'dispersion'], 'C', 'Light changes speed and direction at the boundary: refraction.', 2],
        ['The splitting of white light into its seven colours by a prism is called', ['dispersion', 'refraction', 'reflection', 'interference'], 'A', 'Dispersion separates white light into a spectrum.', 2],
    ],

    'Agriculture, health and the environment' => [
        ['Which of the following is a method of controlling soil erosion?', ['continuous cropping', 'bush burning', 'contour ploughing', 'overgrazing'], 'C', 'Ploughing along contours slows water running down slopes.', 2],
        ['Which of the following is a vector of the disease river blindness (onchocerciasis)?', ['housefly', 'Anopheles mosquito', 'tsetse fly', 'blackfly (Simulium)'], 'D', 'River blindness is spread by blackflies that breed in fast-flowing rivers.', 3],
        ['Sleeping sickness is spread by the', ['tsetse fly', 'Aedes mosquito', 'housefly', 'blackfly'], 'A', 'The tsetse fly transmits Trypanosoma, which causes sleeping sickness.', 2],
        ['Cholera is mainly spread through', ['mosquito bites', 'shaking hands', 'contaminated food and water', 'the air'], 'C', 'Cholera bacteria spread through food and water contaminated with faeces.', 1],
        ['The build-up of gases such as carbon dioxide that trap heat in the atmosphere causes', ['acid rain only', 'earthquakes', 'the ozone layer to thicken', 'the greenhouse effect and global warming'], 'D', 'Greenhouse gases trap infrared radiation and warm the earth.', 2],
        ['Which of the following is a biological method of pest control?', ['using ladybirds to eat aphids', 'spraying insecticides', 'burning the farm', 'hand-picking only'], 'A', 'Biological control uses natural enemies of the pest.', 2],
    ],

    'SHS 1: The nature and processes of science' => [
        ['A testable explanation that a scientist proposes before doing an experiment is', ['a law', 'an observation', 'a conclusion', 'a hypothesis'], 'D', 'A hypothesis is an educated guess that an experiment can support or reject.', 1],
        ['In an experiment on how fertiliser affects maize growth, the amount of fertiliser is the', ['constant', 'dependent variable', 'control', 'independent variable'], 'D', 'The independent variable is the one the scientist changes on purpose. Plant growth, which is measured, is the dependent variable.', 2],
        ['Why do scientists repeat an experiment several times?', ['to use up the materials', 'to avoid recording data', 'to change the hypothesis each time', 'to make the results more reliable'], 'D', 'Repeating shows whether results are consistent and reduces the effect of chance errors.', 1],
        ['In a fair test, the set-up that is left without the factor being tested is the', ['variable', 'hypothesis', 'control', 'theory'], 'C', 'The control gives a baseline to compare the treated set-ups with.', 2],
        ['Which of these is a quantitative observation?', ['the solution turned blue', 'the leaf feels rough', 'the water boiled at 100 °C', 'the gas smells sharp'], 'C', 'A quantitative observation includes a measured number. The others describe qualities.', 1],
    ],

    'SHS 1: Solids, binary compounds and their names' => [
        ['What is the chemical name of the binary compound NaCl?', ['sodium chlorate', 'sodium hydroxide', 'sodium chlorine', 'sodium chloride'], 'D', 'A compound of a metal and one non-metal takes the ending -ide: sodium chloride.', 1],
        ['What is the formula of magnesium oxide?', ['Mg₂O₃', 'Mg₂O', 'MgO₂', 'MgO'], 'D', 'Mg²⁺ and O²⁻ have equal and opposite charges, so they combine one to one.', 2],
        ['Which property makes metals suitable for electrical wires?', ['brittleness', 'low melting point', 'good electrical conductivity', 'dull surface'], 'C', 'Metals have free electrons, so they conduct electricity well. Copper is the usual choice for wiring.', 1],
        ['Which of these solids is a non-metal?', ['copper', 'iron', 'aluminium', 'sulphur'], 'D', 'Sulphur is a brittle yellow non-metal that does not conduct electricity.', 1],
        ['A compound made of exactly two elements is called', ['a binary compound', 'an alloy', 'a mixture', 'an isotope'], 'A', 'Water (H₂O) and sodium chloride (NaCl) are binary compounds.', 1],
    ],

    'SHS 1: Diffusion and osmosis' => [
        ['The spreading of particles from where they are many to where they are few is', ['osmosis', 'transpiration', 'evaporation', 'diffusion'], 'D', 'Diffusion is the net movement of particles down a concentration gradient.', 1],
        ['Osmosis is the movement of', ['salt through any membrane', 'water from a concentrated to a dilute solution', 'gases from the lungs to the blood', 'water through a partially permeable membrane from a dilute to a more concentrated solution'], 'D', 'In osmosis water moves from where there is more water (dilute) to where there is less (concentrated).', 2],
        ['A slice of pawpaw placed in strong salt solution becomes soft and limp because', ['it gains salt and water', 'its cells divide', 'it gains water by osmosis', 'it loses water by osmosis'], 'D', 'The salt solution is more concentrated than the cell sap, so water leaves the cells.', 2],
        ['Salting fish helps to preserve it because', ['salt adds vitamins', 'salt cooks the fish', 'salt kills only flies', 'salt draws water out of microbes by osmosis, so they cannot grow'], 'D', 'Bacteria and fungi lose water to the salty surroundings and cannot survive.', 2],
    ],

    'SHS 1: Reproduction in plants and humans' => [
        ['The male part of a flower that produces pollen is the', ['stigma', 'ovary', 'anther', 'style'], 'C', 'The anther, held on the filament, makes pollen. Together they form the stamen.', 1],
        ['The transfer of pollen from an anther to a stigma is', ['fertilisation', 'dispersal', 'germination', 'pollination'], 'D', 'Pollination comes first. Fertilisation happens later, when the male and female gametes fuse.', 1],
        ['After fertilisation, the ovary of a flower develops into the', ['seed', 'root', 'petal', 'fruit'], 'D', 'The ovary becomes the fruit and the ovules inside it become the seeds.', 1],
        ['In humans, fertilisation normally takes place in the', ['uterus', 'ovary', 'vagina', 'oviduct (fallopian tube)'], 'D', 'The sperm meets the egg in the oviduct. The embryo then implants in the uterus.', 2],
        ['Which structure supplies the human foetus with food and oxygen from the mother?', ['amniotic fluid', 'oviduct', 'cervix', 'placenta'], 'D', 'The placenta exchanges food, oxygen and wastes between the mother\'s blood and the foetus\'s blood.', 2],
    ],

    'SHS 1: Solar energy and solar panels' => [
        ['A solar panel changes', ['heat energy into sound', 'chemical energy into heat', 'electrical energy into light', 'light energy into electrical energy'], 'D', 'Photovoltaic cells in the panel produce electricity when light falls on them.', 1],
        ['Solar cells are usually made from the semiconductor', ['copper', 'plastic', 'iron', 'silicon'], 'D', 'Silicon is the most common material for solar cells.', 1],
        ['In a solar home system, the battery is used to', ['store energy for use at night or on cloudy days', 'make the panel hotter', 'change DC into sound', 'cool the inverter'], 'A', 'Panels only produce power in daylight, so a battery stores energy for later.', 1],
        ['A solar panel gives the most power when it', ['is covered in dust', 'is kept wet', 'is placed indoors', 'faces the sun directly with no shade'], 'D', 'Shade, dust and a poor angle all reduce the light reaching the cells.', 1],
    ],

    'SHS 1: Force' => [
        ['Force is measured in which SI unit?', ['joule', 'watt', 'newton', 'pascal'], 'C', 'Force is measured in newtons (N). 1 N gives a 1 kg mass an acceleration of 1 m/s².', 1],
        ['What is the weight of a 50 kg student? (Take g = 10 N/kg.)', ['5 N', '50 N', '500 N', '5,000 N'], 'C', 'Weight = mass × g = 50 × 10 = 500 N.', 1],
        ['Friction always acts', ['in the direction of motion', 'upwards', 'against the direction of motion', 'only on wet surfaces'], 'C', 'Friction opposes the movement, or the attempted movement, of surfaces against each other.', 1],
        ['Which instrument measures force?', ['thermometer', 'barometer', 'ammeter', 'spring balance (newton meter)'], 'D', 'A spring balance stretches in proportion to the force applied.', 1],
    ],

    'SHS 1: Basic electronics' => [
        ['Which component lets current flow in one direction only?', ['resistor', 'capacitor', 'diode', 'switch'], 'C', 'A diode conducts in its forward direction and blocks current in reverse.', 1],
        ['LED stands for', ['light-emitting diode', 'low-energy device', 'light electric detector', 'long electron diode'], 'A', 'An LED is a diode that gives out light when current flows through it.', 1],
        ['In a circuit, a resistor is used to', ['store charge', 'switch the circuit off', 'produce light', 'limit the current'], 'D', 'A resistor opposes current, for example to protect an LED from too much current.', 1],
        ['Silicon and germanium are examples of', ['conductors', 'insulators', 'semiconductors', 'magnets'], 'C', 'Semiconductors conduct better than insulators but worse than metals; their conduction can be controlled.', 1],
    ],

    'SHS 1: Promoting health and safety' => [
        ['The first thing to do for a person with a minor burn is to', ['apply butter', 'burst any blisters', 'cool it under clean running water', 'cover it with cotton wool'], 'C', 'Cool running water for several minutes reduces heat damage and pain.', 1],
        ['Which hazard symbol warns that a substance can catch fire easily?', ['a skull and crossbones', 'an exclamation mark', 'a corroded hand', 'a flame'], 'D', 'The flame symbol marks flammable substances.', 1],
        ['Washing hands with soap mainly helps to', ['remove germs and prevent the spread of disease', 'make the skin darker', 'cure malaria', 'increase body temperature'], 'A', 'Soap lifts dirt and germs off the skin so that water can rinse them away.', 1],
        ['Which practice reduces the risk of road accidents for pedestrians?', ['crossing at a zebra crossing after checking both ways', 'walking in the middle of the road', 'crossing behind a parked bus', 'using a phone while crossing'], 'A', 'Use marked crossings and look both ways before crossing.', 1],
    ],

    'SHS 1: Science in local industry' => [
        ['Making soap from fat or oil and an alkali is called', ['fermentation', 'distillation', 'saponification', 'neutralisation only'], 'C', 'In saponification an alkali such as caustic soda or potash breaks fats into soap and glycerol.', 2],
        ['Local "alata samina" (black soap) gets its alkali from', ['ash from burnt plant materials such as cocoa pods and plantain peels', 'table salt', 'vinegar', 'sugar'], 'A', 'The ash contains potassium compounds that act as the alkali.', 2],
        ['In processing cassava into gari, the grated cassava is fermented and pressed mainly to', ['add colour', 'add vitamins', 'increase the starch', 'remove water and reduce the poisonous cyanide content'], 'D', 'Fermentation and pressing lower the cyanide in cassava and remove water before roasting.', 3],
        ['Soft soap is usually made with', ['potassium hydroxide', 'sodium chloride', 'sulphuric acid', 'calcium carbonate'], 'A', 'Potash (KOH) gives soft or liquid soaps; caustic soda (NaOH) gives hard bar soap.', 3],
    ],

    'SHS 2: Acids, bases and salts' => [
        ['A sample of lemon juice has a pH of 3. The juice is', ['strongly alkaline', 'neutral', 'acidic', 'weakly alkaline'], 'C', 'pH values below 7 are acidic, 7 is neutral and above 7 is alkaline.', 1],
        ['Which of these is a base used in the home?', ['vinegar', 'lemon juice', 'baking soda', 'orange juice'], 'C', 'Baking soda (sodium hydrogencarbonate) is basic. Vinegar and citrus juices are acidic.', 1],
        ['An acid reacts with a base to form', ['a salt and water', 'only hydrogen gas', 'an oxide and oxygen', 'a metal and acid'], 'A', 'This reaction is neutralisation: acid + base → salt + water.', 1],
        ['Blue litmus paper turns red in', ['an acid', 'a base', 'pure water', 'salt solution'], 'A', 'Acids turn blue litmus red. Bases turn red litmus blue.', 1],
        ['Indigestion tablets often contain a mild base because', ['they neutralise excess stomach acid', 'they add acid to the stomach', 'they kill all bacteria', 'they increase the pH of blood to 14'], 'A', 'Antacids such as magnesium hydroxide neutralise excess hydrochloric acid in the stomach.', 2],
    ],

    'SHS 2: Human excretory organs' => [
        ['Which organ removes urea from the blood and produces urine?', ['liver', 'lung', 'kidney', 'heart'], 'C', 'The kidneys filter the blood and excrete urea in urine.', 1],
        ['The skin excretes water, salts and a little urea in', ['tears', 'bile', 'saliva', 'sweat'], 'D', 'Sweat glands remove some wastes as they help to cool the body.', 1],
        ['The lungs excrete', ['urea', 'bile pigments', 'carbon dioxide and water vapour', 'excess salt'], 'C', 'Carbon dioxide from respiration leaves the body when we breathe out.', 1],
        ['Urea is made in the', ['kidney', 'pancreas', 'bladder', 'liver'], 'D', 'The liver breaks down excess amino acids and forms urea, which the blood carries to the kidneys.', 2],
    ],

    'SHS 2: Gaseous exchange in humans' => [
        ['Gaseous exchange in the human lungs takes place in the', ['trachea', 'bronchi', 'alveoli', 'larynx'], 'C', 'The alveoli have thin, moist walls and a rich blood supply.', 1],
        ['When we breathe in, the diaphragm', ['contracts and flattens', 'relaxes and domes upwards', 'stays still', 'moves the ribs down'], 'A', 'The flattening diaphragm and rising ribs enlarge the chest, so air rushes in.', 2],
        ['Compared with inhaled air, exhaled air contains', ['more oxygen', 'less water vapour', 'more carbon dioxide', 'less nitrogen only'], 'C', 'Body cells use oxygen and produce carbon dioxide, which is breathed out.', 1],
        ['Which disease mainly damages the alveoli and is strongly linked with smoking?', ['malaria', 'scurvy', 'cholera', 'emphysema'], 'D', 'Emphysema breaks down alveolar walls, reducing the surface area for gaseous exchange.', 2],
    ],

    'SHS 2: Concepts of electricity' => [
        ['A current of 2 A flows through a 6 Ω resistor. What is the voltage across it?', ['3 V', '8 V', '12 V', '4 V'], 'C', 'V = IR = 2 × 6 = 12 V.', 2],
        ['Electric current is measured with', ['a voltmeter', 'a thermometer', 'an ammeter', 'a galvanometer only'], 'C', 'An ammeter is connected in series to measure current in amperes.', 1],
        ['A 100 W bulb is used for 5 hours. How much energy does it use?', ['0.5 kWh', '20 kWh', '500 kWh', '5 kWh'], 'A', 'Energy = 0.1 kW × 5 h = 0.5 kWh.', 2],
        ['Two 4 Ω resistors are connected in series. What is their total resistance?', ['2 Ω', '4 Ω', '8 Ω', '16 Ω'], 'C', 'In series, resistances add: 4 + 4 = 8 Ω.', 1],
        ['Which material is a good electrical insulator?', ['copper', 'graphite', 'aluminium', 'rubber'], 'D', 'Rubber has no free electrons, so it is used to cover wires.', 1],
    ],

    'SHS 2: Buoyancy' => [
        ['The upward force a fluid exerts on an object placed in it is called', ['weight', 'friction', 'upthrust (buoyant force)', 'tension'], 'C', 'Upthrust is the force that makes objects feel lighter in water.', 1],
        ['Archimedes\' principle states that the upthrust on an object in a fluid equals', ['the object\'s mass', 'the weight of fluid displaced', 'the volume of the object', 'the depth of the fluid'], 'B', 'The upthrust equals the weight of the fluid the object pushes aside.', 2],
        ['An object weighs 10 N in air and 6 N in water. What is the upthrust on it?', ['16 N', '4 N', '6 N', '60 N'], 'B', 'Upthrust = weight in air − apparent weight in water = 10 − 6 = 4 N.', 2],
        ['A steel ship floats although steel is denser than water because', ['steel becomes lighter in water', 'its hollow shape displaces a weight of water equal to its own weight', 'water pushes it from the sides', 'the engine keeps it up'], 'B', 'The hollow hull holds a lot of air, so the ship displaces enough water to balance its weight.', 3],
    ],

    'SHS 2: Semiconductors and electronic devices' => [
        ['Adding small amounts of impurities to a semiconductor to change its conductivity is called', ['doping', 'plating', 'annealing', 'galvanising'], 'A', 'Doping creates n-type or p-type material.', 2],
        ['A transistor can be used as', ['a switch or an amplifier', 'a fuse', 'a battery', 'a resistor only'], 'A', 'A small base current controls a larger current, so the transistor can switch or amplify.', 2],
        ['A light-dependent resistor (LDR) has a resistance that', ['rises as light gets brighter', 'falls as light gets brighter', 'never changes', 'depends only on temperature'], 'B', 'More light frees more charge carriers, so the resistance falls.', 2],
    ],

    'SHS 2: Pathogenic diseases' => [
        ['Malaria is caused by', ['a virus', 'a bacterium', 'Plasmodium, a protozoan', 'a fungus'], 'C', 'The Plasmodium parasite is carried by female Anopheles mosquitoes.', 1],
        ['Which disease is caused by a virus?', ['tuberculosis', 'cholera', 'measles', 'typhoid'], 'C', 'Measles is viral. Tuberculosis, cholera and typhoid are bacterial.', 2],
        ['Sleeping nets treated with insecticide mainly prevent', ['cholera', 'malaria', 'tuberculosis', 'ringworm'], 'B', 'They keep mosquitoes away while people sleep.', 1],
        ['Ringworm is caused by', ['a worm', 'a fungus', 'a virus', 'a protozoan'], 'B', 'Despite its name, ringworm is a fungal skin infection.', 2],
    ],

    'SHS 2: Indigenous beverages' => [
        ['Palm wine becomes alcoholic because', ['yeasts ferment its sugars into alcohol', 'it is boiled', 'salt is added', 'it is exposed to sunlight'], 'A', 'Yeasts in the sap use the sugar and produce ethanol and carbon dioxide.', 1],
        ['The gas given off when yeast ferments sugar is', ['oxygen', 'nitrogen', 'carbon dioxide', 'hydrogen'], 'C', 'Fermentation: glucose → ethanol + carbon dioxide.', 1],
        ['"Sobolo" is a local drink made mainly from', ['hibiscus (roselle) calyces', 'cocoa beans', 'millet only', 'palm sap'], 'A', 'Sobolo is brewed from dried hibiscus calyces, often with ginger.', 1],
    ],

    'SHS 3: The composition of air' => [
        ['Which gas makes up about 78% of the air?', ['oxygen', 'nitrogen', 'carbon dioxide', 'argon'], 'B', 'Air is about 78% nitrogen, 21% oxygen and 1% argon, with small amounts of carbon dioxide and other gases.', 1],
        ['About what percentage of air is oxygen?', ['78%', '1%', '21%', '0.04%'], 'C', 'Oxygen is about one-fifth of the air.', 1],
        ['Which gas in air is used by plants for photosynthesis?', ['nitrogen', 'oxygen', 'carbon dioxide', 'argon'], 'C', 'Plants take in carbon dioxide and give out oxygen during photosynthesis.', 1],
        ['Limewater turns milky when it reacts with', ['oxygen', 'carbon dioxide', 'nitrogen', 'water vapour'], 'B', 'Carbon dioxide forms insoluble calcium carbonate with limewater.', 1],
    ],

    'SHS 3: Oil and natural gas' => [
        ['Crude oil is separated into fractions such as petrol and diesel by', ['filtration', 'fractional distillation', 'evaporation', 'chromatography'], 'B', 'The fractions have different boiling points, so they condense at different heights in a column.', 2],
        ['The main gas in natural gas is', ['methane', 'butane', 'hydrogen', 'oxygen'], 'A', 'Natural gas is mostly methane (CH₄).', 1],
        ['Ghana\'s first major offshore oil field, which started production in 2010, is the', ['Jubilee field', 'Volta field', 'Ashanti field', 'Keta field'], 'A', 'The Jubilee field, off the Western Region, began producing in December 2010.', 2],
        ['Complete burning of methane produces', ['carbon monoxide only', 'carbon dioxide and water', 'hydrogen and oxygen', 'soot only'], 'B', 'CH₄ + 2O₂ → CO₂ + 2H₂O.', 2],
    ],

    'SHS 3: The nervous system' => [
        ['The basic unit of the nervous system is the', ['nephron', 'neurone', 'alveolus', 'villus'], 'B', 'Neurones (nerve cells) carry electrical impulses.', 1],
        ['The central nervous system is made up of the', ['brain and spinal cord', 'heart and lungs', 'nerves of the arms', 'eyes and ears'], 'A', 'The brain and spinal cord process information. Nerves connect them to the rest of the body.', 1],
        ['Quickly pulling your hand away from a hot pot is an example of', ['a voluntary action', 'a reflex action', 'a learned skill', 'a hormone response'], 'B', 'A reflex is fast and automatic, and the spinal cord handles it before the brain is aware.', 1],
        ['The gap between two neurones is a', ['synapse', 'axon', 'dendrite', 'nucleus'], 'A', 'Chemicals cross the synapse to pass the impulse to the next neurone.', 2],
    ],

    'SHS 3: Movement of the human body' => [
        ['The joint at the knee is a', ['ball and socket joint', 'hinge joint', 'pivot joint', 'fixed joint'], 'B', 'The knee moves in one plane, like a door hinge.', 1],
        ['Muscles are joined to bones by', ['ligaments', 'tendons', 'cartilage', 'nerves'], 'B', 'Tendons attach muscle to bone. Ligaments join bone to bone.', 1],
        ['When the biceps contracts, the arm', ['straightens at the elbow', 'bends at the elbow', 'rotates at the shoulder only', 'stays still'], 'B', 'The biceps and triceps work as an antagonistic pair: biceps bends, triceps straightens.', 1],
        ['Which joint allows the head to turn from side to side?', ['hinge joint', 'pivot joint', 'gliding joint', 'fixed joint'], 'B', 'The pivot joint between the top two vertebrae allows rotation.', 2],
    ],

    'SHS 3: Light energy' => [
        ['When light is reflected from a plane mirror, the angle of reflection', ['is twice the angle of incidence', 'equals the angle of incidence', 'is always 90°', 'is half the angle of incidence'], 'B', 'This is the law of reflection.', 1],
        ['A pencil standing in a glass of water looks bent at the surface. This is caused by', ['reflection', 'refraction', 'diffraction', 'dispersion'], 'B', 'Light changes speed between media and bends.', 1],
        ['White light splits into colours when it passes through a prism. This is', ['dispersion', 'reflection', 'absorption', 'polarisation'], 'A', 'Each colour bends by a different amount, so they spread out into a spectrum.', 2],
        ['A short-sighted person sees distant objects clearly with', ['a concave (diverging) lens', 'a convex (converging) lens', 'a plane mirror', 'a prism'], 'A', 'A diverging lens moves the image back onto the retina for distant objects.', 3],
    ],

    'SHS 3: Forces and motion' => [
        ['A car increases its speed from 10 m/s to 30 m/s in 5 s. What is its acceleration?', ['2 m/s²', '4 m/s²', '6 m/s²', '8 m/s²'], 'B', 'a = (30 − 10) ÷ 5 = 4 m/s².', 2],
        ['A force of 20 N acts on a 4 kg mass. What is the acceleration?', ['80 m/s²', '5 m/s²', '24 m/s²', '0.2 m/s²'], 'B', 'a = F ÷ m = 20 ÷ 4 = 5 m/s².', 2],
        ['Passengers lurch forward when a bus brakes suddenly. This is because of', ['inertia', 'upthrust', 'friction', 'gravity only'], 'A', 'Their bodies tend to keep moving forward (Newton\'s first law).', 1],
        ['A runner covers 400 m in 80 s. What is the average speed?', ['5 m/s', '320 m/s', '0.2 m/s', '480 m/s'], 'A', 'Speed = 400 ÷ 80 = 5 m/s.', 1],
    ],

    'SHS 3: Logic gates' => [
        ['An OR gate gives an output of 1 when', ['both inputs are 0', 'at least one input is 1', 'only both inputs are 1', 'the inputs are equal'], 'B', 'OR is true if either input, or both, is true.', 2],
        ['A NOT gate with an input of 0 gives an output of', ['0', '1', '2', 'no output'], 'B', 'A NOT gate inverts its input.', 1],
        ['A NAND gate gives an output of 0 only when', ['both inputs are 1', 'both inputs are 0', 'one input is 1', 'never'], 'A', 'NAND is NOT-AND: it gives 0 only when AND would give 1.', 2],
    ],

    'SHS 3: Heredity and genetics' => [
        ['The passing of traits from parents to their offspring is called', ['mutation', 'heredity', 'evolution', 'variation'], 'B', 'Heredity is the transmission of characteristics through genes.', 1],
        ['A gene that shows its effect even when only one copy is present is', ['recessive', 'dominant', 'mutant', 'sex-linked'], 'B', 'A dominant allele masks the recessive allele in a heterozygote.', 1],
        ['Two parents with genotype Tt (T = tall, dominant) are crossed. What fraction of the offspring is expected to be short (tt)?', ['1/4', '1/2', '3/4', '0'], 'A', 'Tt × Tt gives TT, Tt, Tt and tt in the ratio 1 : 2 : 1, so 1/4 are tt.', 3],
        ['In humans, the sex chromosomes of a male are', ['XX', 'XY', 'YY', 'XO'], 'B', 'Males have one X and one Y chromosome; females have two X chromosomes.', 1],
        ['An organism\'s observable characteristics, such as height or eye colour, make up its', ['genotype', 'phenotype', 'allele', 'chromosome'], 'B', 'The phenotype is how the genotype shows, together with effects of the environment.', 2],
    ],
];
