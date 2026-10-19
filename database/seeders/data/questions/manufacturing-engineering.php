<?php

/*
| SHS Manufacturing Engineering (2023 curriculum STEM elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Manufacturing Engineering curriculum.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Production systems' => [
        ['Making one unique item to a customer\'s order is', ['job (one-off) production', 'batch production', 'mass production', 'continuous flow production'], 'A', 'Job production makes a single item to a customer\'s own requirements, such as a made-to-measure suit.', 1],
        ['Bottling soft drinks in very large numbers on an assembly line is an example of', ['one-off production', 'job production', 'mass production', 'prototyping'], 'C', 'Mass production makes huge numbers of identical products, often non-stop.', 1],
        ['Making a set number of identical items, such as 500 school uniforms, and then switching to another product is', ['job production', 'batch production', 'continuous production', 'craft production'], 'B', 'Batch production makes a fixed quantity at a time before the line is reset for another product.', 1],
        ['A machine makes one part every 45 seconds. How many parts does it make in an 8-hour shift with no stops?', ['360', '480', '640', '800'], 'C', '8 hours = 8 × 3,600 = 28,800 s. 28,800 ÷ 45 = 640 parts.', 3],
        ['A Gantt chart is used in manufacturing to', ['measure hardness', 'schedule tasks over time', 'cast metal', 'test voltage'], 'B', 'A Gantt chart shows each task as a bar against a timeline, so overlaps and deadlines are clear.', 2],
    ],

    'Shaping processes' => [
        ['Pouring molten metal into a mould to make a shape is', ['forging', 'machining', 'welding', 'casting'], 'D', 'In casting, liquid metal fills a mould and takes its shape as it cools.', 1],
        ['Shaping hot metal by hammering or pressing it is', ['forging', 'casting', 'turning', 'sintering'], 'A', 'Forging shapes metal while it is solid, which also improves its grain structure.', 1],
        ['Milling, turning and drilling are examples of', ['forming processes', 'joining processes', 'material-removal (machining) processes', 'casting processes'], 'C', 'These processes cut away material to reach the final shape.', 1],
        ['Injection moulding is commonly used to make', ['timber planks', 'plastic bottle caps and containers', 'steel rails only', 'glass windows'], 'B', 'Molten plastic is injected into a closed mould, which suits making many identical plastic parts.', 2],
        ['Forcing material through a shaped die to make long sections, such as aluminium window frames, is', ['forging', 'casting', 'extrusion', 'stamping'], 'C', 'Extrusion gives a long piece with the same cross-section along its length.', 2],
        ['Pressing metal powder in a die and heating it below its melting point so the particles bond is', ['annealing', 'sintering', 'galvanising', 'extrusion'], 'B', 'Sintering is the key step in powder metallurgy.', 3],
        ['3D printing is an example of', ['subtractive machining', 'forging', 'additive manufacturing', 'casting'], 'C', 'A 3D printer builds an object layer by layer, adding material only where it is needed.', 1],
    ],

    'Joining and finishing' => [
        ['Which process joins metals by melting the parent metal at the joint?', ['soldering', 'riveting', 'bolting', 'welding'], 'D', 'Welding fuses the parent metals together. Soldering melts only a filler metal and leaves the parent metal solid.', 2],
        ['Coating steel with zinc to prevent rusting is', ['anodising', 'galvanising', 'annealing', 'tempering'], 'B', 'The zinc protects the steel even when scratched, because zinc corrodes in preference to iron.', 2],
        ['Heating a metal and letting it cool slowly to make it softer and less brittle is', ['quenching', 'hardening', 'case hardening', 'annealing'], 'D', 'Annealing relieves internal stresses and makes the metal easier to work.', 2],
    ],

    'Automation and CNC' => [
        ['CNC stands for', ['Central Network Connection', 'Controlled Nail Cutting', 'Computer Numerical Control', 'Computer Notation Code'], 'C', 'A CNC machine follows coded numerical instructions to move its cutting tool.', 1],
        ['Using machines and control systems to carry out tasks with little human input is', ['automation', 'outsourcing', 'handcrafting', 'hand assembly'], 'A', 'Automation increases speed and consistency and can remove people from dangerous tasks.', 1],
    ],

    'Quality and measurement' => [
        ['A shaft is dimensioned 25 ± 0.05 mm. What is the largest acceptable size?', ['25.5 mm', '25.05 mm', '24.95 mm', '25.005 mm'], 'B', 'Upper limit = 25 + 0.05 = 25.05 mm.', 2],
        ['For a dimension of 40 ± 0.1 mm, what is the tolerance (the total variation allowed)?', ['0.1 mm', '40.1 mm', '0.05 mm', '0.2 mm'], 'D', 'Limits are 39.9 mm and 40.1 mm. Tolerance = 40.1 − 39.9 = 0.2 mm.', 3],
        ['A micrometer is commonly used to measure to an accuracy of', ['1 mm', '0.01 mm', '1 cm', '0.5 mm'], 'B', 'A standard metric micrometer reads to 0.01 mm.', 2],
        ['Checking finished products and rejecting the faulty ones is', ['quality control', 'quality assurance planning', 'marketing', 'procurement'], 'A', 'Quality control inspects output. Quality assurance plans the whole process so that faults do not happen.', 2],
        ['In a batch of 2,000 bolts, 50 are faulty. What percentage is defective?', ['5%', '25%', '2.5%', '0.25%'], 'C', '50 ÷ 2,000 × 100% = 2.5%.', 2],
        ['The body that sets product standards in Ghana is the', ['Ghana Revenue Authority', 'Ghana Standards Authority', 'Electoral Commission', 'Ghana Tourism Authority'], 'B', 'The Ghana Standards Authority develops standards and tests products for quality and safety.', 1],
    ],

    'Lean manufacturing' => [
        ['The main aim of lean manufacturing is to', ['eliminate waste', 'increase stock levels', 'slow down production', 'hire more inspectors'], 'A', 'Lean methods cut anything that does not add value, such as waiting, excess stock and defects.', 2],
        ['Just-in-time (JIT) production means that', ['large warehouses of stock are kept', 'production happens only at night', 'materials arrive just when they are needed', 'goods are made long before orders arrive'], 'C', 'JIT keeps stock low by having parts delivered only when they are needed for production.', 2],
        ['Kaizen is a Japanese term meaning', ['zero stock', 'continuous improvement', 'quality inspection', 'mass production'], 'B', 'Kaizen means making many small improvements all the time, with every worker involved.', 2],
    ],

    'Costs and supply' => [
        ['Fixed costs are GH¢10,000 and the variable cost is GH¢5 per unit. What is the total cost of 2,000 units?', ['GH¢10,005', 'GH¢20,000', 'GH¢12,000', 'GH¢15,000'], 'B', 'Total cost = 10,000 + 5 × 2,000 = 10,000 + 10,000 = GH¢20,000.', 2],
        ['A product sells for GH¢15, costs GH¢10 per unit to make, and fixed costs are GH¢5,000. What is the break-even quantity?', ['500', '333', '2,000', '1,000'], 'D', 'Each unit contributes 15 − 10 = GH¢5. Break-even = 5,000 ÷ 5 = 1,000 units.', 3],
        ['The network of suppliers, manufacturers, distributors and retailers that moves a product to customers is the', ['supply chain', 'assembly line', 'production plan', 'bill of materials'], 'A', 'The supply chain covers every step from raw material to the customer.', 1],
        ['A list of all the parts and raw materials needed to make a product is the', ['invoice', 'bill of materials', 'work permit', 'price list'], 'B', 'The bill of materials (BOM) lists every component and the quantity of each.', 2],
        ['Ghana\'s One District, One Factory programme aims mainly to', ['close down factories', 'import more finished goods', 'build one school in every district', 'set up industries across the districts to create jobs'], 'D', 'The programme encourages factories in every district to add value to local raw materials and create jobs.', 1],
    ],

    'Safety' => [
        ['Which PPE protects workers from loud machine noise?', ['ear defenders', 'goggles', 'gloves', 'an apron'], 'A', 'Ear defenders or earplugs reduce noise and prevent hearing damage.', 1],
    ],

    'SHS 1: Materials and their properties' => [
        ['The four main groups of engineering materials are metals, polymers, ceramics and', ['liquids', 'composites', 'gases', 'minerals only'], 'B', 'Composites combine two or more materials.', 1],
        ['Which of these is a composite material?', ['glass', 'pure copper', 'fibreglass (glass-reinforced plastic)', 'polythene'], 'C', 'Reinforced concrete is another composite.', 1],
        ['The ability of a metal to be hammered or rolled into thin sheets is', ['conductivity', 'brittleness', 'elasticity', 'malleability'], 'D', 'Gold and aluminium are very malleable.', 1],
        ['Brinell and Rockwell tests measure a material\'s', ['density', 'hardness', 'colour', 'electrical resistance'], 'B', 'They press an indenter into the surface and measure the dent.', 2],
    ],

    'SHS 1: Rapid prototyping' => [
        ['Stereolithography (SLA) 3D printing builds parts by', ['cutting metal blocks', 'melting plastic filament', 'curing liquid resin layer by layer with a UV laser', 'casting molten iron'], 'C', 'SLA gives very fine detail.', 2],
        ['Rapid prototyping mainly helps manufacturers to', ['replace all workers', 'mass-produce at the lowest cost', 'avoid design', 'test designs quickly before investing in tooling'], 'D', 'Problems are found early, saving money.', 1],
    ],

    'SHS 1: Marking out, measurement and gauging' => [
        ['Which tool is used to scratch fine lines on metal when marking out?', ['chisel', 'scriber', 'file', 'hacksaw'], 'B', 'The lines are easier to see on metal coated with marking blue.', 1],
        ['Odd-leg (jenny) calipers are used to', ['cut threads', 'measure internal diameters only', 'mark lines parallel to an edge', 'drill holes'], 'C', 'One leg runs along the edge while the other scribes a line.', 2],
        ['A gauge that checks whether a part is within its size limits without measuring the exact size is a', ['protractor', 'micrometer', 'steel rule', 'go/no-go gauge'], 'D', 'The part must pass the "go" end and not the "no-go" end.', 2],
        ['A vernier caliper shows 12 mm on the main scale and the vernier reading is 0.36 mm. What is the measurement?', ['12.036 mm', '12.36 mm', '12.6 mm', '15.6 mm'], 'B', 'Add the two readings: 12 + 0.36 = 12.36 mm.', 2],
        ['A centre punch is used to', ['measure angles', 'cut sheet metal', 'make a small dent so a drill bit starts in the right place', 'bend rods'], 'C', 'The dent stops the drill from wandering.', 1],
    ],

    'SHS 2: Mechanical properties of materials' => [
        ['The point beyond which a material will not return to its original shape is its', ['hardness', 'melting point', 'density', 'elastic limit'], 'D', 'Beyond it the material deforms permanently.', 2],
        ['Cast iron breaks suddenly with little bending. It is described as', ['ductile', 'brittle', 'malleable', 'elastic'], 'B', 'Brittle materials snap without warning.', 1],
        ['Failure of a part after many repeated cycles of loading, even below its breaking strength, is', ['creep at room temperature only', 'corrosion', 'fatigue failure', 'melting'], 'C', 'Aircraft and vehicle parts are inspected for fatigue cracks.', 3],
    ],

    'SHS 2: Drawing and modelling for manufacture' => [
        ['The permitted variation on a dimension, shown on an engineering drawing, is the', ['tolerance', 'scale', 'title block', 'projection symbol'], 'A', 'For example, 50 ± 0.1 mm.', 1],
        ['A 3D computer model of a part is mainly used in manufacture to', ['check the design and produce instructions for machines', 'decorate the factory', 'replace all drawings with sketches', 'store raw materials'], 'A', 'CAD models link to CAM and simulation.', 1],
    ],

    'SHS 2: Casting, joining and safety' => [
        ['Forcing molten metal into a steel mould under pressure is', ['die casting', 'sand casting', 'forging', 'extrusion'], 'A', 'It gives accurate parts in large numbers.', 2],
        ['Joining metals with a filler metal that melts above 450 °C but below the parent metals\' melting point is', ['brazing', 'welding', 'soft soldering', 'riveting'], 'A', 'Soft soldering uses fillers that melt below 450 °C.', 3],
        ['In MIG welding, the shielding gas protects the weld from', ['the oxygen and nitrogen in the air', 'sunlight', 'water', 'noise'], 'A', 'Air would make the weld weak and porous.', 3],
        ['Guards on machines in a workshop are there mainly to', ['keep hands and clothing away from moving parts', 'make the machine look better', 'speed up the machine', 'reduce the cost'], 'A', 'Never remove guards while a machine is running.', 1],
    ],
];
