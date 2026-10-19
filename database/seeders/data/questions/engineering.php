<?php

/*
| SHS Engineering (2023 curriculum STEM elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Engineering curriculum.
| Take g = 10 m/s² unless stated.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Engineering and the design process' => [
        ['Which branch of engineering deals mainly with roads, bridges and dams?', ['civil engineering', 'electrical engineering', 'chemical engineering', 'software engineering'], 'A', 'Civil engineers design and build public infrastructure.', 1],
        ['In the engineering design process, after defining the problem the next step is usually to', ['research and generate possible solutions', 'sell the product', 'drop the idea', 'paint the prototype'], 'A', 'Once the problem is clear, engineers research and brainstorm solutions before choosing, building and testing one.', 1],
        ['An engineer\'s first duty in professional ethics is to', ['protect public safety, health and welfare', 'make as much profit as possible', 'finish quickly at any cost', 'always use the cheapest materials'], 'A', 'Codes of engineering ethics put the safety of the public above everything else.', 1],
        ['The professional body that registers engineers in Ghana is the', ['Medical and Dental Council', 'Engineering Council of Ghana', 'Ghana Bar Association', 'Pharmacy Council'], 'B', 'The Engineering Council, set up under the Engineering Council Act, 2011 (Act 819), registers and regulates engineers.', 2],
    ],

    'Units and measurement' => [
        ['The SI unit of pressure is the', ['newton', 'joule', 'pascal', 'watt'], 'C', '1 pascal = 1 newton per square metre.', 1],
        ['1 megawatt (MW) is equal to', ['1,000 W', '1,000,000 W', '0.001 W', '1,000,000,000 W'], 'B', 'Mega means one million (10⁶).', 1],
        ['A length is measured as 25.0 ± 0.1 cm. What is the percentage uncertainty?', ['0.1%', '0.4%', '4%', '2.5%'], 'B', 'Percentage uncertainty = 0.1 ÷ 25.0 × 100% = 0.4%.', 3],
    ],

    'Statics and structures' => [
        ['A force of 20 N acts 0.5 m from a pivot. What is its moment?', ['40 N m', '10 N m', '20.5 N m', '0.025 N m'], 'B', 'Moment = force × perpendicular distance = 20 × 0.5 = 10 N m.', 1],
        ['A 40 N weight sits 0.3 m from a pivot. What force, 0.6 m from the pivot on the other side, will balance it?', ['80 N', '12 N', '20 N', '24 N'], 'C', 'Clockwise moment = anticlockwise moment: 40 × 0.3 = F × 0.6, so F = 12 ÷ 0.6 = 20 N.', 2],
        ['A member of a structure that is being squeezed is in', ['tension', 'compression', 'torsion', 'shear only'], 'B', 'Compression pushes the ends of a member together. Tension pulls them apart.', 1],
        ['A shaft that is being twisted is under', ['tension', 'compression', 'torsion', 'bending only'], 'C', 'Torsion is a twisting load, as on a drive shaft.', 1],
        ['Triangles are used in trusses and bridges because they', ['use more material', 'are rigid and do not change shape easily under load', 'only look attractive', 'are easy to paint'], 'B', 'A triangle cannot change shape unless one of its sides changes length, which makes it a rigid frame.', 2],
        ['A rope breaks at 6,000 N and its working load is 1,500 N. What is the factor of safety?', ['4', '9,000', '4,500', '0.25'], 'A', 'Factor of safety = breaking load ÷ working load = 6,000 ÷ 1,500 = 4.', 2],
    ],

    'Materials' => [
        ['A force of 2,000 N acts on an area of 0.01 m². What is the stress?', ['20 Pa', '200 Pa', '200,000 Pa', '20,000 Pa'], 'C', 'Stress = force ÷ area = 2,000 ÷ 0.01 = 200,000 Pa (200 kPa).', 2],
        ['A wire 2 m long stretches by 4 mm. What is the strain?', ['0.002', '0.5', '2', '0.008'], 'A', 'Strain = extension ÷ original length = 0.004 m ÷ 2 m = 0.002. Strain has no units.', 3],
        ['Young\'s modulus is the ratio of', ['strain to stress', 'force to area', 'load to extension squared', 'stress to strain'], 'D', 'Young\'s modulus = stress ÷ strain. A stiff material has a high value.', 2],
        ['The ability of a material to be drawn out into wires is', ['ductility', 'brittleness', 'hardness', 'elasticity'], 'A', 'Copper is very ductile, which is why it is used for electrical wire.', 1],
        ['Resistance to scratching and wear is', ['toughness', 'malleability', 'ductility', 'hardness'], 'D', 'Hardness is resistance to scratching, cutting and wear. Toughness is resistance to breaking under impact.', 2],
        ['A block of mass 4 kg has a volume of 0.002 m³. What is its density?', ['2,000 kg/m³', '0.0005 kg/m³', '8 kg/m³', '500 kg/m³'], 'A', 'Density = mass ÷ volume = 4 ÷ 0.002 = 2,000 kg/m³.', 2],
    ],

    'Work, energy and machines' => [
        ['A force of 50 N moves an object 4 m in the direction of the force. How much work is done?', ['12.5 J', '54 J', '200 J', '46 J'], 'C', 'Work = force × distance = 50 × 4 = 200 J.', 1],
        ['A motor does 600 J of work in 5 s. What is its power?', ['3,000 W', '120 W', '605 W', '0.0083 W'], 'B', 'Power = work ÷ time = 600 ÷ 5 = 120 W.', 1],
        ['A machine gives out 300 J of useful work for every 400 J put in. What is its efficiency?', ['75%', '133%', '25%', '70%'], 'A', 'Efficiency = useful output ÷ input × 100% = 300 ÷ 400 × 100% = 75%.', 2],
        ['Find the kinetic energy of a 2 kg mass moving at 3 m/s.', ['6 J', '9 J', '18 J', '3 J'], 'B', 'KE = ½mv² = ½ × 2 × 3² = 9 J.', 2],
        ['A 5 kg mass is raised through 4 m. What is its gain in potential energy?', ['20 J', '2 J', '50 J', '200 J'], 'D', 'PE = mgh = 5 × 10 × 4 = 200 J.', 2],
        ['A pulley system with a velocity ratio of 4 lifts an 800 N load with an effort of 250 N. What is its efficiency?', ['64%', '125%', '32%', '80%'], 'D', 'Mechanical advantage = 800 ÷ 250 = 3.2. Efficiency = MA ÷ VR × 100% = 3.2 ÷ 4 × 100% = 80%.', 4],
        ['A 20-tooth driver gear turns at 300 rpm and drives a 60-tooth gear. How fast does the driven gear turn?', ['900 rpm', '100 rpm', '60 rpm', '180 rpm'], 'B', 'Speed × teeth is the same for both gears: 300 × 20 = N × 60, so N = 100 rpm.', 3],
    ],

    'Electrical, fluids and thermal' => [
        ['A 6 Ω heater is connected to a 12 V supply. What is its power?', ['72 W', '2 W', '24 W', '0.5 W'], 'C', 'P = V² ÷ R = 144 ÷ 6 = 24 W. (Current = 2 A, and P = VI = 12 × 2 = 24 W.)', 3],
        ['What is the water pressure at a depth of 5 m? (Density of water = 1,000 kg/m³)', ['5,000 Pa', '50,000 Pa', '500 Pa', '2,000 Pa'], 'B', 'P = ρgh = 1,000 × 10 × 5 = 50,000 Pa. (This ignores the air pressure above the water.)', 3],
        ['A gas at 100 kPa occupies 2 L. At constant temperature it is compressed to 1 L. What is the new pressure?', ['50 kPa', '100 kPa', '200 kPa', '400 kPa'], 'C', 'Boyle\'s law: p₁V₁ = p₂V₂, so 100 × 2 = p₂ × 1 and p₂ = 200 kPa.', 3],
        ['Heat moves through a solid metal rod mainly by', ['conduction', 'convection', 'radiation', 'evaporation'], 'A', 'In solids, heat passes from particle to particle (and by free electrons in metals): conduction.', 1],
    ],

    'Energy and sustainability' => [
        ['Which of these is a renewable energy source?', ['coal', 'solar energy', 'natural gas', 'diesel'], 'B', 'Sunlight is not used up. Coal, gas and diesel are fossil fuels that will run out.', 1],
        ['The Akosombo Dam generates electricity from', ['nuclear fission', 'coal', 'wind', 'hydro (moving water)'], 'D', 'Water from Lake Volta falls through turbines at Akosombo to drive generators.', 1],
    ],

    'SHS 1: Health, safety and ethics in engineering' => [
        ['A hazard is', ['the chance that harm will occur', 'anything that could cause harm', 'a type of insurance', 'a safety sign'], 'B', 'Risk is how likely the harm is and how serious it would be.', 2],
        ['Identifying hazards, judging the risks and deciding on controls is a', ['patent search', 'design brief', 'risk assessment', 'cost estimate'], 'C', 'It is done before work begins.', 1],
        ['Locking off and tagging a machine\'s power supply before maintenance prevents', ['noise', 'theft of tools', 'rust', 'the machine being switched on while someone works on it'], 'D', 'This is called lockout–tagout.', 2],
        ['An engineer who reports unsafe practices in their company to the authorities is', ['a contractor', 'a whistle-blower', 'a shareholder', 'an apprentice'], 'B', 'Professional ethics put public safety first.', 2],
    ],

    'SHS 1: Electrical and electronic circuits' => [
        ['An appliance draws 5 A from a 240 V supply. What is its power?', ['245 W', '48 W', '1,200 W', '235 W'], 'C', 'P = VI = 240 × 5 = 1,200 W.', 1],
        ['An LED is usually connected with a resistor in series in order to', ['change AC to DC', 'make it brighter than its rating', 'store charge', 'limit the current so the LED is not damaged'], 'D', 'Too much current overheats the LED.', 2],
        ['In a parallel circuit, each branch has', ['the same current through it', 'the same voltage across it', 'no current', 'double the supply voltage'], 'B', 'The currents in the branches add up to the total current.', 2],
    ],

    'Renewable energy' => [
        ['Ghana\'s Bui Dam on the Black Volta generates', ['coal power', 'nuclear power', 'hydroelectric power', 'geothermal power'], 'C', 'It is Ghana\'s second-largest hydroelectric plant after Akosombo.', 1],
        ['A wind turbine converts', ['sound into electricity', 'heat into light', 'chemical energy into heat', 'the kinetic energy of moving air into electrical energy'], 'D', 'The blades turn a generator.', 1],
        ['Energy from plant and animal materials, such as wood, crop waste and biogas, is', ['geothermal energy', 'biomass energy', 'tidal energy', 'nuclear energy'], 'B', 'It is renewable if the plants are regrown.', 1],
    ],

    'Energy efficiency and conservation' => [
        ['A solar water heater costs GH¢3,000 and saves GH¢500 a year in electricity. What is the payback period?', ['15 years', '5 years', '6 years', '2 years'], 'C', 'Payback = cost ÷ yearly saving = 3,000 ÷ 500 = 6 years.', 2],
        ['Replacing filament bulbs with LED bulbs saves energy because LEDs', ['need no electricity', 'use higher voltage', 'are brighter at all times', 'convert more of the electricity into light and less into heat'], 'D', 'LEDs use up to about 80% less energy for the same light.', 1],
        ['A survey of how and where a building uses energy, to find savings, is an', ['environmental impact only', 'energy audit', 'insurance claim', 'architectural drawing'], 'B', 'It points out waste such as poor insulation or old equipment.', 2],
    ],

    'SHS 3: Energy systems' => [
        ['Electricity is transmitted over long distances at very high voltage mainly to', ['increase the current', 'make it more dangerous', 'reduce power losses in the cables', 'change it into DC'], 'C', 'Higher voltage means lower current, and losses depend on I²R.', 3],
        ['The three main parts of a power supply system are generation, transmission and', ['storage only', 'consumption tax', 'metering only', 'distribution'], 'D', 'Distribution networks deliver power to homes and businesses.', 1],
    ],

    'Engineering design and prototyping' => [
        ['Repeatedly testing a design and improving it is called', ['iterative design', 'mass production', 'reverse engineering', 'quality inspection only'], 'A', 'Each cycle of testing leads to a better version.', 1],
        ['A quick, low-cost model used to test an idea early is a', ['low-fidelity prototype', 'final product', 'patent', 'invoice'], 'A', 'Cardboard models and sketches are low-fidelity prototypes.', 2],
        ['3D printing is useful in prototyping mainly because it', ['makes complex parts quickly without special tooling', 'is the cheapest way to make millions of parts', 'only prints paper', 'needs no design file'], 'A', 'It suits one-off and small-batch parts.', 2],
    ],

    'Automation and embedded systems' => [
        ['A computer system built into a product to control it, such as in a washing machine, is', ['an embedded system', 'a supercomputer', 'a server farm', 'a spreadsheet'], 'A', 'It is usually based on a microcontroller.', 1],
        ['A rugged industrial computer used to control machines on a production line is a', ['programmable logic controller (PLC)', 'smartphone', 'router', 'scanner'], 'A', 'PLCs read sensors and switch actuators.', 2],
        ['A thermostat that switches a heater on and off to keep a set temperature is an example of', ['closed-loop (feedback) control', 'open-loop control', 'manual control', 'no control'], 'A', 'It measures the temperature and responds to it.', 2],
        ['The part of a computer that carries out instructions is the', ['CPU', 'monitor', 'keyboard', 'power supply'], 'A', 'The central processing unit fetches, decodes and executes instructions.', 1],
    ],
];
