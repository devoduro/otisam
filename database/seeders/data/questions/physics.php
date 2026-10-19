<?php

/*
| SHS Physics: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
| Take g = 10 m/s² unless stated.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Measurement and motion' => [
        ['Which of the following is a fundamental quantity?', ['velocity', 'force', 'mass', 'pressure'], 'C', 'The fundamental quantities include mass, length, time, temperature and current. The others are derived.', 1],
        ['The dimensions of velocity are', ['LT', 'LT⁻¹', 'LT⁻²', 'MLT⁻¹'], 'B', 'Velocity = length ÷ time, so its dimensions are LT⁻¹.', 2],
        ['A car starts from rest and accelerates uniformly at 2 m/s² for 5 s. Find its final velocity.', ['2.5 m/s', '7 m/s', '10 m/s', '25 m/s'], 'C', 'v = u + at = 0 + 2 × 5 = 10 m/s.', 2],
        ['A body starts from rest and accelerates at 4 m/s². How far does it travel in 3 s?', ['6 m', '12 m', '18 m', '36 m'], 'C', 's = ut + ½at² = 0 + ½ × 4 × 9 = 18 m.', 3],
        ['A stone is dropped from rest from the top of a building and hits the ground after 2 s. Find the height of the building.', ['10 m', '20 m', '40 m', '5 m'], 'B', 'h = ½gt² = ½ × 10 × 4 = 20 m.', 3],
        ['The area under a velocity–time graph represents', ['acceleration', 'distance (displacement)', 'force', 'momentum'], 'B', 'Area = velocity × time = displacement.', 2],
        ['The slope of a velocity–time graph gives the', ['distance', 'acceleration', 'speed', 'momentum'], 'B', 'Slope = change in velocity ÷ time = acceleration.', 2],
        ['Find the momentum of a 1,000 kg car moving at 20 m/s.', ['50 kg m/s', '2,000 kg m/s', '20,000 kg m/s', '200,000 kg m/s'], 'C', 'p = mv = 1000 × 20 = 20,000 kg m/s.', 2],
        ['Newton\'s first law of motion is also called the law of', ['acceleration', 'inertia', 'action and reaction', 'gravitation'], 'B', 'The first law says a body stays at rest or in uniform motion unless acted on by a resultant force: inertia.', 1],
        ['The tendency of a body to resist a change in its state of rest or motion is', ['momentum', 'inertia', 'friction', 'impulse'], 'B', 'Inertia depends on mass.', 1],
    ],

    'Forces, work, energy, machines' => [
        ['A force of 50 N acts on an area of 2 m². Find the pressure.', ['25 Pa', '48 Pa', '52 Pa', '100 Pa'], 'A', 'P = F/A = 50 ÷ 2 = 25 Pa.', 1],
        ['A machine has a velocity ratio of 5 and an efficiency of 80%. Find its mechanical advantage.', ['4', '5', '6.25', '400'], 'A', 'Efficiency = MA/VR, so MA = 0.8 × 5 = 4.', 3],
        ['A spring stretches by 4 cm under a load of 8 N. What load will stretch it by 6 cm (within the elastic limit)?', ['10 N', '12 N', '14 N', '16 N'], 'B', 'By Hooke\'s law, k = 8/4 = 2 N/cm, so F = 2 × 6 = 12 N.', 3],
        ['Which of the following is NOT a unit of energy?', ['joule', 'kilowatt-hour', 'electron-volt', 'watt'], 'D', 'The watt is a unit of power (J/s), not energy.', 2],
        ['Pressure in a liquid increases with', ['depth', 'surface area only', 'the shape of the container', 'the volume of the container'], 'A', 'p = hρg: pressure increases with depth and density.', 2],
        ['A body floats in water because', ['its weight is greater than the upthrust', 'the upthrust equals its weight', 'it has no weight', 'water has no density'], 'B', 'By the law of flotation, a floating body displaces its own weight of fluid.', 2],
    ],

    'Heat' => [
        ['The temperature 27 °C on the Kelvin scale is', ['246 K', '273 K', '300 K', '327 K'], 'C', 'T(K) = θ(°C) + 273 = 27 + 273 = 300 K.', 1],
        ['The amount of heat needed to raise the temperature of 1 kg of a substance by 1 K is its', ['heat capacity', 'specific heat capacity', 'latent heat', 'thermal conductivity'], 'B', 'Specific heat capacity is in J kg⁻¹ K⁻¹.', 2],
        ['How much heat is needed to raise 2 kg of water from 20 °C to 70 °C? (c = 4200 J kg⁻¹ K⁻¹)', ['42,000 J', '210,000 J', '420,000 J', '588,000 J'], 'C', 'Q = mcΔθ = 2 × 4200 × 50 = 420,000 J.', 3],
        ['The heat absorbed when a solid changes to a liquid without a change in temperature is', ['latent heat of fusion', 'latent heat of vaporisation', 'specific heat', 'heat capacity'], 'A', 'Latent heat of fusion breaks the bonds of the solid at its melting point.', 2],
        ['The transfer of heat in liquids and gases by the movement of the fluid is', ['conduction', 'evaporation', 'radiation', 'convection'], 'D', 'Warm fluid rises and cool fluid sinks, creating convection currents.', 1],
    ],

    'Waves, light, sound' => [
        ['A wave has a frequency of 50 Hz and a wavelength of 4 m. Find its speed.', ['12.5 m/s', '54 m/s', '200 m/s', '0.08 m/s'], 'C', 'v = fλ = 50 × 4 = 200 m/s.', 2],
        ['Which of the following is a longitudinal wave?', ['light', 'radio waves', 'sound', 'X-rays'], 'C', 'In sound waves the particles vibrate along the direction of travel.', 2],
        ['Which electromagnetic wave has the longest wavelength?', ['gamma rays', 'X-rays', 'visible light', 'radio waves'], 'D', 'Radio waves have the longest wavelength and lowest frequency.', 2],
        ['The refractive index of a medium is 1.5. If the speed of light in air is 3 × 10⁸ m/s, find the speed of light in the medium.', ['1.5 × 10⁸ m/s', '3 × 10⁸ m/s', '4.5 × 10⁸ m/s', '2 × 10⁸ m/s'], 'D', 'n = c/v, so v = 3 × 10⁸ ÷ 1.5 = 2 × 10⁸ m/s.', 3],
        ['Total internal reflection can occur when light travels from', ['glass into air at an angle greater than the critical angle', 'air into glass', 'air into water', 'a vacuum into air'], 'A', 'It needs light going from a denser to a less dense medium at more than the critical angle.', 3],
        ['A converging lens has a focal length of 10 cm. An object is placed 30 cm from it. Find the image distance.', ['7.5 cm', '30 cm', '20 cm', '15 cm'], 'D', '1/v = 1/f − 1/u = 1/10 − 1/30 = 2/30, so v = 15 cm (real image).', 4],
        ['Echoes are produced by the', ['reflection of sound', 'refraction of sound', 'diffraction of sound', 'absorption of sound'], 'A', 'An echo is sound reflected from a hard surface.', 1],
    ],

    'Electricity and magnetism' => [
        ['A current of 2 A flows for 30 s. How much charge passes?', ['15 C', '28 C', '32 C', '60 C'], 'D', 'Q = It = 2 × 30 = 60 C.', 2],
        ['An electric iron rated 1000 W is used for 2 hours. How much energy does it use in kWh?', ['2 kWh', '0.5 kWh', '20 kWh', '2,000 kWh'], 'A', 'E = P × t = 1 kW × 2 h = 2 kWh.', 2],
        ['A transformer has 100 turns on its primary and 500 turns on its secondary. If the primary voltage is 240 V, find the secondary voltage.', ['48 V', '240 V', '1,200 V', '12,000 V'], 'C', 'Vs/Vp = Ns/Np, so Vs = 240 × 500/100 = 1,200 V (a step-up transformer).', 3],
        ['The device used to protect an electrical circuit from excess current is a', ['fuse', 'transformer', 'rheostat', 'capacitor'], 'A', 'A fuse wire melts and breaks the circuit when the current is too large.', 1],
        ['Which of the following materials is ferromagnetic?', ['copper', 'brass', 'aluminium', 'iron'], 'D', 'Iron, nickel and cobalt are strongly magnetic.', 1],
    ],

    'Modern physics' => [
        ['An alpha particle is the same as the nucleus of a', ['hydrogen atom', 'uranium atom', 'carbon atom', 'helium atom'], 'D', 'An alpha particle has 2 protons and 2 neutrons, like a helium nucleus.', 2],
        ['The half-life of a radioactive sample is 5 days. What fraction of it remains after 15 days?', ['1/2', '1/3', '1/4', '1/8'], 'D', '15 days is 3 half-lives: (½)³ = 1/8.', 3],
        ['The emission of electrons from a metal surface when light falls on it is the', ['photoelectric effect', 'thermionic emission', 'Compton effect', 'radioactivity'], 'A', 'In the photoelectric effect, light of high enough frequency releases electrons.', 2],
    ],

    'SHS 1: Introduction to physics and matter' => [
        ['What is the SI unit of temperature?', ['degree Celsius', 'degree Fahrenheit', 'joule', 'kelvin'], 'D', 'The kelvin is the SI base unit of thermodynamic temperature.', 1],
        ['A block has a mass of 600 g and a volume of 200 cm³. What is its density?', ['400 g/cm³', '0.33 g/cm³', '120,000 g/cm³', '3 g/cm³'], 'D', 'Density = mass ÷ volume = 600 ÷ 200 = 3 g/cm³.', 1],
        ['Which instrument measures the diameter of a thin wire most accurately?', ['metre rule', 'measuring tape', 'micrometer screw gauge', 'vernier caliper'], 'C', 'A micrometer reads to 0.01 mm; a vernier caliper to about 0.1 mm or 0.02 mm.', 2],
        ['According to the kinetic theory, the particles of a gas', ['are closely packed and vibrate in fixed positions', 'move freely and randomly at high speed', 'do not move at all', 'are larger than those of solids'], 'B', 'Gas particles are far apart and move randomly, colliding with each other and the walls.', 1],
    ],

    'SHS 1: Motion and pressure' => [
        ['What is the pressure 2 m below the surface of water? (Density of water = 1,000 kg/m³)', ['2,000 Pa', '20,000 Pa', '200 Pa', '5,000 Pa'], 'B', 'p = ρgh = 1,000 × 10 × 2 = 20,000 Pa, ignoring air pressure above the water.', 2],
        ['Atmospheric pressure is measured with a', ['thermometer', 'hydrometer', 'manometer only', 'barometer'], 'D', 'A barometer measures air pressure; a manometer measures gas pressure differences.', 1],
        ['A hydraulic press works because pressure applied to an enclosed liquid', ['turns into heat', 'is lost at the walls', 'acts only downwards', 'is transmitted equally in all directions'], 'D', 'This is Pascal\'s principle: a small force on a small piston gives a large force on a large piston.', 2],
        ['A body moves with uniform velocity. Its acceleration is', ['equal to its velocity', 'constant and positive', 'increasing', 'zero'], 'D', 'Uniform velocity means no change in velocity, so no acceleration.', 1],
    ],

    'SHS 1: Thermometers and temperature' => [
        ['Convert 27 °C to kelvin.', ['246 K', '300 K', '273 K', '327 K'], 'B', 'T(K) = θ(°C) + 273 = 27 + 273 = 300 K.', 1],
        ['The narrow constriction in a clinical thermometer', ['speeds up heating', 'makes the thermometer heavier', 'measures pressure', 'stops the mercury falling back before it is read'], 'D', 'The mercury thread breaks at the constriction, so the reading stays until the thermometer is shaken.', 2],
        ['Alcohol, rather than mercury, is used in thermometers for very cold places because alcohol', ['has a high boiling point', 'is silver-coloured', 'conducts electricity', 'has a very low freezing point'], 'D', 'Alcohol freezes at about −115 °C; mercury freezes at about −39 °C.', 2],
        ['A thermometer that uses the voltage produced at the junction of two different metals is a', ['six\'s thermometer', 'clinical thermometer', 'thermocouple', 'gas thermometer'], 'C', 'Thermocouples measure very high temperatures, such as in furnaces.', 2],
    ],

    'SHS 1: Mirrors, reflection and refraction' => [
        ['You look at yourself in a plane mirror. Your image is', ['real and inverted', 'virtual, upright and laterally inverted', 'real and magnified', 'virtual and diminished'], 'B', 'A plane-mirror image is the same size, upright and reversed left to right.', 1],
        ['Which mirror is used in torches and car headlamps to give a parallel beam?', ['no mirror', 'convex mirror', 'plane mirror', 'concave mirror'], 'D', 'A lamp at the focus of a concave mirror gives a parallel beam.', 2],
        ['Light passes from air into glass with an angle of incidence of 60° and an angle of refraction of 30°. What is the refractive index? (sin 60° = 0.866, sin 30° = 0.5)', ['0.58', '2.0', '1.73', '1.5'], 'C', 'n = sin i ÷ sin r = 0.866 ÷ 0.5 ≈ 1.73.', 3],
        ['Total internal reflection can happen only when light travels', ['through a vacuum', 'from air into water', 'along the normal', 'from a denser to a less dense medium at more than the critical angle'], 'D', 'Both conditions are needed: denser to less dense, and an angle of incidence above the critical angle.', 3],
    ],

    'SHS 1: Light through different media' => [
        ['Light travels at 3 × 10⁸ m/s in air. What is its speed in glass of refractive index 1.5?', ['4.5 × 10⁸ m/s', '2 × 10⁸ m/s', '3 × 10⁸ m/s', '1.5 × 10⁸ m/s'], 'B', 'v = c ÷ n = 3 × 10⁸ ÷ 1.5 = 2 × 10⁸ m/s.', 3],
        ['Optical fibres carry light by', ['absorption', 'diffraction', 'total internal reflection', 'dispersion'], 'C', 'Light reflects repeatedly off the inside walls of the fibre.', 2],
        ['Which colour of light is bent most by a glass prism?', ['red', 'yellow', 'green', 'violet'], 'D', 'Violet has the shortest wavelength and is refracted most.', 2],
        ['A red shirt looks black under pure blue light because it', ['turns the light green', 'reflects blue light', 'gives out red light', 'absorbs the blue light and reflects nothing'], 'D', 'A red object reflects only red light; with no red light falling on it, it looks black.', 2],
    ],

    'SHS 1: Electric charge and magnetism' => [
        ['Two negatively charged balloons are brought close together. They will', ['attract', 'repel', 'stick together', 'not affect each other'], 'B', 'Like charges repel; unlike charges attract.', 1],
        ['A gold-leaf electroscope is used to', ['store electricity', 'measure current', 'detect electric charge', 'measure temperature'], 'C', 'The leaf rises when the electroscope is charged.', 1],
        ['Which of these materials is strongly attracted by a magnet?', ['copper', 'aluminium', 'iron', 'brass'], 'C', 'Iron, nickel and cobalt are ferromagnetic.', 1],
        ['A magnet can be demagnetised by', ['painting it', 'keeping it with keepers', 'storing it in pairs', 'heating it strongly or hammering it'], 'D', 'Heat and vibration disorder the magnetic domains.', 2],
    ],

    'SHS 1: Semiconductors and transducers' => [
        ['An n-type semiconductor is made by doping silicon with', ['copper', 'a trivalent element such as boron', 'a pentavalent element such as phosphorus', 'oxygen'], 'C', 'Phosphorus has five outer electrons, so it adds free electrons, the majority carriers.', 3],
        ['In a p-type semiconductor, the majority charge carriers are', ['electrons', 'holes', 'protons', 'neutrons'], 'B', 'Trivalent impurities create holes, which act as positive carriers.', 3],
        ['A microphone is a transducer that changes', ['heat into sound', 'electrical energy into sound', 'light into heat', 'sound energy into electrical energy'], 'D', 'A loudspeaker does the reverse.', 1],
        ['Which device changes electrical energy into sound?', ['microphone', 'loudspeaker', 'solar cell', 'thermistor'], 'B', 'A loudspeaker is an output transducer.', 1],
    ],

    'SHS 1: Atoms and the nucleus' => [
        ['The nucleus of an atom contains', ['protons and electrons', 'electrons only', 'protons and neutrons', 'neutrons and electrons'], 'C', 'Electrons orbit outside the nucleus.', 1],
        ['Isotopes of an element have the same number of', ['neutrons but different numbers of protons', 'protons but different numbers of neutrons', 'nucleons', 'electrons and neutrons'], 'B', 'Isotopes share the atomic number but differ in mass number.', 2],
        ['An alpha particle is the same as', ['a neutron', 'an electron', 'a photon', 'a helium nucleus'], 'D', 'It is two protons and two neutrons.', 2],
        ['A radioactive sample of 80 g has a half-life of 5 days. How much is left after 15 days?', ['40 g', '20 g', '10 g', '5 g'], 'C', '15 days is 3 half-lives: 80 → 40 → 20 → 10 g.', 2],
    ],

    'SHS 2: Dimensions, vectors, flotation and deformation' => [
        ['What are the dimensions of force?', ['ML²T⁻²', 'MLT⁻¹', 'MLT⁻²', 'MT⁻²'], 'C', 'Force = mass × acceleration = M × LT⁻².', 2],
        ['Forces of 3 N and 4 N act at right angles on a body. What is the size of their resultant?', ['7 N', '1 N', '5 N', '12 N'], 'C', 'R = √(3² + 4²) = √25 = 5 N.', 2],
        ['A spring with a force constant of 200 N/m is stretched by 5 cm. What force is needed?', ['4 N', '1,000 N', '40 N', '10 N'], 'D', 'F = kx = 200 × 0.05 = 10 N, within the elastic limit.', 2],
        ['The law of flotation states that a floating object', ['displaces its own volume of fluid', 'has no weight', 'displaces its own weight of the fluid in which it floats', 'is always denser than the fluid'], 'C', 'For floating, upthrust equals weight, and upthrust equals the weight of fluid displaced.', 2],
    ],

    'SHS 2: Measurement of heat' => [
        ['How much heat is needed to raise the temperature of 2 kg of water by 10 °C? (c = 4,200 J/kg K)', ['8,400 J', '84,000 J', '840 J', '42,000 J'], 'B', 'Q = mcΔθ = 2 × 4,200 × 10 = 84,000 J.', 2],
        ['The heat needed to change 1 kg of a solid to liquid at its melting point, without a change in temperature, is its', ['specific heat capacity', 'specific latent heat of fusion', 'heat capacity', 'thermal conductivity'], 'B', 'Latent heat changes the state, not the temperature.', 2],
        ['The heat capacity of a body is the heat needed to', ['boil it', 'melt 1 kg of it', 'raise 1 kg of it by 1 K', 'raise the temperature of the whole body by 1 K'], 'D', 'Heat capacity = mass × specific heat capacity, in J/K.', 3],
        ['Steam at 100 °C causes worse burns than boiling water at 100 °C because steam', ['contains less energy', 'is hotter', 'gives out extra latent heat when it condenses on the skin', 'is a better conductor'], 'C', 'Each kilogram of steam releases about 2.26 MJ as it condenses.', 2],
    ],

    'SHS 2: Electrostatics' => [
        ['If the distance between two point charges is doubled, the force between them becomes', ['twice as large', 'half as large', 'one quarter as large', 'four times as large'], 'C', 'Coulomb\'s law: F ∝ 1/r², so doubling r divides F by 4.', 3],
        ['A capacitor stores 6 μC of charge when the voltage across it is 3 V. What is its capacitance?', ['18 μF', '2 μF', '0.5 μF', '9 μF'], 'B', 'C = Q ÷ V = 6 ÷ 3 = 2 μF.', 2],
        ['Capacitors of 2 μF and 3 μF are connected in parallel. What is the total capacitance?', ['1.2 μF', '5 μF', '6 μF', '1 μF'], 'B', 'In parallel, capacitances add: 2 + 3 = 5 μF.', 2],
        ['A lightning conductor protects a building by', ['making the building positive', 'storing the lightning', 'repelling clouds', 'safely conducting charge to the earth'], 'D', 'Its sharp points and a thick conductor lead charge harmlessly to the ground.', 1],
    ],

    'SHS 2: Photoelectric effect and radioactivity' => [
        ['In the photoelectric effect, electrons are emitted from a metal surface only when', ['the metal is cold', 'the light is very bright, whatever its frequency', 'the light\'s frequency is above the threshold frequency', 'the light is red'], 'C', 'Each photon must have enough energy (hf) to free an electron.', 3],
        ['The energy of a photon is given by', ['E = IVt', 'E = mgh', 'E = ½mv²', 'E = hf'], 'D', 'h is Planck\'s constant and f the frequency.', 2],
        ['Which radiation is the most penetrating?', ['alpha', 'beta', 'gamma', 'visible light'], 'C', 'Gamma rays need thick lead or concrete to stop them.', 1],
        ['A beta particle is', ['a neutron', 'a helium nucleus', 'a fast-moving electron', 'a photon'], 'C', 'It is emitted when a neutron in the nucleus turns into a proton.', 2],
    ],

    'SHS 2: Projectiles, friction and circular motion' => [
        ['A ball rolls off a table 1.25 m high. How long does it take to reach the floor?', ['0.25 s', '0.5 s', '1.25 s', '2.5 s'], 'B', 'h = ½gt², so t = √(2 × 1.25 ÷ 10) = √0.25 = 0.5 s.', 3],
        ['A projectile reaches its greatest range on level ground when launched at', ['30°', '45°', '60°', '90°'], 'B', 'Range = u² sin 2θ ÷ g, which is largest when 2θ = 90°.', 2],
        ['A body moving in a circle at constant speed has an acceleration directed', ['upwards', 'away from the centre', 'along the tangent', 'towards the centre'], 'D', 'This centripetal acceleration constantly changes the direction of the velocity.', 2],
        ['A force of 20 N is needed to slide a box whose weight is 100 N at steady speed. What is the coefficient of friction?', ['2', '5', '0.2', '0.02'], 'C', 'μ = F ÷ R = 20 ÷ 100 = 0.2.', 2],
    ],

    'SHS 2: Electromagnetism' => [
        ['A wire 0.2 m long carries 4 A at right angles to a magnetic field of 0.5 T. What force acts on it?', ['10 N', '4 N', '1.6 N', '0.4 N'], 'D', 'F = BIL = 0.5 × 4 × 0.2 = 0.4 N.', 3],
        ['Fleming\'s left-hand rule gives the direction of the', ['magnetic field around a wire', 'induced current in a generator', 'force on a current-carrying conductor in a magnetic field', 'electric field'], 'C', 'It is used for motors; the right-hand rule is for generators.', 2],
        ['An electric bell works because it contains', ['a diode', 'a permanent magnet only', 'a capacitor', 'an electromagnet'], 'D', 'The electromagnet pulls the hammer, which breaks the circuit, so the action repeats.', 1],
        ['The strength of an electromagnet can be increased by', ['reducing the current', 'using a copper core', 'increasing the current and the number of turns', 'removing the core'], 'C', 'More current, more turns and a soft iron core all strengthen it.', 1],
    ],

    'SHS 2: Waves' => [
        ['A wave has a frequency of 50 Hz and a wavelength of 4 m. What is its speed?', ['12.5 m/s', '200 m/s', '54 m/s', '0.08 m/s'], 'B', 'v = fλ = 50 × 4 = 200 m/s.', 1],
        ['Which of these is a longitudinal wave?', ['light', 'sound', 'radio', 'X-rays'], 'B', 'In sound the particles vibrate along the direction the wave travels.', 1],
        ['Sound cannot travel through', ['water', 'steel', 'a vacuum', 'air'], 'C', 'Sound needs a material medium.', 1],
        ['The bending of waves around obstacles or through gaps is', ['reflection', 'diffraction', 'refraction', 'interference'], 'B', 'Diffraction is greatest when the gap is about the size of the wavelength.', 2],
    ],

    'SHS 2: Electric fields, magnetic fields and electronics' => [
        ['Electric field lines point', ['in no particular direction', 'from negative to positive charges', 'in circles around charges', 'from positive to negative charges'], 'D', 'The field direction is the direction of the force on a positive test charge.', 2],
        ['A transistor has three terminals called the', ['live, neutral and earth', 'anode, cathode and grid', 'emitter, base and collector', 'source, drain and fuse'], 'C', 'A small base current controls a larger collector current.', 2],
        ['The magnetic field lines around a straight wire carrying current are', ['straight lines away from the wire', 'straight lines parallel to the wire', 'absent', 'concentric circles around the wire'], 'D', 'The right-hand grip rule gives their direction.', 2],
    ],

    'SHS 3: Thermal expansion' => [
        ['A bimetallic strip is used in a thermostat because', ['it never expands', 'it conducts heat poorly', 'the two metals expand by different amounts and the strip bends', 'it melts at a low temperature'], 'C', 'The bending makes or breaks the electrical contact.', 2],
        ['Gaps are left between lengths of railway line to', ['reduce noise', 'save steel', 'let water drain', 'allow for expansion in hot weather'], 'D', 'Without gaps, expanding rails would buckle.', 1],
        ['A steel rod 10 m long is heated by 50 °C. The linear expansivity of steel is 1.2 × 10⁻⁵ /K. By how much does it expand?', ['0.006 m', '0.06 m', '0.0006 m', '6 m'], 'A', 'ΔL = αLΔθ = 1.2 × 10⁻⁵ × 10 × 50 = 0.006 m (6 mm).', 3],
        ['Water has its greatest density at', ['0 °C', '4 °C', '100 °C', '−4 °C'], 'B', 'Water contracts from 0 °C to 4 °C, then expands. This is why ice forms on top of ponds.', 2],
    ],

    'SHS 3: Electric circuits and Kirchhoff\'s laws' => [
        ['Kirchhoff\'s first (current) law states that at a junction', ['the total current entering equals the total current leaving', 'the voltages add up to zero', 'the resistance is zero', 'current is used up'], 'A', 'Charge is conserved, so current is not lost at a junction.', 2],
        ['A cell of e.m.f. 12 V and internal resistance 1 Ω is connected to a 5 Ω resistor. What current flows?', ['2 A', '2.4 A', '12 A', '0.5 A'], 'A', 'I = E ÷ (R + r) = 12 ÷ 6 = 2 A.', 3],
        ['For the cell in a 12 V, 1 Ω and 5 Ω circuit carrying 2 A, what is the terminal voltage?', ['12 V', '10 V', '2 V', '14 V'], 'B', 'V = E − Ir = 12 − 2 × 1 = 10 V (or IR = 2 × 5 = 10 V).', 3],
        ['Kirchhoff\'s second (voltage) law is based on the conservation of', ['energy', 'charge', 'mass', 'momentum'], 'A', 'Around any closed loop, the e.m.f.s equal the sum of the potential drops.', 3],
    ],

    'SHS 3: X-rays and nuclear energy' => [
        ['X-rays are produced when', ['fast-moving electrons hit a metal target', 'light passes through glass', 'a magnet is heated', 'sound waves travel in air'], 'A', 'In an X-ray tube, electrons are accelerated by a high voltage and stopped by a tungsten target.', 2],
        ['X-rays are used in hospitals mainly to', ['take images of bones', 'cool patients', 'measure blood pressure', 'sterilise water'], 'A', 'X-rays pass through soft tissue but are absorbed by bone.', 1],
        ['Splitting a heavy nucleus, such as uranium-235, into smaller nuclei is', ['nuclear fission', 'nuclear fusion', 'radioactive decay only', 'ionisation'], 'A', 'Fission releases large amounts of energy and is used in nuclear power stations.', 2],
        ['Einstein\'s equation E = mc² shows that', ['mass can be converted into energy', 'energy has no mass', 'light has mass', 'speed equals mass'], 'A', 'The small mass lost in nuclear reactions appears as a large amount of energy.', 2],
    ],

    'SHS 3: Astronomy and cosmology' => [
        ['A light-year is a unit of', ['time', 'distance', 'speed', 'brightness'], 'B', 'It is the distance light travels in one year, about 9.46 × 10¹⁵ m.', 2],
        ['The Sun is', ['a planet', 'a star', 'a moon', 'a comet'], 'B', 'The Sun is a medium-sized star at the centre of our solar system.', 1],
        ['Which planet is closest to the Sun?', ['Venus', 'Earth', 'Mercury', 'Mars'], 'C', 'The order outwards begins Mercury, Venus, Earth, Mars.', 1],
        ['The theory that the universe began from an extremely hot, dense state and has been expanding since is the', ['Big Bang theory', 'steady state theory only', 'theory of relativity', 'kinetic theory'], 'A', 'Evidence includes the red shift of galaxies and the cosmic microwave background.', 2],
    ],

    'SHS 3: Alternating current' => [
        ['An alternating voltage has a peak value of 340 V. What is its r.m.s. value?', ['170 V', '240 V', '480 V', '340 V'], 'B', 'V(rms) = V₀ ÷ √2 = 340 ÷ 1.414 ≈ 240 V.', 3],
        ['What is the frequency of the mains supply in Ghana?', ['50 Hz', '60 Hz', '100 Hz', '240 Hz'], 'A', 'Ghana\'s mains supply is about 230–240 V at 50 Hz.', 1],
        ['Transformers work only with alternating current because', ['a changing magnetic field is needed to induce a voltage', 'direct current is too large', 'AC has no magnetic effect', 'DC melts the coils'], 'A', 'Steady DC gives a constant field, which induces nothing in the secondary coil.', 3],
        ['The reactance of an inductor', ['increases as the frequency increases', 'decreases as the frequency increases', 'does not depend on frequency', 'is always zero'], 'A', 'X(L) = 2πfL.', 3],
    ],

    'SHS 3: Lenses and lasers' => [
        ['An object is 30 cm from a converging lens of focal length 10 cm. How far from the lens is the image?', ['15 cm', '20 cm', '7.5 cm', '40 cm'], 'A', '1/v = 1/f − 1/u = 1/10 − 1/30 = 2/30, so v = 15 cm (real-is-positive convention).', 3],
        ['What is the power of a converging lens of focal length 25 cm?', ['0.25 D', '4 D', '25 D', '2.5 D'], 'B', 'P = 1 ÷ f (in metres) = 1 ÷ 0.25 = +4 dioptres.', 2],
        ['A magnifying glass is a', ['converging (convex) lens with the object inside its focal length', 'diverging lens', 'plane mirror', 'prism'], 'A', 'It forms a magnified, upright, virtual image.', 2],
        ['Laser light differs from ordinary light because it is', ['monochromatic and coherent', 'made of many colours', 'always invisible', 'unable to travel in air'], 'A', 'Laser light has one wavelength and its waves are in step.', 2],
    ],

    'SHS 3: Electromagnetic induction' => [
        ['Faraday\'s law states that the induced e.m.f. is proportional to', ['the rate of change of magnetic flux linkage', 'the resistance of the coil', 'the temperature', 'the mass of the magnet'], 'A', 'Moving a magnet faster gives a larger induced e.m.f.', 3],
        ['Lenz\'s law states that the induced current flows in a direction that', ['opposes the change producing it', 'helps the change producing it', 'is always clockwise', 'is random'], 'A', 'This follows from the conservation of energy.', 3],
        ['An a.c. generator changes', ['mechanical energy into electrical energy', 'electrical energy into mechanical energy', 'heat into light', 'chemical energy into electrical energy'], 'A', 'A coil rotating in a magnetic field has a voltage induced in it.', 1],
        ['Eddy currents in a transformer core are reduced by', ['making the core from thin insulated laminations', 'using a solid copper core', 'increasing the current', 'removing the core'], 'A', 'Laminations break up the current paths and cut energy losses.', 3],
    ],

    'SHS 3: Simple harmonic motion and collisions' => [
        ['The period of a simple pendulum depends on', ['its length and g', 'the mass of the bob', 'the size of the bob only', 'the colour of the string'], 'A', 'T = 2π√(l/g), which does not depend on mass.', 2],
        ['A 2 kg trolley moving at 3 m/s hits a 1 kg trolley at rest and they stick together. What is their common speed?', ['2 m/s', '3 m/s', '1.5 m/s', '6 m/s'], 'A', 'Momentum before = 2 × 3 = 6 kg m/s. After: 3 kg × v = 6, so v = 2 m/s.', 3],
        ['In an elastic collision,', ['both momentum and kinetic energy are conserved', 'only kinetic energy is conserved', 'neither is conserved', 'the bodies stick together'], 'A', 'In inelastic collisions momentum is conserved but some kinetic energy is lost.', 2],
        ['In simple harmonic motion, the acceleration is', ['proportional to the displacement and directed towards the centre', 'constant', 'zero at the ends', 'greatest at the centre'], 'A', 'a = −ω²x: largest at the extremes, zero at the centre.', 3],
    ],

    'SHS 3: Rectification and power supplies' => [
        ['A half-wave rectifier uses', ['one diode', 'four diodes', 'a transistor only', 'a transformer only'], 'A', 'The diode passes only one half of each a.c. cycle.', 2],
        ['A bridge rectifier uses', ['two diodes', 'four diodes', 'one diode', 'six capacitors'], 'B', 'Four diodes let both halves of the a.c. cycle through in the same direction.', 2],
        ['In a power supply, a capacitor placed across the output of a rectifier', ['smooths the output voltage', 'increases the ripple', 'changes DC to AC', 'blocks all current'], 'A', 'It charges at the peaks and discharges between them, filling in the dips.', 2],
    ],
];
