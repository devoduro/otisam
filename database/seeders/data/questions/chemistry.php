<?php

/*
| SHS Chemistry: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Atomic structure and periodicity
    ['The number of neutrons in ²³Na (atomic number 11) is', ['11', '12', '23', '34'], 'B', 'Neutrons = mass number − atomic number = 23 − 11 = 12.', 2],
    ['Which of the following elements is an alkali metal?', ['calcium', 'potassium', 'aluminium', 'chlorine'], 'B', 'Potassium is in Group 1, the alkali metals.', 1],
    ['Elements in the same group of the periodic table have the same', ['number of shells', 'number of valence electrons', 'mass number', 'number of neutrons'], 'B', 'Group members have the same number of outer-shell electrons, so they react similarly.', 2],
    ['Across a period from left to right, atomic radius generally', ['increases', 'decreases', 'stays the same', 'doubles'], 'B', 'Nuclear charge increases while electrons go into the same shell, pulling them closer.', 3],
    ['The halogens are found in Group', ['1', '2', '17 (VII)', '18 (0)'], 'C', 'Fluorine, chlorine, bromine and iodine are in Group 17 (VII).', 1],
    ['The electronic configuration 1s² 2s² 2p⁶ 3s¹ belongs to', ['magnesium', 'sodium', 'neon', 'aluminium'], 'B', 'That configuration has 11 electrons: sodium.', 3],

    // Bonding
    ['The bond in a chlorine molecule (Cl₂) is', ['ionic', 'covalent', 'metallic', 'hydrogen'], 'B', 'Two chlorine atoms share a pair of electrons.', 2],
    ['Which of the following compounds is ionic?', ['CH₄', 'NaCl', 'H₂O', 'CO₂'], 'B', 'Sodium chloride forms when sodium transfers an electron to chlorine.', 1],
    ['The unusually high boiling point of water compared with H₂S is due to', ['ionic bonding', 'hydrogen bonding', 'metallic bonding', 'van der Waals forces only'], 'B', 'Strong hydrogen bonds between water molecules need extra energy to break.', 3],
    ['The shape of a methane (CH₄) molecule is', ['linear', 'trigonal planar', 'tetrahedral', 'bent'], 'C', 'Four bonding pairs around carbon repel to a tetrahedral shape (109.5°).', 3],

    // Stoichiometry and gases
    ['What is the molar mass of CaCO₃? (Ca = 40, C = 12, O = 16)', ['68 g/mol', '84 g/mol', '100 g/mol', '116 g/mol'], 'C', '40 + 12 + 3 × 16 = 100 g/mol.', 2],
    ['How many moles are in 4 g of sodium hydroxide? (Na = 23, O = 16, H = 1)', ['0.1 mol', '0.4 mol', '1 mol', '10 mol'], 'A', 'M(NaOH) = 40 g/mol; 4 ÷ 40 = 0.1 mol.', 2],
    ['What volume does 2 moles of a gas occupy at s.t.p.? (Molar volume = 22.4 dm³)', ['11.2 dm³', '22.4 dm³', '44.8 dm³', '67.2 dm³'], 'C', '2 × 22.4 = 44.8 dm³.', 2],
    ['According to Boyle\'s law, at constant temperature the volume of a fixed mass of gas is', ['directly proportional to its pressure', 'inversely proportional to its pressure', 'independent of pressure', 'proportional to temperature'], 'B', 'PV = constant at constant temperature.', 2],
    ['Find the concentration of a solution containing 0.5 mol of solute in 250 cm³ of solution.', ['0.125 mol/dm³', '0.5 mol/dm³', '2.0 mol/dm³', '125 mol/dm³'], 'C', '250 cm³ = 0.25 dm³; 0.5 ÷ 0.25 = 2.0 mol/dm³.', 3],
    ['In the equation N₂ + 3H₂ → 2NH₃, how many moles of hydrogen react with 2 moles of nitrogen?', ['2', '3', '6', '4'], 'C', 'The ratio N₂ : H₂ is 1 : 3, so 2 mol N₂ needs 6 mol H₂.', 2],

    // Acids, bases, salts, redox
    ['The pH of a neutral solution at 25 °C is', ['0', '1', '7', '14'], 'C', 'Pure water at 25 °C has pH 7.', 1],
    ['Which indicator is pink in alkaline solution and colourless in acid?', ['methyl orange', 'universal indicator', 'litmus', 'phenolphthalein'], 'D', 'Phenolphthalein is colourless in acid and pink in alkali.', 2],
    ['A salt formed from a strong acid and a weak base, such as NH₄Cl, gives a solution that is', ['acidic', 'basic', 'neutral', 'amphoteric'], 'A', 'The ammonium ion hydrolyses to release H⁺, so the solution is slightly acidic.', 4],
    ['Oxidation is defined as', ['gain of electrons', 'loss of oxygen', 'gain of hydrogen', 'loss of electrons'], 'D', 'OIL RIG: oxidation is loss, reduction is gain of electrons.', 2],
    ['What is the oxidation number of manganese in KMnO₄?', ['+2', '+4', '+6', '+7'], 'D', 'K is +1 and four O are −8, so Mn = +7 to make the total zero.', 3],
    ['In electrolysis, the electrode at which oxidation occurs is the', ['anode', 'cathode', 'electrolyte', 'salt bridge'], 'A', 'Anions lose electrons at the anode: oxidation.', 2],

    // Rates, equilibrium, energy
    ['Which of the following will NOT increase the rate of a reaction?', ['increasing temperature', 'adding a catalyst', 'increasing particle size of a solid reactant', 'increasing concentration'], 'C', 'Bigger particles have less surface area, so the reaction slows down.', 2],
    ['A reaction that gives out heat to the surroundings is', ['exothermic', 'endothermic', 'reversible', 'photochemical'], 'A', 'Exothermic reactions release heat; ΔH is negative.', 1],
    ['According to Le Chatelier\'s principle, increasing the pressure on N₂ + 3H₂ ⇌ 2NH₃ will', ['shift the equilibrium to the left', 'stop the reaction', 'have no effect', 'shift the equilibrium to the right'], 'D', 'The right side has fewer gas molecules (2 against 4), so higher pressure favours ammonia.', 3],

    // Organic chemistry
    ['The general formula of the alkenes is', ['CₙH₂ₙ', 'CₙH₂ₙ₊₂', 'CₙH₂ₙ₋₂', 'CₙH₂ₙ₊₁OH'], 'A', 'Alkenes have one C=C double bond: CₙH₂ₙ.', 2],
    ['The functional group of the alkanols (alcohols) is', ['−COOH', '−NH₂', '−CHO', '−OH'], 'D', 'Alcohols contain the hydroxyl group −OH.', 1],
    ['Bromine water is decolourised by', ['ethene', 'ethane', 'methane', 'propane'], 'A', 'Ethene\'s double bond adds bromine, removing the orange colour.', 2],
    ['The reaction between an alkanoic acid and an alkanol to form a sweet-smelling compound is', ['saponification', 'cracking', 'polymerisation', 'esterification'], 'D', 'Acid + alcohol → ester + water (esterification).', 2],
    ['Soap is made by boiling fats or oils with', ['sodium hydroxide', 'sulphuric acid', 'sodium chloride', 'ethanol only'], 'A', 'Saponification is the alkaline hydrolysis of fats with NaOH or KOH.', 2],
    ['Polythene (polyethene) is formed by', ['condensation polymerisation', 'cracking of glucose', 'fermentation', 'addition polymerisation of ethene'], 'D', 'Many ethene molecules join through their double bonds.', 2],
    ['Ethanol is produced from sugar by', ['fermentation using yeast', 'cracking', 'distillation of air', 'electrolysis'], 'A', 'Yeast enzymes convert glucose into ethanol and carbon dioxide.', 1],

    // Industrial and environmental chemistry
    ['The Haber process is used to manufacture', ['sulphuric acid', 'chlorine', 'nitric acid', 'ammonia'], 'D', 'Nitrogen and hydrogen combine over an iron catalyst to make ammonia.', 1],
    ['The catalyst used in the Contact process for making sulphuric acid is', ['iron', 'nickel', 'vanadium(V) oxide', 'platinum only'], 'C', 'V₂O₅ catalyses the oxidation of SO₂ to SO₃.', 3],
    ['Hard water contains dissolved', ['calcium and magnesium salts', 'sodium chloride', 'carbon dioxide only', 'nitrogen'], 'A', 'Ca²⁺ and Mg²⁺ ions stop soap lathering easily.', 2],
    ['Acid rain is mainly caused by', ['oxygen and nitrogen', 'water vapour', 'carbon monoxide only', 'sulphur dioxide and oxides of nitrogen'], 'D', 'SO₂ and NOₓ dissolve in rainwater to form acids.', 2],
];
