<?php

/*
| SHS Applied Technology (2023 curriculum elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Applied Technology curriculum
| (electrical and electronics, mechanical and automotive, wood, metal and building work, drawing and safety).
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Workshop safety' => [
        ['What is the first thing to do before repairing an electrical appliance?', ['isolate it from the power supply', 'test it with wet hands', 'remove the fuse while it is on', 'turn up the voltage'], 'A', 'Switching off and unplugging (isolating) the appliance removes the risk of electric shock.', 1],
        ['Which fire extinguisher is suitable for a fire in electrical equipment?', ['water', 'foam', 'sand mixed with water', 'carbon dioxide (CO₂)'], 'D', 'CO₂ does not conduct electricity and leaves no residue. Water and foam conduct and can cause electric shock.', 2],
        ['Which item protects the eyes from the bright light of arc welding?', ['welding helmet with a filter lens', 'dust mask', 'ear muffs', 'safety boots'], 'A', 'The arc gives off intense visible and ultraviolet light, so a darkened filter lens is needed.', 1],
    ],

    'Electrical and electronics' => [
        ['In modern single-phase wiring in Ghana, the earth wire is coloured', ['brown', 'blue', 'green and yellow', 'red'], 'C', 'Ghana follows the IEC colours: brown for live, blue for neutral, and green-and-yellow for earth.', 2],
        ['A 12 V supply drives current through a 4 Ω resistor. What is the current?', ['48 A', '0.33 A', '16 A', '3 A'], 'D', 'I = V ÷ R = 12 ÷ 4 = 3 A.', 2],
        ['An appliance draws 2 A from a 240 V supply. What is its power?', ['120 W', '480 W', '242 W', '238 W'], 'B', 'P = V × I = 240 × 2 = 480 W.', 2],
        ['The purpose of a fuse in a circuit is', ['to break the circuit when the current is too high', 'to store charge', 'to raise the voltage', 'to reduce resistance'], 'A', 'The fuse wire melts when too much current flows, cutting off the supply and protecting the circuit.', 1],
        ['Resistors of 3 Ω, 5 Ω and 2 Ω are connected in series. What is the total resistance?', ['10 Ω', '0.97 Ω', '30 Ω', '5 Ω'], 'A', 'In series the resistances add: 3 + 5 + 2 = 10 Ω.', 2],
        ['Two 6 Ω resistors are connected in parallel. What is the combined resistance?', ['12 Ω', '6 Ω', '3 Ω', '36 Ω'], 'C', 'For two equal resistors in parallel, R = 6 ÷ 2 = 3 Ω. (Or 1/R = 1/6 + 1/6 = 1/3.)', 3],
        ['A diode allows current to flow', ['in one direction only', 'equally in both directions', 'only when it is cold', 'only in AC circuits'], 'A', 'A diode conducts when forward-biased and blocks current in the reverse direction.', 2],
        ['A 2 kW heater is used for 3 hours. How much energy does it use?', ['1.5 kWh', '5 kWh', '6 kWh', '0.67 kWh'], 'C', 'Energy = power × time = 2 kW × 3 h = 6 kWh.', 2],
        ['A transformer has 1,000 turns on the primary and 50 on the secondary. With 240 V input, what is the output voltage?', ['4,800 V', '12 V', '24 V', '120 V'], 'B', 'Vs = Vp × Ns/Np = 240 × 50/1,000 = 12 V. It is a step-down transformer.', 3],
        ['A 12 V car battery is usually made of how many 2 V cells in series?', ['2', '4', '6', '12'], 'C', '6 cells × 2 V = 12 V.', 2],
    ],

    'Mechanical and automotive' => [
        ['In what order do the strokes of a four-stroke engine happen?', ['intake, compression, power, exhaust', 'compression, intake, exhaust, power', 'power, intake, compression, exhaust', 'intake, power, compression, exhaust'], 'A', 'Air and fuel are drawn in, compressed, burnt to push the piston down (power), then pushed out as exhaust.', 2],
        ['In a petrol engine, the fuel–air mixture is ignited by the', ['injector', 'spark plug', 'piston', 'crankshaft'], 'B', 'The spark plug makes a spark across its gap to ignite the compressed mixture.', 1],
        ['A diesel engine ignites its fuel by', ['a spark plug', 'a flame from the exhaust', 'heat from compressing the air', 'a magnet'], 'C', 'Diesel engines compress air until it is hot enough to ignite the fuel as it is injected.', 3],
        ['A driver gear with 20 teeth turns a driven gear with 60 teeth. What is the gear ratio?', ['1 : 3', '3 : 1', '2 : 6', '20 : 1'], 'B', 'Gear ratio = driven teeth ÷ driver teeth = 60 ÷ 20 = 3, written 3 : 1. The driven gear turns 3 times slower.', 3],
        ['The machine tool used to turn cylindrical work is the', ['shaping machine', 'drilling machine', 'lathe', 'grinder'], 'C', 'On a lathe the work spins while a cutting tool shapes it into a cylinder.', 1],
    ],

    'Metal work' => [
        ['Which of the following is a ferrous metal?', ['copper', 'aluminium', 'mild steel', 'brass'], 'C', 'Ferrous metals contain iron. Mild steel is mostly iron; the others contain no iron.', 1],
        ['Steel is mainly an alloy of iron and', ['carbon', 'copper', 'zinc', 'aluminium'], 'A', 'Steel is iron with a small amount of carbon, usually less than about 2%.', 1],
        ['Brass is an alloy of', ['copper and tin', 'iron and carbon', 'lead and tin', 'copper and zinc'], 'D', 'Brass is copper and zinc. Copper and tin make bronze.', 2],
        ['When fitting a hacksaw blade, the teeth should point', ['towards the handle', 'upwards', 'in any direction', 'away from the handle'], 'D', 'A hacksaw cuts on the forward (push) stroke, so the teeth point away from the handle.', 2],
        ['A tool used to cut an external thread on a rod is a', ['tap', 'die', 'reamer', 'drill'], 'B', 'A die cuts external threads. A tap cuts internal threads in a hole.', 2],
        ['Flux is used in soldering to', ['cool the joint', 'colour the solder', 'make the joint heavier', 'clean the surface and prevent oxidation'], 'D', 'Flux removes and prevents oxides so the solder can wet and bond to the metal.', 2],
        ['Which tool measures the outside diameter of a rod to about 0.02 mm?', ['steel rule', 'vernier caliper', 'try square', 'scriber'], 'B', 'A vernier caliper reads to 0.02 mm (some to 0.05 mm). A steel rule reads only to about 0.5 mm.', 2],
    ],

    'Wood and building work' => [
        ['Which of these is a hardwood?', ['pine', 'spruce', 'odum', 'cedar'], 'C', 'Odum (iroko) comes from a broad-leaved tree, so it is a hardwood. Pine, spruce and cedar are softwoods.', 2],
        ['The main purpose of seasoning timber is to', ['reduce its moisture content', 'change its colour', 'make it heavier', 'make it softer'], 'A', 'Drying timber reduces shrinking, warping and decay after it is used.', 1],
        ['A mortise and tenon is a', ['wood joint', 'welding process', 'brick bond', 'soldering method'], 'A', 'A tenon on one piece fits into a mortise (slot) cut in the other.', 1],
        ['In a 1 : 2 : 4 concrete mix, the 2 stands for', ['cement', 'sand (fine aggregate)', 'coarse aggregate', 'water'], 'B', 'The ratio is cement : sand : coarse aggregate, so 1 part cement, 2 parts sand and 4 parts stones.', 2],
        ['A brick bond in which every course is made of stretchers only is', ['English bond', 'Flemish bond', 'stretcher bond', 'header bond'], 'C', 'Stretcher bond shows only the long faces of the bricks, with joints staggered by half a brick.', 2],
    ],

    'Drawing and measurement' => [
        ['In first-angle projection, the plan is drawn', ['above the front elevation', 'to the left of the end view', 'below the front elevation', 'on top of the end view'], 'C', 'In first-angle projection the plan goes below the front elevation. In third-angle it goes above.', 3],
        ['About how many millimetres are there in 1 inch?', ['2.54', '25.4', '10', '100'], 'B', '1 inch = 2.54 cm = 25.4 mm.', 2],
    ],

    'SHS 1: Vehicle transmission and braking' => [
        ['The clutch in a manual car is used to', ['cool the engine', 'connect and disconnect the engine from the gearbox', 'steer the front wheels', 'charge the battery'], 'B', 'The driver presses the clutch to change gear smoothly.', 1],
        ['The differential allows the driving wheels to', ['turn backwards only', 'stop at the same time', 'turn at different speeds when the vehicle goes round a corner', 'receive fuel'], 'C', 'The outer wheel travels further than the inner wheel on a bend.', 2],
        ['Hydraulic brakes work by transmitting pressure through', ['coolant', 'engine oil', 'air only', 'brake fluid'], 'D', 'Pressing the pedal pushes fluid to the wheel cylinders or calipers.', 2],
        ['An anti-lock braking system (ABS) helps the driver by', ['making the car faster', 'stopping the wheels locking so the car can still be steered', 'cooling the brakes', 'saving fuel'], 'B', 'Locked wheels skid and cannot steer.', 2],
    ],

    'SHS 1: Manufactured boards' => [
        ['Plywood is made by gluing thin layers (veneers) with the grain of each layer', ['removed', 'all in the same direction', 'at right angles to the next', 'curved'], 'C', 'Cross-graining makes plywood strong and stable.', 2],
        ['A board made from wood chips bonded with resin is', ['solid timber', 'plywood', 'hardboard', 'chipboard (particle board)'], 'D', 'It is cheap and often covered with a veneer or laminate.', 1],
        ['An advantage of manufactured boards over solid timber is that they', ['never need cutting', 'come in large, stable sheets', 'are always waterproof', 'are always heavier'], 'B', 'They resist warping and are made in standard sizes.', 1],
    ],

    'SHS 1: Site mobilisation, plant and equipment' => [
        ['Moving workers, machines and materials onto a construction site before work starts is', ['excavation', 'demolition', 'mobilisation', 'finishing'], 'C', 'It includes setting up stores, offices and access.', 1],
        ['Which machine is mainly used for digging trenches and loading earth?', ['tower crane only', 'concrete mixer', 'road roller', 'excavator'], 'D', 'Excavators have a boom, arm and bucket.', 1],
        ['A machine with a large front blade used to push soil and clear land is a', ['forklift', 'bulldozer', 'dumper', 'compressor'], 'B', 'Bulldozers clear and level sites.', 1],
    ],

    'SHS 2: Engine cooling and lubrication' => [
        ['The thermostat in an engine cooling system', ['charges the battery', 'pumps oil', 'keeps coolant from flowing to the radiator until the engine is warm', 'ignites the fuel'], 'C', 'It helps the engine reach its working temperature quickly.', 2],
        ['Coolant (antifreeze) is used instead of plain water mainly to', ['clean the fuel', 'make the engine heavier', 'lubricate the pistons', 'raise the boiling point and prevent corrosion'], 'D', 'It also lowers the freezing point in cold climates.', 2],
        ['The oil filter in an engine', ['cools the coolant', 'removes dirt from the lubricating oil', 'filters air for the cabin', 'stores fuel'], 'B', 'Clean oil reduces wear on moving parts.', 1],
        ['A measure of how easily oil flows, shown by grades such as SAE 20W-50, is its', ['octane rating', 'density', 'viscosity', 'flash point'], 'C', 'Thicker oil has higher viscosity.', 2],
    ],

    'SHS 2: Heat treatment and sand casting' => [
        ['Heating steel and then cooling it quickly in water or oil is', ['tempering', 'annealing', 'normalising', 'hardening (quenching)'], 'D', 'It makes the steel hard but brittle.', 2],
        ['Reheating hardened steel to a lower temperature to reduce brittleness is', ['quenching', 'tempering', 'casting', 'forging'], 'B', 'Tempering trades a little hardness for toughness.', 2],
        ['In sand casting, the shape used to make the cavity in the sand is the', ['ladle', 'riser', 'pattern', 'crucible'], 'C', 'The pattern is removed, leaving a hollow the shape of the part.', 2],
        ['In a sand mould, the riser', ['cools the metal', 'holds the pattern', 'is where the sand is mixed', 'supplies extra molten metal as the casting cools and shrinks'], 'D', 'Without a riser, shrinkage cavities can form.', 3],
    ],

    'SHS 2: Steering, suspension and wheels' => [
        ['Most modern cars use which steering system?', ['chain and sprocket', 'rack and pinion', 'belt and pulley', 'worm only'], 'B', 'Turning the pinion moves the rack sideways to turn the wheels.', 2],
        ['The shock absorber in a vehicle\'s suspension', ['stores fuel', 'turns the wheels', 'damps the bouncing of the springs', 'cools the brakes'], 'C', 'Without it, the car would keep bouncing after a bump.', 2],
        ['Leaf springs are commonly used in the suspension of', ['aircraft', 'bicycles', 'motorbikes only', 'trucks and pick-ups'], 'D', 'They are strong and simple for heavy loads.', 2],
        ['Worn tyre tread is dangerous mainly because it', ['improves braking', 'reduces grip, especially on wet roads', 'reduces fuel use', 'makes steering lighter'], 'B', 'Tread channels water away from the contact patch.', 1],
    ],

    'SHS 2: Metal forming processes' => [
        ['Passing hot metal between heavy rollers to make it thinner is', ['drilling', 'casting', 'rolling', 'welding'], 'C', 'Steel sheets and bars are made by rolling.', 1],
        ['Which tool is used to bend sheet metal along a straight line?', ['file', 'hacksaw', 'centre punch', 'folding machine (bending bars)'], 'D', 'Folding bars or a sheet-metal folder give clean bends.', 2],
        ['Forging improves the strength of metal parts because it', ['adds carbon', 'refines and aligns the grain structure', 'makes them hollow', 'melts them'], 'B', 'Forged parts such as spanners are tough.', 3],
    ],

    'SHS 2: Woodwork tools and machines' => [
        ['Which hand plane is used to smooth and straighten the edges of long boards?', ['router plane', 'block plane', 'jack plane', 'plough plane'], 'C', 'Its long sole bridges hollows to give a straight edge.', 2],
        ['Which machine is best for cutting curves in timber?', ['mortiser', 'circular saw', 'planer', 'band saw'], 'D', 'Its narrow blade can follow curved lines.', 2],
        ['A firmer chisel is mainly used for', ['cutting metal', 'general paring and cutting joints', 'measuring', 'drilling holes'], 'B', 'Mortise chisels are thicker, for chopping deep mortises.', 2],
        ['Before using a woodwork machine, a learner must first', ['wear loose clothing', 'remove the guards', 'check that guards are in place and get permission', 'switch on without checking'], 'C', 'Guards and supervision prevent serious injuries.', 1],
    ],

    'SHS 2: Foundations and formwork' => [
        ['A continuous strip of concrete under a load-bearing wall is a', ['pile foundation', 'pad foundation', 'raft foundation', 'strip foundation'], 'D', 'Pad foundations support single columns.', 2],
        ['A foundation that covers the whole area of a building, used on weak soils, is a', ['strip foundation', 'raft foundation', 'pad foundation', 'stepped foundation'], 'B', 'It spreads the load over a large area.', 2],
        ['Formwork in concrete work is used to', ['cure bricks', 'mix concrete', 'hold wet concrete in shape until it hardens', 'cut steel'], 'C', 'It is removed once the concrete is strong enough.', 1],
        ['A damp-proof course (DPC) in a wall prevents', ['noise', 'cracks from heat', 'termites only', 'moisture rising from the ground'], 'D', 'It is laid above ground level near the base of walls.', 1],
    ],

    'SHS 2: Wood joints, adhesives, fasteners and roofs' => [
        ['Which joint is traditionally used for the corners of drawers?', ['butt joint', 'dovetail joint', 'halving joint', 'bridle joint'], 'B', 'Its interlocking tails and pins resist pulling apart.', 2],
        ['PVA glue is commonly used for', ['sealing roofs', 'welding metal', 'joining wood indoors', 'gluing glass to metal'], 'C', 'It is a white woodworking adhesive.', 1],
        ['Screws hold better than nails mainly because', ['they are shorter', 'they are heavier', 'they rust faster', 'their threads grip the wood'], 'D', 'Screws also allow parts to be taken apart.', 1],
        ['A roof that slopes down on all four sides is a', ['gable roof', 'hip roof', 'lean-to roof', 'flat roof'], 'B', 'A gable roof has two slopes with triangular ends.', 2],
    ],

    'SHS 2: Domestic electrical installation' => [
        ['A miniature circuit breaker (MCB) is preferred to a rewirable fuse because it', ['uses more copper', 'never trips', 'can be reset after tripping', 'is decorative'], 'C', 'It switches off automatically on overload or short circuit.', 2],
        ['Two-way switching is used, for example, to', ['charge two batteries', 'run two appliances on one plug', 'double the voltage', 'control a staircase light from both the top and the bottom'], 'D', 'Each switch can turn the lamp on or off.', 2],
        ['A residual current device (RCD) protects people by', ['raising the voltage', 'cutting the supply when current leaks to earth', 'storing charge', 'cooling the cables'], 'B', 'It trips quickly enough to prevent a fatal shock.', 3],
        ['Electrical appliances with metal cases are earthed so that', ['they look better', 'they use less power', 'a fault current flows safely to earth and blows the fuse', 'they work faster'], 'C', 'Earthing prevents the case becoming live.', 2],
    ],

    'SHS 2: Electronic components and digital electronics' => [
        ['A resistor has the colour bands red, red and brown. What is its value?', ['122 Ω', '22 Ω', '2.2 kΩ', '220 Ω'], 'D', 'Red = 2, red = 2, brown = × 10: 22 × 10 = 220 Ω.', 3],
        ['The unit of capacitance is the', ['farad', 'ohm', 'henry', 'watt'], 'A', 'Practical capacitors are usually measured in μF, nF or pF.', 1],
        ['Three 1.5 V cells are connected in series. What is the total voltage?', ['4.5 V', '1.5 V', '3 V', '0.5 V'], 'A', 'In series the voltages add.', 1],
        ['Which logic gate gives an output of 1 only when both inputs are 1?', ['AND', 'OR', 'NOT', 'NOR'], 'A', 'AND needs all inputs high.', 1],
    ],

    'SHS 3: CNC, design for manufacture and 3D printing' => [
        ['CNC machines are programmed with instructions commonly written in', ['G-code', 'HTML', 'Morse code', 'shorthand'], 'A', 'G-code tells the machine where and how to move.', 2],
        ['Designing a product so that it is easy and cheap to make is', ['design for manufacture', 'reverse engineering', 'quality control only', 'marketing'], 'A', 'Fewer parts and standard components cut costs.', 2],
        ['A laser cutter cuts materials by', ['focusing a powerful beam of light to burn or melt them', 'sawing with a blade', 'grinding', 'hammering'], 'A', 'It gives very accurate cuts in wood, acrylic and thin metal.', 1],
        ['Linking a CAD drawing directly to a machine that makes the part is', ['CAD/CAM', 'manual drafting', 'freehand sketching', 'casting'], 'A', 'CAM stands for computer-aided manufacture.', 1],
    ],

    'SHS 3: Building services and finishes' => [
        ['A trap (U-bend) under a sink prevents', ['foul gases from the drain entering the room', 'water from flowing', 'pipes from rusting', 'taps from leaking'], 'A', 'The water held in the bend forms a seal.', 2],
        ['A septic tank is used to', ['treat household sewage where there is no public sewer', 'store drinking water', 'heat water', 'collect rainwater for drinking'], 'A', 'Solids settle and are broken down by bacteria.', 1],
        ['The first coat of paint applied to bare wood or metal is the', ['primer', 'finishing coat', 'undercoat only', 'varnish'], 'A', 'Primer seals the surface and helps later coats stick.', 1],
        ['Applying a smooth layer of cement mortar to a wall is', ['plastering (rendering)', 'pointing', 'grouting', 'tiling'], 'A', 'It prepares the wall for painting.', 1],
    ],

    'SHS 3: Electrical machines and appliances' => [
        ['In a DC motor, the commutator', ['reverses the current in the coil every half turn', 'stores charge', 'cools the motor', 'measures speed'], 'A', 'This keeps the coil turning in the same direction.', 3],
        ['An alternator in a vehicle produces', ['alternating current, which is rectified to charge the battery', 'only direct current', 'mechanical energy', 'heat for the cabin'], 'A', 'Diodes inside it convert the AC to DC.', 3],
        ['Losses in a transformer caused by the resistance of its windings are', ['copper losses', 'iron losses', 'eddy current losses', 'hysteresis losses'], 'A', 'Iron (core) losses include eddy current and hysteresis losses.', 3],
        ['An electric kettle that does not heat although power reaches it most likely has', ['a broken (open-circuit) heating element', 'too much water', 'a new plug', 'a clean filter'], 'A', 'A continuity test with a multimeter confirms the fault.', 2],
        ['In AM radio, the information is carried by changes in the carrier wave\'s', ['amplitude', 'frequency', 'speed', 'colour'], 'A', 'In FM it is carried by changes in frequency.', 2],
    ],
];
