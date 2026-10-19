<?php

/*
| SHS Geography: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Earth and solar system
    ['The earth rotates on its axis from', ['east to west', 'west to east', 'north to south', 'south to north'], 'B', 'The earth rotates west to east, so the sun appears to rise in the east.', 1],
    ['One complete rotation of the earth takes about', ['24 hours', '365 days', '30 days', '12 hours'], 'A', 'A rotation takes about 24 hours, giving day and night.', 1],
    ['The revolution of the earth around the sun causes', ['day and night', 'the seasons', 'tides only', 'earthquakes'], 'B', 'Revolution, together with the tilt of the earth\'s axis, causes the seasons.', 2],
    ['The time at a place 45° east of Greenwich when it is 12 noon at Greenwich is', ['9:00 a.m.', '3:00 p.m.', '12 midnight', '6:00 p.m.'], 'B', '15° = 1 hour, so 45° = 3 hours ahead: 3:00 p.m.', 3],
    ['Lines of latitude are also called', ['meridians', 'parallels', 'contours', 'isobars'], 'B', 'Lines of latitude run parallel to the equator.', 1],
    ['The Tropic of Cancer lies at latitude', ['0°', '23½° N', '23½° S', '66½° N'], 'B', 'The Tropic of Cancer is at about 23½° N.', 2],

    // Rocks and landforms
    ['Rocks formed from layers of sediments that have been compressed are', ['igneous rocks', 'sedimentary rocks', 'metamorphic rocks', 'volcanic rocks'], 'B', 'Sedimentary rocks such as sandstone form from compacted sediments.', 1],
    ['Marble is a metamorphic rock formed from', ['granite', 'limestone', 'basalt', 'sandstone'], 'B', 'Heat and pressure change limestone into marble.', 2],
    ['A landform formed where a river deposits sediment at its mouth in a lake or sea is a', ['delta', 'gorge', 'waterfall', 'meander'], 'A', 'Deltas form where sediment builds up at the river mouth, as at the mouth of the Volta.', 2],
    ['A steep-sided valley formed by river erosion in its upper course is a', ['flood plain', 'V-shaped valley', 'delta', 'ox-bow lake'], 'B', 'Vertical erosion in the upper course produces V-shaped valleys.', 2],
    ['An ox-bow lake is formed in the', ['upper course of a river', 'middle and lower course of a river', 'source of a river', 'mouth of a river only'], 'B', 'A meander neck is cut through and the old loop is left as a lake.', 3],
    ['The breaking down of rocks in place by heat, frost, chemicals or organisms is called', ['erosion', 'deposition', 'weathering', 'transportation'], 'C', 'Weathering breaks rocks down where they are; erosion moves the material away.', 2],
    ['A sand bar that joins an island to the mainland is a', ['spit', 'stack', 'lagoon', 'tombolo'], 'D', 'A tombolo links an island to the coast.', 4],

    // Climate and vegetation
    ['The instrument used to measure atmospheric pressure is the', ['thermometer', 'hygrometer', 'barometer', 'anemometer'], 'C', 'A barometer measures air pressure.', 1],
    ['The instrument used to measure wind speed is the', ['wind vane', 'barometer', 'rain gauge', 'anemometer'], 'D', 'An anemometer measures wind speed; a wind vane shows direction.', 1],
    ['Rainfall caused by moist air rising over a mountain is called', ['relief (orographic) rainfall', 'convectional rainfall', 'cyclonic rainfall', 'frontal rainfall'], 'A', 'Air forced up a mountain cools and condenses on the windward side.', 2],
    ['The side of a mountain that receives little rainfall is the', ['windward side', 'summit', 'leeward (rain shadow) side', 'foot'], 'C', 'Descending air on the leeward side is warm and dry.', 2],
    ['The equatorial climate is characterised by', ['low temperatures and little rain', 'very little rain', 'cold winters and hot summers', 'high temperatures and rain throughout the year'], 'D', 'Near the equator temperatures stay high and rain falls all year.', 1],
    ['Mangrove vegetation is found mainly along', ['coastal lagoons and river estuaries', 'mountain tops', 'deserts', 'savanna grasslands'], 'A', 'Mangroves grow in salty, swampy coastal areas.', 2],
    ['The relative humidity of air is measured with a', ['hygrometer', 'barometer', 'thermometer', 'anemometer'], 'A', 'A hygrometer (wet and dry bulb) measures relative humidity.', 2],

    // Map work
    ['The representative fraction of a map with a scale of 1 cm to 1 km is', ['1 : 1,000', '1 : 10,000', '1 : 100,000', '1 : 1,000,000'], 'C', '1 km = 100,000 cm, so the RF is 1 : 100,000.', 3],
    ['On a topographical map, contour lines that are very close together show', ['a gentle slope', 'a river', 'flat land', 'a steep slope'], 'D', 'Height changes quickly over a short distance on a steep slope.', 2],
    ['A six-figure grid reference gives', ['the height of a place', 'the scale of a map', 'the position of a point on a map', 'the direction of north'], 'C', 'Eastings and northings locate a point precisely.', 2],

    // Human and economic geography (Ghana and West Africa)
    ['The main type of farming practised by most farmers in Ghana is', ['plantation farming', 'mechanised farming only', 'ranching', 'subsistence farming'], 'D', 'Most farmers grow food on small plots mainly for their families.', 1],
    ['Shifting cultivation involves', ['moving the farm to a new plot when the soil loses fertility', 'farming the same plot forever', 'farming only cash crops', 'using only tractors'], 'A', 'Farmers abandon exhausted land and clear a new plot.', 1],
    ['Bauxite is mined in Ghana at', ['Tarkwa', 'Obuasi', 'Awaso', 'Akwatia'], 'C', 'Bauxite is mined at Awaso in the Western North Region (and deposits exist at Nyinahin).', 3],
    ['Diamonds are mined in Ghana mainly at', ['Akwatia', 'Nsuta', 'Tarkwa', 'Obuasi'], 'A', 'Akwatia in the Eastern Region is Ghana\'s main diamond area.', 2],
    ['Manganese is mined in Ghana at', ['Nsuta', 'Akwatia', 'Awaso', 'Bonwire'], 'A', 'Nsuta near Tarkwa is a major manganese mine.', 2],
    ['Ghana\'s main seaports are', ['Tema and Takoradi', 'Accra and Kumasi', 'Tamale and Bolgatanga', 'Ho and Koforidua'], 'A', 'Tema and Takoradi are the two main harbours.', 1],
    ['Oil was discovered in commercial quantities in Ghana in 2007 in the', ['Akwapim hills', 'Volta basin', 'Keta basin only', 'Jubilee field'], 'D', 'The offshore Jubilee field began production in 2010.', 2],
    ['Which of the following is a pull factor of rural-urban migration?', ['lack of social amenities in villages', 'poor farming conditions in villages', 'better job opportunities in towns', 'land disputes in villages'], 'C', 'Pull factors attract people to towns; push factors drive them from villages.', 2],
    ['Population density is calculated as', ['population × land area', 'land area ÷ population', 'births − deaths', 'population ÷ land area'], 'D', 'Density = number of people per unit of land area.', 1],
    ['The river on which the Bui Dam is built is the', ['Pra', 'White Volta', 'Black Volta', 'Tano'], 'C', 'The Bui hydroelectric dam is on the Black Volta.', 2],
    ['The Kainji Dam is found in', ['Ghana', 'Togo', 'Côte d\'Ivoire', 'Nigeria'], 'D', 'The Kainji Dam is on the River Niger in Nigeria.', 2],
];
