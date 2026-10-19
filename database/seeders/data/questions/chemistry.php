<?php

/*
| SHS Chemistry: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Atomic structure and periodicity' => [
        ['The number of neutrons in ²³Na (atomic number 11) is', ['11', '12', '23', '34'], 'B', 'Neutrons = mass number − atomic number = 23 − 11 = 12.', 2],
        ['Which of the following elements is an alkali metal?', ['calcium', 'potassium', 'aluminium', 'chlorine'], 'B', 'Potassium is in Group 1, the alkali metals.', 1],
        ['Elements in the same group of the periodic table have the same', ['number of shells', 'number of valence electrons', 'mass number', 'number of neutrons'], 'B', 'Group members have the same number of outer-shell electrons, so they react similarly.', 2],
        ['Across a period from left to right, atomic radius generally', ['increases', 'decreases', 'stays the same', 'doubles'], 'B', 'Nuclear charge increases while electrons go into the same shell, pulling them closer.', 3],
        ['The halogens are found in Group', ['1', '2', '17 (VII)', '18 (0)'], 'C', 'Fluorine, chlorine, bromine and iodine are in Group 17 (VII).', 1],
        ['The electronic configuration 1s² 2s² 2p⁶ 3s¹ belongs to', ['magnesium', 'sodium', 'neon', 'aluminium'], 'B', 'That configuration has 11 electrons: sodium.', 3],
    ],

    'Bonding' => [
        ['The bond in a chlorine molecule (Cl₂) is', ['ionic', 'covalent', 'metallic', 'hydrogen'], 'B', 'Two chlorine atoms share a pair of electrons.', 2],
        ['Which of the following compounds is ionic?', ['CH₄', 'NaCl', 'H₂O', 'CO₂'], 'B', 'Sodium chloride forms when sodium transfers an electron to chlorine.', 1],
        ['The unusually high boiling point of water compared with H₂S is due to', ['ionic bonding', 'hydrogen bonding', 'metallic bonding', 'van der Waals forces only'], 'B', 'Strong hydrogen bonds between water molecules need extra energy to break.', 3],
        ['The shape of a methane (CH₄) molecule is', ['linear', 'trigonal planar', 'tetrahedral', 'bent'], 'C', 'Four bonding pairs around carbon repel to a tetrahedral shape (109.5°).', 3],
    ],

    'Stoichiometry and gases' => [
        ['What is the molar mass of CaCO₃? (Ca = 40, C = 12, O = 16)', ['68 g/mol', '84 g/mol', '100 g/mol', '116 g/mol'], 'C', '40 + 12 + 3 × 16 = 100 g/mol.', 2],
        ['How many moles are in 4 g of sodium hydroxide? (Na = 23, O = 16, H = 1)', ['0.1 mol', '0.4 mol', '1 mol', '10 mol'], 'A', 'M(NaOH) = 40 g/mol; 4 ÷ 40 = 0.1 mol.', 2],
        ['What volume does 2 moles of a gas occupy at s.t.p.? (Molar volume = 22.4 dm³)', ['11.2 dm³', '22.4 dm³', '44.8 dm³', '67.2 dm³'], 'C', '2 × 22.4 = 44.8 dm³.', 2],
        ['According to Boyle\'s law, at constant temperature the volume of a fixed mass of gas is', ['directly proportional to its pressure', 'inversely proportional to its pressure', 'independent of pressure', 'proportional to temperature'], 'B', 'PV = constant at constant temperature.', 2],
        ['Find the concentration of a solution containing 0.5 mol of solute in 250 cm³ of solution.', ['0.125 mol/dm³', '0.5 mol/dm³', '2.0 mol/dm³', '125 mol/dm³'], 'C', '250 cm³ = 0.25 dm³; 0.5 ÷ 0.25 = 2.0 mol/dm³.', 3],
        ['In the equation N₂ + 3H₂ → 2NH₃, how many moles of hydrogen react with 2 moles of nitrogen?', ['2', '3', '6', '4'], 'C', 'The ratio N₂ : H₂ is 1 : 3, so 2 mol N₂ needs 6 mol H₂.', 2],
    ],

    'Acids, bases, salts, redox' => [
        ['The pH of a neutral solution at 25 °C is', ['0', '1', '7', '14'], 'C', 'Pure water at 25 °C has pH 7.', 1],
        ['Which indicator is pink in alkaline solution and colourless in acid?', ['methyl orange', 'universal indicator', 'litmus', 'phenolphthalein'], 'D', 'Phenolphthalein is colourless in acid and pink in alkali.', 2],
        ['A salt formed from a strong acid and a weak base, such as NH₄Cl, gives a solution that is', ['acidic', 'basic', 'neutral', 'amphoteric'], 'A', 'The ammonium ion hydrolyses to release H⁺, so the solution is slightly acidic.', 4],
        ['Oxidation is defined as', ['gain of electrons', 'loss of oxygen', 'gain of hydrogen', 'loss of electrons'], 'D', 'OIL RIG: oxidation is loss, reduction is gain of electrons.', 2],
        ['What is the oxidation number of manganese in KMnO₄?', ['+2', '+4', '+6', '+7'], 'D', 'K is +1 and four O are −8, so Mn = +7 to make the total zero.', 3],
        ['In electrolysis, the electrode at which oxidation occurs is the', ['anode', 'cathode', 'electrolyte', 'salt bridge'], 'A', 'Anions lose electrons at the anode: oxidation.', 2],
    ],

    'Rates, equilibrium, energy' => [
        ['Which of the following will NOT increase the rate of a reaction?', ['increasing temperature', 'adding a catalyst', 'increasing particle size of a solid reactant', 'increasing concentration'], 'C', 'Bigger particles have less surface area, so the reaction slows down.', 2],
        ['A reaction that gives out heat to the surroundings is', ['exothermic', 'endothermic', 'reversible', 'photochemical'], 'A', 'Exothermic reactions release heat; ΔH is negative.', 1],
        ['According to Le Chatelier\'s principle, increasing the pressure on N₂ + 3H₂ ⇌ 2NH₃ will', ['shift the equilibrium to the left', 'stop the reaction', 'have no effect', 'shift the equilibrium to the right'], 'D', 'The right side has fewer gas molecules (2 against 4), so higher pressure favours ammonia.', 3],
    ],

    'Organic chemistry' => [
        ['The general formula of the alkenes is', ['CₙH₂ₙ', 'CₙH₂ₙ₊₂', 'CₙH₂ₙ₋₂', 'CₙH₂ₙ₊₁OH'], 'A', 'Alkenes have one C=C double bond: CₙH₂ₙ.', 2],
        ['The functional group of the alkanols (alcohols) is', ['−COOH', '−NH₂', '−CHO', '−OH'], 'D', 'Alcohols contain the hydroxyl group −OH.', 1],
        ['Bromine water is decolourised by', ['ethene', 'ethane', 'methane', 'propane'], 'A', 'Ethene\'s double bond adds bromine, removing the orange colour.', 2],
        ['The reaction between an alkanoic acid and an alkanol to form a sweet-smelling compound is', ['saponification', 'cracking', 'polymerisation', 'esterification'], 'D', 'Acid + alcohol → ester + water (esterification).', 2],
        ['Soap is made by boiling fats or oils with', ['sodium hydroxide', 'sulphuric acid', 'sodium chloride', 'ethanol only'], 'A', 'Saponification is the alkaline hydrolysis of fats with NaOH or KOH.', 2],
        ['Polythene (polyethene) is formed by', ['condensation polymerisation', 'cracking of glucose', 'fermentation', 'addition polymerisation of ethene'], 'D', 'Many ethene molecules join through their double bonds.', 2],
        ['Ethanol is produced from sugar by', ['fermentation using yeast', 'cracking', 'distillation of air', 'electrolysis'], 'A', 'Yeast enzymes convert glucose into ethanol and carbon dioxide.', 1],
    ],

    'Industrial and environmental chemistry' => [
        ['The Haber process is used to manufacture', ['sulphuric acid', 'chlorine', 'nitric acid', 'ammonia'], 'D', 'Nitrogen and hydrogen combine over an iron catalyst to make ammonia.', 1],
        ['The catalyst used in the Contact process for making sulphuric acid is', ['iron', 'nickel', 'vanadium(V) oxide', 'platinum only'], 'C', 'V₂O₅ catalyses the oxidation of SO₂ to SO₃.', 3],
        ['Hard water contains dissolved', ['calcium and magnesium salts', 'sodium chloride', 'carbon dioxide only', 'nitrogen'], 'A', 'Ca²⁺ and Mg²⁺ ions stop soap lathering easily.', 2],
        ['Acid rain is mainly caused by', ['oxygen and nitrogen', 'water vapour', 'carbon monoxide only', 'sulphur dioxide and oxides of nitrogen'], 'D', 'SO₂ and NOₓ dissolve in rainwater to form acids.', 2],
    ],

    'SHS 1: Atoms and the scientific method' => [
        ['A sodium atom has atomic number 11 and mass number 23. How many neutrons does it have?', ['11', '12', '23', '34'], 'B', 'Neutrons = mass number − atomic number = 23 − 11 = 12.', 1],
        ['What is the electron configuration of magnesium (atomic number 12)?', ['8, 2, 2', '2, 10', '2, 8, 1, 1', '2, 8, 2'], 'D', 'The shells fill 2, then 8, then the remaining 2.', 1],
        ['Chlorine is 75% chlorine-35 and 25% chlorine-37. What is its relative atomic mass?', ['36', '35.5', '35', '37'], 'B', '(75 × 35 + 25 × 37) ÷ 100 = (2,625 + 925) ÷ 100 = 35.5.', 2],
        ['Which particle has a relative charge of −1 and almost no mass?', ['proton', 'neutron', 'electron', 'nucleus'], 'C', 'The electron\'s mass is about 1/1840 of a proton\'s.', 1],
    ],

    'SHS 1: The mole concept' => [
        ['How many moles are in 4.4 g of carbon dioxide? (C = 12, O = 16)', ['10 mol', '1 mol', '0.44 mol', '0.1 mol'], 'D', 'Mr of CO₂ = 12 + 32 = 44, so moles = 4.4 ÷ 44 = 0.1 mol.', 2],
        ['How many molecules are in 0.5 mol of water? (Avogadro\'s constant = 6.02 × 10²³ mol⁻¹)', ['6.02 × 10²³', '3.01 × 10²³', '1.20 × 10²⁴', '9.0 × 10²³'], 'B', '0.5 × 6.02 × 10²³ = 3.01 × 10²³ molecules.', 2],
        ['What is the mass of 0.25 mol of sodium hydroxide, NaOH? (Na = 23, O = 16, H = 1)', ['4 g', '40 g', '160 g', '10 g'], 'D', 'Mr = 23 + 16 + 1 = 40, so mass = 0.25 × 40 = 10 g.', 2],
        ['What volume does 1 mol of any gas occupy at s.t.p.?', ['6.02 dm³', '24.0 cm³', '1 dm³', '22.4 dm³'], 'D', 'The molar volume of a gas at s.t.p. is 22.4 dm³ (about 24 dm³ at room temperature).', 1],
    ],

    'SHS 1: Formulae, mole ratios and equations' => [
        ['A compound contains 80% carbon and 20% hydrogen by mass. What is its empirical formula? (C = 12, H = 1)', ['CH', 'CH₂', 'CH₃', 'CH₄'], 'C', 'C: 80 ÷ 12 = 6.67; H: 20 ÷ 1 = 20. The ratio 6.67 : 20 = 1 : 3, so CH₃.', 3],
        ['In the balanced equation N₂ + 3H₂ → 2NH₃, how many moles of hydrogen react with 2 mol of nitrogen?', ['3 mol', '6 mol', '2 mol', '4 mol'], 'B', 'The mole ratio N₂ : H₂ is 1 : 3, so 2 mol N₂ needs 6 mol H₂.', 2],
        ['What is the relative formula mass of calcium carbonate, CaCO₃? (Ca = 40, C = 12, O = 16)', ['68', '84', '100', '116'], 'C', '40 + 12 + 3 × 16 = 100.', 1],
        ['CH₄ + 2O₂ → CO₂ + 2H₂O. How many moles of oxygen are needed to burn 3 mol of methane completely?', ['3 mol', '6 mol', '2 mol', '1.5 mol'], 'B', 'Each mole of CH₄ needs 2 mol of O₂: 3 × 2 = 6 mol.', 2],
    ],

    'SHS 1: Kinetic theory and the states of matter' => [
        ['How many times faster does hydrogen (Mr = 2) diffuse than oxygen (Mr = 32)?', ['2 times', '4 times', '8 times', '16 times'], 'B', 'Graham\'s law: rate ∝ 1/√M, so the ratio is √(32 ÷ 2) = √16 = 4.', 3],
        ['Iodine crystals change straight from solid to vapour on heating. This is', ['evaporation', 'sublimation', 'condensation', 'melting'], 'B', 'Sublimation skips the liquid state.', 1],
        ['Sweating cools the body because', ['water conducts heat into the body', 'sweat is cold', 'sweat blocks sunlight', 'evaporation takes heat energy from the skin'], 'D', 'The fastest molecules escape, lowering the average energy of what is left.', 2],
        ['A gas occupies 2 dm³ at 300 K. At constant pressure, what is its volume at 600 K?', ['1 dm³', '4 dm³', '2 dm³', '6 dm³'], 'B', 'Charles\'s law: V ∝ T, so doubling the kelvin temperature doubles the volume.', 2],
    ],

    'SHS 1: Periodic properties' => [
        ['Down Group 1, the reactivity of the alkali metals', ['decreases', 'increases', 'stays constant', 'drops to zero'], 'B', 'The outer electron is further from the nucleus and is lost more easily.', 2],
        ['The energy needed to remove one mole of electrons from one mole of gaseous atoms is the', ['electron affinity', 'first ionisation energy', 'lattice energy', 'electronegativity'], 'B', 'It is measured in kJ/mol.', 2],
        ['Which element has the highest electronegativity?', ['oxygen', 'chlorine', 'fluorine', 'sodium'], 'C', 'Fluorine attracts bonding electrons more strongly than any other element.', 1],
    ],

    'SHS 1: Bonding between atoms' => [
        ['Sodium chloride is held together by', ['hydrogen bonds', 'covalent bonds', 'metallic bonds', 'ionic bonds'], 'D', 'Na gives an electron to Cl, forming Na⁺ and Cl⁻ ions that attract strongly.', 1],
        ['A covalent bond is formed when atoms', ['lose protons', 'transfer electrons', 'share a pair of electrons', 'gain neutrons'], 'C', 'Non-metal atoms share electrons, as in H₂, Cl₂ and H₂O.', 1],
        ['The bond formed when both shared electrons come from one atom, as in NH₄⁺, is', ['a hydrogen bond', 'an ionic bond', 'a metallic bond', 'a dative (coordinate) bond'], 'D', 'The nitrogen in NH₃ donates its lone pair to H⁺.', 2],
        ['Metallic bonding is best described as', ['transfer of electrons to non-metals', 'shared pairs between two atoms', 'positive ions in a sea of delocalised electrons', 'weak attraction between molecules'], 'C', 'The free electrons explain why metals conduct electricity.', 2],
    ],

    'SHS 1: Forces between molecules' => [
        ['Water has an unusually high boiling point for its size because of', ['its low mass', 'ionic bonding', 'metallic bonding', 'hydrogen bonding between molecules'], 'D', 'Strong hydrogen bonds must be broken before water can boil.', 2],
        ['The weak forces between non-polar molecules such as iodine are', ['covalent bonds', 'ionic bonds', 'van der Waals (dispersion) forces', 'hydrogen bonds'], 'C', 'They come from temporary dipoles and grow with molecule size.', 2],
        ['Hydrogen bonding occurs between molecules in which hydrogen is bonded to', ['metals', 'C', 'Cl only', 'N, O or F'], 'D', 'These small, very electronegative atoms make the hydrogen strongly positive.', 2],
    ],

    'SHS 1: Analysing and classifying organic compounds' => [
        ['An organic compound decolourises bromine water. This shows that it', ['is a salt', 'is an alkane', 'contains a C=C double bond (is unsaturated)', 'contains no carbon'], 'C', 'Bromine adds across the double bond, removing the orange-brown colour.', 2],
        ['The functional group –OH is found in', ['alkanoic acids only', 'alkanes', 'alkenes', 'alkanols (alcohols)'], 'D', 'Ethanol, C₂H₅OH, is an example.', 1],
        ['The –COOH group is the functional group of', ['alkanols', 'esters', 'alkanoic (carboxylic) acids', 'ketones'], 'C', 'Ethanoic acid, CH₃COOH, is the acid in vinegar.', 1],
        ['What is the general formula of the alkanes?', ['CₙH₂ₙ₊₁OH', 'CₙH₂ₙ', 'CₙH₂ₙ₋₂', 'CₙH₂ₙ₊₂'], 'D', 'For example, methane CH₄ and propane C₃H₈.', 1],
        ['Compounds with the same molecular formula but different structures are', ['allotropes', 'isotopes', 'isomers', 'homologues'], 'C', 'Butane and methylpropane are both C₄H₁₀.', 2],
    ],

    'SHS 2: Energy changes' => [
        ['An exothermic reaction has an enthalpy change (ΔH) that is', ['always 100 kJ', 'positive', 'zero', 'negative'], 'D', 'Energy is given out to the surroundings, so the products have less energy.', 1],
        ['Which process is endothermic?', ['neutralising an acid', 'burning charcoal', 'dissolving ammonium nitrate in water', 'rusting'], 'C', 'The solution gets colder because it absorbs heat; cold packs use this.', 2],
        ['Hess\'s law states that the total enthalpy change of a reaction', ['equals the activation energy', 'depends on the catalyst', 'is always negative', 'does not depend on the route taken'], 'D', 'It allows enthalpy changes to be calculated from other reactions.', 3],
        ['Breaking chemical bonds', ['involves no energy change', 'releases energy', 'absorbs energy', 'always produces light'], 'C', 'Bond breaking is endothermic; bond making is exothermic.', 2],
    ],

    'SHS 2: Chemical kinetics' => [
        ['A catalyst speeds up a reaction by', ['changing the products', 'increasing the temperature', 'being used up', 'providing a route with lower activation energy'], 'D', 'A catalyst is chemically unchanged at the end of the reaction.', 2],
        ['Powdered chalk reacts faster with acid than lumps of chalk because the powder has', ['a catalyst', 'a higher temperature', 'a larger surface area', 'less mass'], 'C', 'More surface means more collisions per second.', 1],
        ['Raising the temperature increases the rate of reaction mainly because', ['fewer collisions occur', 'particles become heavier', 'the volume decreases', 'more particles collide with energy above the activation energy'], 'D', 'The particles move faster and more collisions succeed.', 2],
        ['A common unit for the rate of a reaction is', ['g cm⁻³', 'kJ mol⁻¹', 'mol dm⁻³ s⁻¹', 'K'], 'C', 'Rate is the change in concentration per unit time.', 2],
    ],

    'SHS 2: Dynamic equilibrium' => [
        ['For N₂(g) + 3H₂(g) ⇌ 2NH₃(g), increasing the pressure shifts the equilibrium', ['to stop the reaction', 'to the left', 'neither way', 'to the right'], 'D', 'Le Chatelier: the side with fewer gas molecules (2 against 4) is favoured.', 3],
        ['Adding a catalyst to a reaction at equilibrium', ['shifts it to the left', 'shifts it to the right', 'shortens the time to reach equilibrium but does not shift it', 'stops the reaction'], 'C', 'A catalyst speeds up the forward and reverse reactions equally.', 2],
        ['The catalyst used in the Haber process for ammonia is', ['nickel', 'platinum', 'vanadium(V) oxide', 'iron'], 'D', 'Iron is used at about 450 °C and 200 atm.', 2],
        ['At dynamic equilibrium,', ['only products are present', 'the reaction has stopped', 'the forward and reverse rates are equal', 'concentrations keep changing quickly'], 'C', 'Both reactions continue, but the concentrations stay constant.', 2],
    ],

    'SHS 2: Acids, bases and salts' => [
        ['According to Brønsted and Lowry, an acid is', ['a hydroxide donor', 'a proton acceptor', 'an electron donor', 'a proton (H⁺) donor'], 'D', 'A base is a proton acceptor.', 2],
        ['A strong acid is one that', ['has a high pH', 'is very concentrated', 'ionises completely in water', 'does not react with metals'], 'C', 'Strength is about ionisation, not concentration. HCl is strong; ethanoic acid is weak.', 2],
        ['What salt forms when sulphuric acid reacts with sodium hydroxide?', ['sodium nitrate', 'sodium chloride', 'sodium sulphide', 'sodium sulphate'], 'D', 'H₂SO₄ + 2NaOH → Na₂SO₄ + 2H₂O.', 1],
        ['What is the pH of 0.01 mol/dm³ hydrochloric acid?', ['1', '2', '0.01', '12'], 'B', 'HCl ionises fully, so [H⁺] = 10⁻² and pH = 2.', 3],
    ],

    'SHS 2: Trends in the periodic table' => [
        ['Which oxide of a Period 3 element is amphoteric?', ['Na₂O', 'Al₂O₃', 'SO₂', 'P₄O₁₀'], 'B', 'Aluminium oxide reacts with both acids and bases.', 3],
        ['Across Period 3 from sodium to chlorine, metallic character', ['increases', 'decreases', 'stays the same', 'is absent throughout'], 'B', 'Elements change from metals (Na, Mg, Al) to non-metals (P, S, Cl).', 2],
        ['The oxides of non-metals such as sulphur are usually', ['basic', 'acidic', 'neutral salts', 'metallic'], 'B', 'SO₂ dissolves in water to form an acidic solution.', 2],
    ],

    'SHS 2: The halogens' => [
        ['Chlorine gas is', ['colourless', 'purple', 'greenish-yellow', 'red-brown'], 'C', 'Bromine is a red-brown liquid and iodine a grey-black solid with purple vapour.', 1],
        ['Down Group 17, the reactivity of the halogens', ['increases', 'decreases', 'stays the same', 'drops to zero'], 'B', 'Larger atoms attract an extra electron less strongly.', 2],
        ['When chlorine is bubbled into potassium bromide solution, the solution turns orange because', ['iodine forms', 'bromine displaces chlorine', 'no reaction occurs', 'chlorine displaces bromine'], 'D', 'A more reactive halogen displaces a less reactive one: Cl₂ + 2KBr → 2KCl + Br₂.', 2],
        ['Chlorine is added to drinking water mainly to', ['add taste', 'kill germs', 'soften the water', 'colour the water'], 'B', 'Chlorine disinfects water by killing bacteria.', 1],
    ],

    'SHS 2: Structure, bonding and properties' => [
        ['Diamond is very hard because', ['it is made of ions', 'it has free electrons', 'each carbon atom is covalently bonded to four others in a giant structure', 'its molecules are small'], 'C', 'The strong bonds run in every direction through the crystal.', 2],
        ['Graphite conducts electricity because it has', ['no bonds', 'ions that move', 'hydrogen bonds', 'delocalised electrons between its layers'], 'D', 'Each carbon uses three electrons in bonds, leaving one free to move.', 2],
        ['Ionic compounds conduct electricity when', ['solid', 'molten or dissolved in water', 'cold', 'powdered'], 'B', 'The ions must be free to move.', 1],
    ],

    'SHS 2: Organic compounds' => [
        ['Alkenes typically undergo', ['no reactions', 'only substitution reactions', 'addition reactions', 'neutralisation'], 'C', 'The double bond opens up and atoms add across it, as with hydrogen or bromine.', 2],
        ['Ethanol can be made from sugar by', ['fermentation with yeast', 'cracking', 'distillation of air', 'electrolysis'], 'A', 'C₆H₁₂O₆ → 2C₂H₅OH + 2CO₂.', 1],
        ['An alkanoic acid reacts with an alkanol to form', ['an ester and water', 'a salt and hydrogen', 'an alkene', 'a polymer only'], 'A', 'This reaction is esterification, usually with concentrated sulphuric acid as catalyst.', 2],
        ['Breaking large hydrocarbon molecules into smaller, more useful ones is', ['cracking', 'polymerisation', 'esterification', 'saponification'], 'A', 'Cracking turns heavy fractions into petrol and alkenes.', 2],
    ],

    'SHS 3: pH and its applications' => [
        ['A solution has [H⁺] = 1 × 10⁻³ mol/dm³. What is its pH?', ['3', '−3', '11', '0.001'], 'A', 'pH = −log₁₀[H⁺] = −log(10⁻³) = 3.', 2],
        ['A solution has a pOH of 4 at 25 °C. What is its pH?', ['4', '10', '14', '18'], 'B', 'pH + pOH = 14, so pH = 14 − 4 = 10.', 3],
        ['A buffer solution', ['resists changes in pH when small amounts of acid or base are added', 'always has pH 7', 'is a strong acid', 'changes colour with pH'], 'A', 'Blood is buffered to keep its pH near 7.4.', 2],
        ['Pure water at 25 °C has a pH of', ['0', '7', '14', '1'], 'B', 'Its H⁺ and OH⁻ concentrations are equal (10⁻⁷ mol/dm³).', 1],
    ],

    'SHS 3: Hydrolysis of salts' => [
        ['A solution of ammonium chloride in water is', ['acidic', 'alkaline', 'neutral', 'a buffer'], 'A', 'NH₄Cl is the salt of a strong acid and a weak base; NH₄⁺ releases H⁺.', 3],
        ['A solution of sodium carbonate in water is', ['acidic', 'alkaline', 'neutral', 'colourless acid'], 'B', 'It is the salt of a weak acid and a strong base; CO₃²⁻ takes H⁺ from water, leaving OH⁻.', 3],
        ['A solution of sodium chloride in water is', ['acidic', 'alkaline', 'neutral', 'strongly basic'], 'C', 'It is the salt of a strong acid and a strong base, so there is no hydrolysis.', 2],
    ],

    'SHS 3: Indicators and titration' => [
        ['Phenolphthalein is', ['colourless in acid and pink in alkali', 'red in acid and yellow in alkali', 'blue in acid', 'always pink'], 'A', 'It changes colour around pH 8–10.', 1],
        ['Which indicator suits a titration of a strong acid with a weak base?', ['methyl orange', 'phenolphthalein', 'litmus paper only', 'universal indicator paper'], 'A', 'The end point is acidic, and methyl orange changes colour at low pH.', 3],
        ['20.0 cm³ of 0.10 mol/dm³ HCl neutralises 25.0 cm³ of NaOH solution. What is the concentration of the NaOH?', ['0.08 mol/dm³', '0.125 mol/dm³', '0.10 mol/dm³', '0.8 mol/dm³'], 'A', 'Moles of HCl = 0.10 × 0.020 = 0.002 mol = moles of NaOH. Concentration = 0.002 ÷ 0.025 = 0.08 mol/dm³.', 3],
        ['The point in a titration where exactly enough reagent has been added to react with the sample is the', ['equivalence point', 'boiling point', 'melting point', 'triple point'], 'A', 'The end point, seen from the indicator, should match it closely.', 2],
    ],

    'SHS 3: Oxidation and reduction' => [
        ['Oxidation is', ['loss of electrons', 'gain of electrons', 'gain of hydrogen', 'loss of oxygen'], 'A', 'Remember OIL RIG: oxidation is loss, reduction is gain (of electrons).', 1],
        ['What is the oxidation number of sulphur in H₂SO₄?', ['+4', '+6', '−2', '+2'], 'B', '2(+1) + S + 4(−2) = 0, so S = +6.', 3],
        ['In electrolysis, reduction takes place at the', ['cathode', 'anode', 'salt bridge', 'voltmeter'], 'A', 'Positive ions gain electrons at the negative electrode (cathode).', 2],
    ],

    'SHS 3: The transition elements' => [
        ['A typical property of transition elements is that they', ['form coloured compounds', 'have only one oxidation state', 'are gases', 'do not conduct electricity'], 'A', 'They also have variable oxidation states and act as catalysts.', 2],
        ['Which transition-metal compound is the catalyst in the Contact process for sulphuric acid?', ['vanadium(V) oxide', 'iron', 'nickel', 'copper(II) sulphate'], 'A', 'V₂O₅ catalyses the oxidation of SO₂ to SO₃.', 3],
        ['Iron can form Fe²⁺ and Fe³⁺ ions. This shows that transition elements have', ['variable oxidation states', 'no electrons', 'full d-subshells only', 'a single valency'], 'A', 'Electrons can be lost from both the 4s and 3d subshells.', 2],
    ],

    'SHS 3: Alkanoic acid derivatives and lipids' => [
        ['Esters are known for their', ['pleasant, fruity smells', 'strong acidity', 'metallic lustre', 'high melting points'], 'A', 'They are used in flavourings and perfumes.', 1],
        ['Boiling a fat or oil with sodium hydroxide gives', ['soap and glycerol', 'ester and water', 'alcohol only', 'sugar'], 'A', 'This is saponification.', 2],
        ['Fats and oils are esters of', ['glycerol and fatty acids', 'ethanol and ethanoic acid', 'glucose and water', 'amino acids'], 'A', 'They are triglycerides: one glycerol joined to three fatty acids.', 2],
    ],

    'SHS 3: Polymers' => [
        ['Polyethene is made from ethene by', ['addition polymerisation', 'condensation polymerisation', 'fermentation', 'cracking'], 'A', 'The C=C bonds open and the monomers join into long chains with no other product.', 2],
        ['Nylon is a condensation polymer because, as it forms,', ['a small molecule such as water is given off', 'nothing else is produced', 'it is heated', 'it contains carbon'], 'A', 'Each link between monomers releases a small molecule.', 3],
        ['Which of these is a natural polymer?', ['starch', 'PVC', 'nylon', 'polystyrene'], 'A', 'Starch, cellulose and proteins are natural polymers.', 1],
        ['A plastic that softens on heating and can be remoulded is', ['a thermoplastic', 'a thermosetting plastic', 'a ceramic', 'an alloy'], 'A', 'Thermosets set permanently on first heating.', 1],
    ],
];
