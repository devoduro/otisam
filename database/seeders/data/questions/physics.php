<?php

/*
| SHS Physics: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
| Take g = 10 m/s² unless stated.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Measurement and motion
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

    // Forces, work, energy, machines
    ['A force of 50 N acts on an area of 2 m². Find the pressure.', ['25 Pa', '48 Pa', '52 Pa', '100 Pa'], 'A', 'P = F/A = 50 ÷ 2 = 25 Pa.', 1],
    ['A machine has a velocity ratio of 5 and an efficiency of 80%. Find its mechanical advantage.', ['4', '5', '6.25', '400'], 'A', 'Efficiency = MA/VR, so MA = 0.8 × 5 = 4.', 3],
    ['A spring stretches by 4 cm under a load of 8 N. What load will stretch it by 6 cm (within the elastic limit)?', ['10 N', '12 N', '14 N', '16 N'], 'B', 'By Hooke\'s law, k = 8/4 = 2 N/cm, so F = 2 × 6 = 12 N.', 3],
    ['Which of the following is NOT a unit of energy?', ['joule', 'kilowatt-hour', 'electron-volt', 'watt'], 'D', 'The watt is a unit of power (J/s), not energy.', 2],
    ['Pressure in a liquid increases with', ['depth', 'surface area only', 'the shape of the container', 'the volume of the container'], 'A', 'p = hρg: pressure increases with depth and density.', 2],
    ['A body floats in water because', ['its weight is greater than the upthrust', 'the upthrust equals its weight', 'it has no weight', 'water has no density'], 'B', 'By the law of flotation, a floating body displaces its own weight of fluid.', 2],

    // Heat
    ['The temperature 27 °C on the Kelvin scale is', ['246 K', '273 K', '300 K', '327 K'], 'C', 'T(K) = θ(°C) + 273 = 27 + 273 = 300 K.', 1],
    ['The amount of heat needed to raise the temperature of 1 kg of a substance by 1 K is its', ['heat capacity', 'specific heat capacity', 'latent heat', 'thermal conductivity'], 'B', 'Specific heat capacity is in J kg⁻¹ K⁻¹.', 2],
    ['How much heat is needed to raise 2 kg of water from 20 °C to 70 °C? (c = 4200 J kg⁻¹ K⁻¹)', ['42,000 J', '210,000 J', '420,000 J', '588,000 J'], 'C', 'Q = mcΔθ = 2 × 4200 × 50 = 420,000 J.', 3],
    ['The heat absorbed when a solid changes to a liquid without a change in temperature is', ['latent heat of fusion', 'latent heat of vaporisation', 'specific heat', 'heat capacity'], 'A', 'Latent heat of fusion breaks the bonds of the solid at its melting point.', 2],
    ['The transfer of heat in liquids and gases by the movement of the fluid is', ['conduction', 'evaporation', 'radiation', 'convection'], 'D', 'Warm fluid rises and cool fluid sinks, creating convection currents.', 1],

    // Waves, light, sound
    ['A wave has a frequency of 50 Hz and a wavelength of 4 m. Find its speed.', ['12.5 m/s', '54 m/s', '200 m/s', '0.08 m/s'], 'C', 'v = fλ = 50 × 4 = 200 m/s.', 2],
    ['Which of the following is a longitudinal wave?', ['light', 'radio waves', 'sound', 'X-rays'], 'C', 'In sound waves the particles vibrate along the direction of travel.', 2],
    ['Which electromagnetic wave has the longest wavelength?', ['gamma rays', 'X-rays', 'visible light', 'radio waves'], 'D', 'Radio waves have the longest wavelength and lowest frequency.', 2],
    ['The refractive index of a medium is 1.5. If the speed of light in air is 3 × 10⁸ m/s, find the speed of light in the medium.', ['1.5 × 10⁸ m/s', '3 × 10⁸ m/s', '4.5 × 10⁸ m/s', '2 × 10⁸ m/s'], 'D', 'n = c/v, so v = 3 × 10⁸ ÷ 1.5 = 2 × 10⁸ m/s.', 3],
    ['Total internal reflection can occur when light travels from', ['glass into air at an angle greater than the critical angle', 'air into glass', 'air into water', 'a vacuum into air'], 'A', 'It needs light going from a denser to a less dense medium at more than the critical angle.', 3],
    ['A converging lens has a focal length of 10 cm. An object is placed 30 cm from it. Find the image distance.', ['7.5 cm', '30 cm', '20 cm', '15 cm'], 'D', '1/v = 1/f − 1/u = 1/10 − 1/30 = 2/30, so v = 15 cm (real image).', 4],
    ['Echoes are produced by the', ['reflection of sound', 'refraction of sound', 'diffraction of sound', 'absorption of sound'], 'A', 'An echo is sound reflected from a hard surface.', 1],

    // Electricity and magnetism
    ['A current of 2 A flows for 30 s. How much charge passes?', ['15 C', '28 C', '32 C', '60 C'], 'D', 'Q = It = 2 × 30 = 60 C.', 2],
    ['An electric iron rated 1000 W is used for 2 hours. How much energy does it use in kWh?', ['2 kWh', '0.5 kWh', '20 kWh', '2,000 kWh'], 'A', 'E = P × t = 1 kW × 2 h = 2 kWh.', 2],
    ['A transformer has 100 turns on its primary and 500 turns on its secondary. If the primary voltage is 240 V, find the secondary voltage.', ['48 V', '240 V', '1,200 V', '12,000 V'], 'C', 'Vs/Vp = Ns/Np, so Vs = 240 × 500/100 = 1,200 V (a step-up transformer).', 3],
    ['The device used to protect an electrical circuit from excess current is a', ['fuse', 'transformer', 'rheostat', 'capacitor'], 'A', 'A fuse wire melts and breaks the circuit when the current is too large.', 1],
    ['Which of the following materials is ferromagnetic?', ['copper', 'brass', 'aluminium', 'iron'], 'D', 'Iron, nickel and cobalt are strongly magnetic.', 1],

    // Modern physics
    ['An alpha particle is the same as the nucleus of a', ['hydrogen atom', 'uranium atom', 'carbon atom', 'helium atom'], 'D', 'An alpha particle has 2 protons and 2 neutrons, like a helium nucleus.', 2],
    ['The half-life of a radioactive sample is 5 days. What fraction of it remains after 15 days?', ['1/2', '1/3', '1/4', '1/8'], 'D', '15 days is 3 half-lives: (½)³ = 1/8.', 3],
    ['The emission of electrons from a metal surface when light falls on it is the', ['photoelectric effect', 'thermionic emission', 'Compton effect', 'radioactivity'], 'A', 'In the photoelectric effect, light of high enough frequency releases electrons.', 2],
];
