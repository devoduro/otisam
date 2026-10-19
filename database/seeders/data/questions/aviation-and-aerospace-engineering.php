<?php

/*
| SHS Aviation and Aerospace Engineering (2023 curriculum STEM elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Aviation and Aerospace Engineering curriculum.
| Take g = 10 m/s² unless stated.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Forces of flight' => [
        ['Which force acts opposite to thrust on an aircraft in flight?', ['lift', 'weight', 'drag', 'torque'], 'C', 'Thrust pushes the aircraft forward and drag (air resistance) holds it back.', 1],
        ['Which force acts opposite to lift?', ['thrust', 'weight', 'drag', 'friction'], 'B', 'Lift acts upwards and weight, the pull of gravity, acts downwards.', 1],
        ['In straight and level flight at constant speed,', ['lift equals weight and thrust equals drag', 'lift is greater than weight', 'thrust is zero', 'drag is greater than thrust'], 'A', 'With no change in speed or height the forces are balanced: lift = weight and thrust = drag.', 2],
        ['An aircraft has a mass of 50,000 kg. What is its weight?', ['5,000 N', '50,000 N', '500,000 N', '5,000,000 N'], 'C', 'W = mg = 50,000 × 10 = 500,000 N.', 2],
        ['An aircraft weighs 20,000 N and has a wing area of 40 m². What is its wing loading?', ['800,000 N/m²', '0.002 N/m²', '20,040 N/m²', '500 N/m²'], 'D', 'Wing loading = weight ÷ wing area = 20,000 ÷ 40 = 500 N/m².', 3],
    ],

    'Wings and lift' => [
        ['Bernoulli\'s principle states that as the speed of a moving fluid increases, its pressure', ['increases', 'stays the same', 'decreases', 'doubles'], 'C', 'Faster-moving air has lower pressure. Air over a wing\'s curved top moves faster, which helps create lift.', 2],
        ['The cross-sectional shape of a wing designed to produce lift is an', ['aerofoil', 'fuselage', 'nacelle', 'empennage'], 'A', 'An aerofoil is curved so that air flowing over it produces lift.', 1],
        ['The angle between the wing\'s chord line and the oncoming airflow is the', ['dihedral angle', 'sweep angle', 'angle of attack', 'glide angle'], 'C', 'Raising the angle of attack increases lift, up to the critical (stalling) angle.', 2],
        ['A wing stalls when', ['the engine is switched off', 'the aircraft lands', 'the fuel tanks are full', 'the angle of attack goes past the critical angle and the airflow separates'], 'D', 'Past the critical angle the air can no longer follow the top surface, and lift drops sharply. A stall is about airflow, not the engine.', 3],
        ['Flaps are lowered during take-off and landing to', ['reduce lift', 'increase lift at low speeds', 'steer on the runway', 'cool the engines'], 'B', 'Flaps increase the wing\'s camber and area so the aircraft can fly safely at lower speeds.', 2],
    ],

    'Aircraft parts and control' => [
        ['The main body of an aircraft that carries the crew, passengers and cargo is the', ['wing', 'fuselage', 'tailplane', 'landing gear'], 'B', 'The fuselage is the central body to which the wings and tail are attached.', 1],
        ['Ailerons control an aircraft\'s', ['roll', 'pitch', 'yaw', 'thrust'], 'A', 'Ailerons on the wing tips move in opposite directions to bank (roll) the aircraft.', 2],
        ['The elevator controls an aircraft\'s', ['roll', 'pitch', 'yaw', 'speed only'], 'B', 'The elevator on the tailplane moves the nose up or down.', 2],
        ['The rudder controls an aircraft\'s', ['roll', 'pitch', 'lift', 'yaw'], 'D', 'The rudder on the fin swings the nose left or right.', 2],
        ['Yaw is rotation of an aircraft about its', ['longitudinal axis', 'lateral axis', 'vertical (normal) axis', 'wing tip'], 'C', 'Roll is about the longitudinal axis, pitch about the lateral axis and yaw about the vertical axis.', 3],
    ],

    'Instruments and safety' => [
        ['A pitot tube is used to measure', ['fuel quantity', 'engine temperature', 'airspeed', 'cabin pressure'], 'C', 'The pitot tube senses the pressure of the oncoming air, from which airspeed is worked out.', 2],
        ['The instrument that shows an aircraft\'s height is the', ['airspeed indicator', 'altimeter', 'compass', 'tachometer'], 'B', 'An altimeter works out height from the air pressure, which falls as altitude rises.', 1],
        ['The flight data recorder and cockpit voice recorder are commonly called', ['transponders', 'autopilots', 'radomes', 'black boxes'], 'D', 'Although they are painted bright orange, they are known as black boxes. They help investigators after accidents.', 1],
    ],

    'Propulsion and the atmosphere' => [
        ['Jet propulsion is best explained by', ['Newton\'s third law of motion', 'Ohm\'s law', 'Hooke\'s law', 'Boyle\'s law'], 'A', 'The engine pushes hot gas backwards, and the gas pushes the engine forwards with an equal and opposite force.', 2],
        ['In what order does air pass through a jet engine?', ['compressor, intake, turbine, combustion chamber, exhaust', 'intake, compressor, combustion chamber, turbine, exhaust', 'intake, turbine, compressor, exhaust, combustion chamber', 'exhaust, turbine, combustion chamber, compressor, intake'], 'B', 'Air is drawn in, squeezed by the compressor, burnt with fuel, spins the turbine (which drives the compressor) and leaves as exhaust.', 3],
        ['A rocket can work in space, where there is no air, because', ['it pushes against the air', 'there is no gravity in space', 'it uses wings for lift', 'it carries its own oxidiser along with its fuel'], 'D', 'A rocket needs no outside air: it carries oxidiser to burn its fuel and pushes exhaust out backwards.', 3],
        ['Most of the Earth\'s weather happens in the', ['stratosphere', 'mesosphere', 'troposphere', 'thermosphere'], 'C', 'The troposphere is the lowest layer, holding most of the water vapour, clouds and weather.', 1],
        ['As altitude increases, air density', ['increases', 'decreases', 'stays the same', 'becomes infinite'], 'B', 'Air is thinner higher up, so wings and engines get less air to work with.', 1],
        ['An aircraft flying at Mach 2 is flying at', ['twice the speed of sound', 'half the speed of sound', 'the speed of light', '2 km/h'], 'A', 'The Mach number is the speed divided by the local speed of sound.', 2],
        ['Taking the speed of sound as 340 m/s, how fast is an aircraft flying at Mach 0.8?', ['425 m/s', '340.8 m/s', '272 m/s', '68 m/s'], 'C', 'Speed = 0.8 × 340 = 272 m/s.', 2],
    ],

    'Space and orbits' => [
        ['A satellite in geostationary orbit takes how long to go round the Earth once?', ['12 hours', 'about 24 hours (one day)', '90 minutes', 'one year'], 'B', 'It matches the Earth\'s rotation, so it stays above the same point on the equator.', 2],
        ['The first crewed Moon landing, Apollo 11, took place in', ['1957', '1961', '1969', '1981'], 'C', 'Neil Armstrong and Buzz Aldrin landed on the Moon in July 1969.', 1],
        ['GhanaSat-1, Ghana\'s first satellite, was built by students of', ['All Nations University', 'KNUST', 'University of Ghana', 'Ashesi University'], 'A', 'GhanaSat-1, a small CubeSat, was built at All Nations University in Koforidua and launched in 2017.', 3],
    ],

    'History, materials and the industry' => [
        ['The Wright brothers made the first powered, controlled aeroplane flight in', ['1783', '1903', '1927', '1969'], 'B', 'Orville and Wilbur Wright flew at Kitty Hawk, USA, in December 1903.', 1],
        ['Aircraft structures often use aluminium alloys and composites mainly because they are', ['cheap and heavy', 'magnetic', 'strong and light', 'good sound insulators only'], 'C', 'A high strength-to-weight ratio means less fuel is needed to lift the aircraft.', 2],
        ['The body that regulates civil aviation in Ghana is the', ['Ghana Civil Aviation Authority (GCAA)', 'Ghana Ports and Harbours Authority', 'National Communications Authority', 'Ghana Highway Authority'], 'A', 'The GCAA oversees air safety, licensing and air navigation services in Ghana.', 1],
        ['Kotoka International Airport is located in', ['Kumasi', 'Accra', 'Tamale', 'Takoradi'], 'B', 'Kotoka International Airport in Accra is Ghana\'s main international airport.', 1],
    ],

    'SHS 1: Avionics and the aviation industry' => [
        ['The electronic systems used on aircraft, such as radios, navigation and displays, are called', ['aerodynamics', 'avionics', 'propulsion', 'airframes'], 'B', 'The word comes from "aviation electronics".', 1],
        ['An aircraft transponder', ['controls the flaps', 'pumps fuel', 'replies to radar signals with the aircraft\'s identity and altitude', 'records cabin music'], 'C', 'It helps air traffic controllers identify each aircraft.', 2],
        ['The United Nations agency that sets international standards for civil aviation is', ['FIFA', 'IATA', 'WHO', 'ICAO'], 'D', 'The International Civil Aviation Organization is based in Montreal.', 2],
    ],

    'SHS 1: Unmanned aerial vehicles (UAVs)' => [
        ['A drone with four rotors is called a', ['fixed-wing UAV', 'quadcopter', 'helicopter with one rotor', 'glider'], 'B', 'Quadcopters can hover and take off vertically.', 1],
        ['Compared with a multirotor drone, a fixed-wing UAV usually', ['needs no runway or launch method', 'hovers better', 'flies longer distances on the same battery', 'is easier to keep still'], 'C', 'Wings give lift efficiently in forward flight.', 2],
        ['In Ghana, drones have been used since 2019 to', ['spray cocoa farms only', 'carry passengers', 'replace all ambulances', 'deliver medical supplies such as blood and vaccines to health facilities'], 'D', 'Fixed-wing delivery drones drop supplies by parachute.', 1],
        ['The part of a drone that processes sensor data and keeps it stable is the', ['propeller', 'flight controller', 'landing gear', 'battery strap'], 'B', 'It adjusts motor speeds many times a second.', 2],
    ],

    'SHS 2: Weather and the standard atmosphere' => [
        ['In the International Standard Atmosphere, the temperature at sea level is', ['25 °C', '0 °C', '15 °C', '−56.5 °C'], 'C', 'The standard sea-level pressure is 1013.25 hPa.', 2],
        ['Which clouds are associated with thunderstorms, turbulence and icing?', ['altostratus', 'cirrus', 'stratus', 'cumulonimbus'], 'D', 'Pilots avoid flying through cumulonimbus clouds.', 2],
        ['In the lower atmosphere, the standard temperature lapse rate is about', ['10 °C per 1,000 ft', '2 °C per 1,000 ft', '0.5 °C per 1,000 ft', '20 °C per 1,000 ft'], 'B', 'This is about 6.5 °C per kilometre.', 3],
        ['The harmattan affects flights in Ghana mainly by', ['raising humidity', 'causing snow', 'reducing visibility with dust haze', 'producing hurricanes'], 'C', 'Low visibility can delay or divert flights.', 1],
    ],

    'SHS 2: Aerodynamic forces' => [
        ['If an aircraft\'s airspeed doubles while everything else stays the same, its lift becomes', ['unchanged', 'twice as large', 'half as large', 'four times as large'], 'D', 'Lift is proportional to the square of the speed: L = ½ρv²SCL.', 3],
        ['Drag caused by the production of lift, linked to wingtip vortices, is', ['parasite drag', 'induced drag', 'skin friction only', 'form drag'], 'B', 'Long, slender wings and winglets reduce induced drag.', 3],
        ['Loading an aircraft so that its centre of gravity is too far aft can make it', ['unable to taxi', 'more fuel efficient and safer', 'unstable and hard to control in pitch', 'heavier'], 'C', 'The centre of gravity must stay within set limits.', 3],
    ],

    'SHS 2: Propulsion systems' => [
        ['A jet engine takes in air at 200 m/s and expels it at 600 m/s, with a mass flow of 50 kg/s. What thrust does it produce?', ['10,000 N', '40,000 N', '30,000 N', '20,000 N'], 'D', 'Thrust = mass flow × change in velocity = 50 × (600 − 200) = 20,000 N.', 3],
        ['A rocket expels gas at 2,500 m/s at a rate of 100 kg/s. What thrust does it produce?', ['25,000 N', '250,000 N', '2,600 N', '2,500,000 N'], 'B', 'Thrust = mass flow × exhaust velocity = 100 × 2,500 = 250,000 N.', 3],
        ['A propeller produces thrust by', ['pushing against the ground', 'burning fuel in its blades', 'accelerating a large mass of air backwards', 'creating a vacuum ahead'], 'C', 'The blades are rotating aerofoils.', 2],
    ],

    'SHS 2: Aircraft instruments' => [
        ['The instrument that shows the aircraft\'s pitch and bank relative to the horizon is the', ['fuel gauge', 'altimeter', 'airspeed indicator', 'attitude indicator (artificial horizon)'], 'D', 'It is essential when flying in cloud.', 2],
        ['The vertical speed indicator shows', ['how fast the aircraft is climbing or descending', 'its heading', 'its engine speed', 'the outside air temperature'], 'A', 'It is usually shown in feet per minute.', 2],
        ['Engine speed in revolutions per minute is shown on the', ['tachometer', 'altimeter', 'compass', 'turn coordinator'], 'A', 'Piston-engine aircraft use a tachometer.', 2],
    ],

    'SHS 2: Aviation bodies and UAV regulations' => [
        ['Before flying certain drones in Ghana, operators must follow the rules of the', ['Ghana Civil Aviation Authority', 'Ghana Tourism Authority', 'Electoral Commission', 'Forestry Commission'], 'A', 'The GCAA sets rules on registration, permits and where drones may fly.', 1],
        ['Which is a basic safety rule for flying a drone?', ['keep it away from airports and within visual line of sight', 'fly over crowds', 'fly as high as possible near runways', 'fly at night without lights'], 'A', 'Drones near airports can endanger manned aircraft.', 1],
    ],

    'SHS 3: Aircraft structures, stability and maintenance' => [
        ['A structure in which the outer skin carries most of the loads is a', ['monocoque structure', 'truss structure', 'space frame', 'tube-and-fabric frame'], 'A', 'Semi-monocoque designs add frames and stringers to support the skin.', 3],
        ['The upward angle of the wings from root to tip (dihedral) improves', ['lateral (roll) stability', 'engine power', 'fuel capacity', 'braking'], 'A', 'When one wing drops, the dihedral helps the aircraft level itself.', 3],
        ['The horizontal tailplane mainly provides', ['longitudinal (pitch) stability', 'lift for take-off', 'thrust', 'directional control only'], 'A', 'The fin gives directional (yaw) stability.', 2],
        ['A walk-around check of the aircraft by the pilot before each flight is the', ['pre-flight inspection', 'overhaul', 'certification', 'landing check'], 'A', 'It looks for damage, leaks and correct fuel and oil levels.', 1],
    ],
];
