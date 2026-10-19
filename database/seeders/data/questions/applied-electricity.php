<?php

/*
| SHS Applied Electricity: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Basic quantities' => [
        ['The unit of electrical resistance is the', ['ampere', 'volt', 'ohm', 'watt'], 'C', 'Resistance is measured in ohms (Ω).', 1],
        ['The unit of electrical power is the', ['joule', 'watt', 'coulomb', 'henry'], 'B', 'Power is measured in watts (W).', 1],
        ['The unit of capacitance is the', ['henry', 'farad', 'tesla', 'weber'], 'B', 'Capacitance is measured in farads (F).', 1],
        ['The unit of inductance is the', ['farad', 'henry', 'ohm', 'siemens'], 'B', 'Inductance is measured in henries (H).', 1],
        ['A 12 V supply drives a current of 3 A through a resistor. Find the resistance.', ['0.25 Ω', '4 Ω', '15 Ω', '36 Ω'], 'B', 'R = V/I = 12/3 = 4 Ω.', 1],
        ['Find the power used by a device drawing 5 A from a 230 V supply.', ['46 W', '235 W', '1,150 W', '2,300 W'], 'C', 'P = VI = 230 × 5 = 1,150 W.', 2],
        ['A 100 W bulb is used for 10 hours. How much energy does it use?', ['0.1 kWh', '1 kWh', '10 kWh', '1,000 kWh'], 'B', 'E = 0.1 kW × 10 h = 1 kWh.', 2],
        ['The resistance of a conductor increases when its', ['length decreases', 'cross-sectional area decreases', 'temperature falls (for metals)', 'material is changed to silver'], 'B', 'R = ρL/A, so a thinner conductor has more resistance.', 2],
    ],

    'Circuits' => [
        ['Resistors of 4 Ω and 12 Ω are connected in parallel. Find the equivalent resistance.', ['3 Ω', '8 Ω', '16 Ω', '48 Ω'], 'A', 'R = (4 × 12)/(4 + 12) = 48/16 = 3 Ω.', 2],
        ['Kirchhoff\'s current law states that at a junction', ['the sum of voltages is zero', 'the sum of currents entering equals the sum leaving', 'current is always zero', 'resistance is constant'], 'B', 'Charge is conserved at a junction.', 2],
        ['Kirchhoff\'s voltage law states that around any closed loop', ['the sum of the e.m.f.s equals the sum of the voltage drops', 'current is the same everywhere', 'resistance adds up to zero', 'power is constant'], 'A', 'Energy is conserved around a loop.', 3],
        ['Three cells of 1.5 V each connected in series give a total e.m.f. of', ['0.5 V', '1.5 V', '3.0 V', '4.5 V'], 'D', 'In series, e.m.f.s add: 3 × 1.5 = 4.5 V.', 1],
        ['Identical cells connected in parallel give', ['a higher voltage', 'the same voltage but a longer life', 'zero voltage', 'a lower current capacity'], 'B', 'Parallel cells keep the voltage of one cell but share the load.', 2],
    ],

    'Magnetism and machines' => [
        ['Fleming\'s right-hand rule is used to find the direction of', ['force on a conductor in a motor', 'current in a resistor', 'magnetic field of a solenoid', 'induced current in a generator'], 'D', 'The right-hand rule applies to generators; the left-hand rule to motors.', 3],
        ['The law that says an induced current opposes the change producing it is', ['Lenz\'s law', 'Ohm\'s law', 'Coulomb\'s law', 'Faraday\'s first law of electrolysis'], 'A', 'Lenz\'s law gives the direction of an induced e.m.f.', 3],
        ['The part of a d.c. motor that reverses the current in the armature every half turn is the', ['brush', 'field coil', 'commutator', 'yoke'], 'C', 'The split-ring commutator keeps the motor turning one way.', 2],
        ['A transformer works only on', ['direct current', 'batteries', 'static electricity', 'alternating current'], 'D', 'It needs a changing magnetic flux, which only a.c. provides.', 2],
        ['Laminating the core of a transformer reduces', ['eddy current losses', 'copper losses', 'voltage', 'number of turns'], 'A', 'Thin insulated layers block eddy currents.', 3],
        ['A transformer has a primary voltage of 240 V and a secondary voltage of 12 V. If the primary has 2,000 turns, find the secondary turns.', ['100', '200', '1,000', '40,000'], 'A', 'Ns = Np × Vs/Vp = 2000 × 12/240 = 100.', 3],
    ],

    'A.C. theory' => [
        ['The frequency of the mains supply in Ghana is', ['50 Hz', '60 Hz', '100 Hz', '240 Hz'], 'A', 'Ghana\'s mains supply is 230 V (often quoted as 240 V) at 50 Hz.', 1],
        ['The r.m.s. value of a sinusoidal voltage with a peak value of 100 V is about', ['50 V', '100 V', '70.7 V', '141 V'], 'C', 'Vrms = Vpeak/√2 ≈ 0.707 × 100 = 70.7 V.', 3],
        ['In a purely inductive a.c. circuit, the current', ['leads the voltage by 90°', 'is zero', 'is in phase with the voltage', 'lags the voltage by 90°'], 'D', 'In an inductor, the current lags the voltage by 90°.', 3],
        ['Capacitive reactance decreases when the', ['frequency increases', 'frequency decreases', 'capacitance decreases', 'voltage decreases'], 'A', 'Xc = 1/(2πfC), so a higher frequency gives a lower reactance.', 4],
        ['Power factor in an a.c. circuit is', ['true power ÷ apparent power', 'apparent power ÷ true power', 'voltage × current', 'resistance ÷ current'], 'A', 'Power factor = cos φ = P/S.', 3],
    ],

    'Electronics' => [
        ['A semiconductor device that allows current to flow in one direction only is a', ['resistor', 'capacitor', 'diode', 'inductor'], 'C', 'A diode conducts when forward-biased.', 1],
        ['Changing a.c. into d.c. is called', ['amplification', 'modulation', 'oscillation', 'rectification'], 'D', 'Diodes rectify a.c. into d.c.', 1],
        ['The three terminals of a bipolar transistor are', ['anode, cathode and gate', 'source, drain and gate', 'emitter, base and collector', 'positive, negative and earth'], 'C', 'BJTs have emitter, base and collector.', 2],
        ['Adding impurities to pure silicon to change its conductivity is called', ['annealing', 'etching', 'soldering', 'doping'], 'D', 'Doping creates n-type or p-type semiconductors.', 2],
    ],

    'Installation and safety' => [
        ['In a three-pin plug, the green/yellow wire is connected to the', ['live terminal', 'neutral terminal', 'earth terminal', 'fuse only'], 'C', 'Green/yellow is earth; brown is live; blue is neutral.', 1],
        ['In a three-pin plug, the fuse is connected in the', ['earth wire', 'neutral wire', 'live wire', 'all three wires'], 'C', 'The fuse must be in the live wire to cut off supply on a fault.', 2],
        ['A device that cuts off supply when it detects current leaking to earth is the', ['MCB only', 'switch fuse', 'transformer', 'RCD (residual current device)'], 'D', 'An RCD (ELCB) trips on earth leakage and protects people from shock.', 3],
        ['Switches in domestic wiring should be connected in the', ['neutral conductor', 'any conductor', 'earth conductor', 'live conductor'], 'D', 'Switching the live side makes the appliance safe when off.', 2],
    ],
];
