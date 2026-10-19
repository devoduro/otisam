<?php

/*
| SHS Geography: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Earth and solar system' => [
        ['The earth rotates on its axis from', ['east to west', 'west to east', 'north to south', 'south to north'], 'B', 'The earth rotates west to east, so the sun appears to rise in the east.', 1],
        ['One complete rotation of the earth takes about', ['24 hours', '365 days', '30 days', '12 hours'], 'A', 'A rotation takes about 24 hours, giving day and night.', 1],
        ['The revolution of the earth around the sun causes', ['day and night', 'the seasons', 'tides only', 'earthquakes'], 'B', 'Revolution, together with the tilt of the earth\'s axis, causes the seasons.', 2],
        ['The time at a place 45° east of Greenwich when it is 12 noon at Greenwich is', ['9:00 a.m.', '3:00 p.m.', '12 midnight', '6:00 p.m.'], 'B', '15° = 1 hour, so 45° = 3 hours ahead: 3:00 p.m.', 3],
        ['Lines of latitude are also called', ['meridians', 'parallels', 'contours', 'isobars'], 'B', 'Lines of latitude run parallel to the equator.', 1],
        ['The Tropic of Cancer lies at latitude', ['0°', '23½° N', '23½° S', '66½° N'], 'B', 'The Tropic of Cancer is at about 23½° N.', 2],
    ],

    'Rocks and landforms' => [
        ['Rocks formed from layers of sediments that have been compressed are', ['igneous rocks', 'sedimentary rocks', 'metamorphic rocks', 'volcanic rocks'], 'B', 'Sedimentary rocks such as sandstone form from compacted sediments.', 1],
        ['Marble is a metamorphic rock formed from', ['granite', 'limestone', 'basalt', 'sandstone'], 'B', 'Heat and pressure change limestone into marble.', 2],
        ['A landform formed where a river deposits sediment at its mouth in a lake or sea is a', ['delta', 'gorge', 'waterfall', 'meander'], 'A', 'Deltas form where sediment builds up at the river mouth, as at the mouth of the Volta.', 2],
        ['A steep-sided valley formed by river erosion in its upper course is a', ['flood plain', 'V-shaped valley', 'delta', 'ox-bow lake'], 'B', 'Vertical erosion in the upper course produces V-shaped valleys.', 2],
        ['An ox-bow lake is formed in the', ['upper course of a river', 'middle and lower course of a river', 'source of a river', 'mouth of a river only'], 'B', 'A meander neck is cut through and the old loop is left as a lake.', 3],
        ['The breaking down of rocks in place by heat, frost, chemicals or organisms is called', ['erosion', 'deposition', 'weathering', 'transportation'], 'C', 'Weathering breaks rocks down where they are; erosion moves the material away.', 2],
        ['A sand bar that joins an island to the mainland is a', ['spit', 'stack', 'lagoon', 'tombolo'], 'D', 'A tombolo links an island to the coast.', 4],
    ],

    'Climate and vegetation' => [
        ['The instrument used to measure atmospheric pressure is the', ['thermometer', 'hygrometer', 'barometer', 'anemometer'], 'C', 'A barometer measures air pressure.', 1],
        ['The instrument used to measure wind speed is the', ['wind vane', 'barometer', 'rain gauge', 'anemometer'], 'D', 'An anemometer measures wind speed; a wind vane shows direction.', 1],
        ['Rainfall caused by moist air rising over a mountain is called', ['relief (orographic) rainfall', 'convectional rainfall', 'cyclonic rainfall', 'frontal rainfall'], 'A', 'Air forced up a mountain cools and condenses on the windward side.', 2],
        ['The side of a mountain that receives little rainfall is the', ['windward side', 'summit', 'leeward (rain shadow) side', 'foot'], 'C', 'Descending air on the leeward side is warm and dry.', 2],
        ['The equatorial climate is characterised by', ['low temperatures and little rain', 'very little rain', 'cold winters and hot summers', 'high temperatures and rain throughout the year'], 'D', 'Near the equator temperatures stay high and rain falls all year.', 1],
        ['Mangrove vegetation is found mainly along', ['coastal lagoons and river estuaries', 'mountain tops', 'deserts', 'savanna grasslands'], 'A', 'Mangroves grow in salty, swampy coastal areas.', 2],
        ['The relative humidity of air is measured with a', ['hygrometer', 'barometer', 'thermometer', 'anemometer'], 'A', 'A hygrometer (wet and dry bulb) measures relative humidity.', 2],
    ],

    'Map work' => [
        ['The representative fraction of a map with a scale of 1 cm to 1 km is', ['1 : 1,000', '1 : 10,000', '1 : 100,000', '1 : 1,000,000'], 'C', '1 km = 100,000 cm, so the RF is 1 : 100,000.', 3],
        ['On a topographical map, contour lines that are very close together show', ['a gentle slope', 'a river', 'flat land', 'a steep slope'], 'D', 'Height changes quickly over a short distance on a steep slope.', 2],
        ['A six-figure grid reference gives', ['the height of a place', 'the scale of a map', 'the position of a point on a map', 'the direction of north'], 'C', 'Eastings and northings locate a point precisely.', 2],
    ],

    'Human and economic geography (Ghana and West Africa)' => [
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
    ],

    'SHS 1: The solar system and the earth\'s motions' => [
        ['Which is the largest planet in the solar system?', ['Saturn', 'Jupiter', 'Earth', 'Neptune'], 'B', 'Jupiter is a gas giant more than 11 times the Earth\'s diameter.', 1],
        ['The belt of asteroids in the solar system lies between', ['Venus and Earth', 'Earth and Mars', 'Mars and Jupiter', 'Saturn and Uranus'], 'C', 'It holds thousands of rocky bodies orbiting the Sun.', 2],
        ['Day and night are caused by the', ['movement of the sun around the earth', 'revolution of the earth around the sun', 'tilt of the moon', 'rotation of the earth on its axis'], 'D', 'One rotation takes about 24 hours.', 1],
        ['The seasons are caused mainly by the earth\'s revolution around the sun together with', ['the moon\'s phases', 'the tilt of its axis (about 23½°)', 'ocean currents', 'volcanic activity'], 'B', 'The tilt changes how directly the sun\'s rays strike each hemisphere.', 2],
    ],

    'SHS 1: Latitude, longitude and time' => [
        ['How many degrees of longitude does the earth turn through in one hour?', ['1°', '24°', '15°', '360°'], 'C', '360° ÷ 24 hours = 15° per hour.', 1],
        ['When it is 12 noon at Greenwich (0°), what is the local time at 45°E?', ['4:30 p.m.', '9:00 a.m.', '12:45 p.m.', '3:00 p.m.'], 'D', '45° ÷ 15° = 3 hours, and places east are ahead: 3 p.m.', 2],
        ['Which Ghanaian town lies on the Greenwich Meridian?', ['Kumasi', 'Tema', 'Tamale', 'Takoradi'], 'B', 'The 0° line of longitude passes through Tema.', 1],
    ],

    'SHS 1: Map reading and relief' => [
        ['On a map with a scale of 1 : 50,000, what real distance does 4 cm represent?', ['0.2 km', '20 km', '200 m', '2 km'], 'D', '4 × 50,000 = 200,000 cm = 2 km.', 2],
        ['Contour lines join places of equal', ['temperature', 'height', 'rainfall', 'population'], 'B', 'Isotherms join equal temperatures and isohyets equal rainfall.', 1],
        ['Contour lines that are very close together show', ['flat land', 'a gentle slope', 'a steep slope', 'a river'], 'C', 'Height changes quickly over a short distance.', 1],
        ['When giving a grid reference, you read first the', ['scale', 'northings (up the side)', 'map title', 'eastings (along the bottom)'], 'D', 'Remember "along the corridor, then up the stairs".', 2],
    ],

    'SHS 1: Ghana\'s physical environment' => [
        ['What is the highest point in Ghana?', ['Kwahu Plateau', 'Mount Afadjato', 'Atewa Hills', 'Gambaga Escarpment'], 'B', 'Mount Afadjato in the Volta Region is about 885 m high.', 1],
        ['Lake Bosumtwi in the Ashanti Region is believed to have formed from', ['a lagoon', 'a dam on the Volta', 'a meteorite impact crater', 'a glacier'], 'C', 'It is Ghana\'s only natural lake.', 2],
        ['Which lake was formed when the Akosombo Dam was built?', ['Weija', 'Bosumtwi', 'Bui', 'Volta'], 'D', 'Lake Volta, behind the Akosombo Dam, is the largest man-made lake in Ghana and one of the largest in the world.', 1],
        ['The Akwapim–Togo Ranges run in a direction of roughly', ['east to west', 'south-west to north-east', 'north to south only', 'north-west to south-east'], 'B', 'They extend from near Accra towards Togo.', 3],
    ],

    'SHS 1: Climate, vegetation and soils of Ghana' => [
        ['The harmattan in Ghana is a', ['local sea breeze', 'wet wind from the Atlantic', 'dry, dusty wind from the Sahara', 'tropical storm'], 'C', 'The north-east trade winds bring it, usually from about December to February.', 1],
        ['Which part of Ghana receives the most rainfall?', ['the Afram plains', 'the far north', 'the Accra plains', 'the south-west (around Axim)'], 'D', 'The Accra plains are the driest part of southern Ghana.', 2],
        ['Tropical rainforest in Ghana is found mainly in the', ['north-east', 'south-west', 'Upper West Region', 'coastal savannah around Accra'], 'B', 'High rainfall supports tall evergreen and semi-deciduous forest.', 1],
        ['Most of northern Ghana is covered by', ['mangrove swamp', 'tropical rainforest', 'Guinea savannah', 'desert'], 'C', 'Grassland with scattered trees suits a single rainy season.', 1],
    ],

    'SHS 1: Rocks and weathering' => [
        ['Granite is an example of', ['organic rock', 'sedimentary rock', 'metamorphic rock', 'igneous rock'], 'D', 'It forms from magma cooling slowly underground.', 1],
        ['The peeling off of outer rock layers due to daily heating and cooling is', ['oxidation', 'carbonation', 'exfoliation', 'hydration'], 'C', 'It is a type of mechanical weathering common in hot, dry areas.', 2],
        ['Rainwater containing carbon dioxide dissolves limestone by', ['abrasion', 'exfoliation', 'frost action', 'carbonation'], 'D', 'It is a form of chemical weathering.', 2],
    ],

    'SHS 1: The atmosphere' => [
        ['The ozone layer, which absorbs harmful ultraviolet rays, is in the', ['troposphere', 'stratosphere', 'mesosphere', 'exosphere'], 'B', 'Most weather happens in the troposphere below it.', 2],
        ['In the troposphere, temperature generally', ['stays the same', 'rises with height', 'falls with height', 'reaches 1,000 °C'], 'C', 'It falls by about 6.5 °C for every 1,000 m.', 2],
        ['The gas that makes up most of the atmosphere is', ['argon', 'oxygen', 'carbon dioxide', 'nitrogen'], 'D', 'About 78% nitrogen and 21% oxygen.', 1],
    ],

    'SHS 1: Collecting geospatial data' => [
        ['Collecting information about the earth from satellites or aircraft is', ['oral history', 'remote sensing', 'a census', 'interviewing'], 'B', 'It is used to map land use, forests and floods.', 2],
        ['A computer system for storing, analysing and displaying data about places is a', ['spreadsheet only', 'word processor', 'Geographic Information System (GIS)', 'compass'], 'C', 'GIS layers different kinds of map data together.', 1],
        ['GPS is used in fieldwork to', ['count population', 'measure rainfall', 'test soil pH', 'find the exact location of a point'], 'D', 'It uses signals from satellites.', 1],
    ],

    'SHS 1: Mining, pollution and hazards in Ghana' => [
        ['Manganese is mined in Ghana mainly at', ['Akwatia', 'Nsuta', 'Obuasi', 'Awaso'], 'B', 'Akwatia is known for diamonds, Obuasi for gold and Awaso for bauxite.', 2],
        ['Diamonds are mined mainly at', ['Tarkwa', 'Nsuta', 'Akwatia', 'Kibi'], 'C', 'Akwatia is in the Eastern Region.', 2],
        ['The national body that manages disasters such as floods in Ghana is', ['NHIA', 'NCCE', 'GRA', 'NADMO'], 'D', 'The National Disaster Management Organisation coordinates relief and prevention.', 1],
        ['A major cause of flooding in Accra is', ['too many trees', 'blocked drains and building on waterways', 'low rainfall', 'sandy soils only'], 'B', 'Waste in gutters and buildings in floodplains block the flow of water.', 1],
    ],

    'SHS 2: Earth movements and landforms' => [
        ['A rift valley is formed mainly by', ['river erosion only', 'folding', 'faulting', 'wind deposition'], 'C', 'Land sinks between two roughly parallel faults.', 2],
        ['The strength of an earthquake is often measured on the', ['Mohs scale', 'Beaufort scale', 'Celsius scale', 'Richter scale'], 'D', 'The Beaufort scale is for wind; the Mohs scale is for mineral hardness.', 1],
        ['Mountains formed when layers of rock are compressed and bent are', ['block mountains', 'fold mountains', 'volcanic mountains', 'residual mountains'], 'B', 'The Atlas Mountains of North Africa are fold mountains.', 2],
        ['Molten rock that reaches the earth\'s surface is called', ['ash', 'magma', 'lava', 'granite'], 'C', 'Below the surface it is called magma.', 1],
    ],

    'SHS 2: Map skills and analysis' => [
        ['The land rises 100 m over a horizontal distance of 2 km. What is the average gradient?', ['1 in 2,000', '1 in 200', '1 in 2', '1 in 20'], 'D', 'Gradient = vertical interval ÷ horizontal equivalent = 100 m ÷ 2,000 m = 1/20.', 3],
        ['A scale written as 1 : 25,000 is called a', ['statement scale', 'representative fraction', 'linear scale', 'grid'], 'B', 'It shows that 1 unit on the map stands for 25,000 of the same units on the ground.', 2],
        ['The direction of a place measured clockwise from north in degrees is its', ['scale', 'contour', 'bearing', 'latitude'], 'C', 'Bearings are written with three figures, such as 045°.', 1],
    ],

    'SHS 2: The physical setting of West Africa' => [
        ['The River Niger rises in the', ['Jos Plateau', 'Atlas Mountains', 'Ethiopian highlands', 'Fouta Djallon highlands'], 'D', 'It flows north-east and then south to the Gulf of Guinea.', 2],
        ['The semi-arid belt along the southern edge of the Sahara is the', ['rainforest', 'Sahel', 'Mediterranean zone', 'Rift Valley'], 'B', 'It suffers droughts and desert encroachment.', 1],
        ['The shallow lake shared by Chad, Niger, Nigeria and Cameroon is Lake', ['Kainji', 'Volta', 'Chad', 'Victoria'], 'C', 'It has shrunk greatly since the 1960s.', 1],
    ],

    'SHS 2: Manufacturing industries in Ghana' => [
        ['Which factor most attracts a factory to a site near its raw materials?', ['many schools', 'a beautiful view', 'cheap cars', 'bulky raw materials that are costly to transport'], 'D', 'Cement and sugar factories, for example, locate near their raw materials.', 2],
        ['Tema became a major industrial centre mainly because of its', ['gold mines', 'harbour, power supply and planned industrial area', 'forest reserves', 'mountains'], 'B', 'The harbour and the Akosombo power supply attracted many industries.', 1],
        ['The VALCO plant at Tema produces', ['cement', 'cocoa butter', 'aluminium', 'textiles'], 'C', 'Smelting aluminium needs a great deal of electricity.', 2],
    ],

    'SHS 2: Soil formation' => [
        ['Which is a factor of soil formation?', ['markets', 'population', 'roads', 'parent rock'], 'D', 'The others are climate, living organisms, relief and time.', 1],
        ['The top layer of a soil profile, rich in humus, is the', ['B horizon (subsoil)', 'A horizon (topsoil)', 'C horizon', 'bedrock'], 'B', 'Most plant roots and soil organisms live in it.', 2],
        ['Humus is formed from', ['sand', 'weathered rock only', 'decayed plant and animal matter', 'salt'], 'C', 'Humus holds water and nutrients.', 1],
    ],

    'SHS 2: World climates and vegetation' => [
        ['A climate with hot, dry summers and mild, wet winters is the', ['monsoon climate', 'equatorial climate', 'tundra climate', 'Mediterranean climate'], 'D', 'It is found around the Mediterranean Sea and at the southern tip of Africa.', 2],
        ['The equatorial climate is', ['hot and wet all year', 'cold all year', 'dry all year', 'hot with snow'], 'A', 'Temperatures stay around 27 °C, with rain in every month.', 1],
        ['Coniferous forest of the cold northern lands is called', ['taiga', 'savannah', 'steppe', 'mangrove'], 'A', 'Conifers have needle leaves that resist snow and frost.', 2],
    ],

    'SHS 2: Land degradation and environmental hazards' => [
        ['Which is a major cause of land degradation in Ghana?', ['illegal mining and deforestation', 'tree planting', 'contour farming', 'mulching'], 'A', 'Overgrazing and bush burning also degrade the land.', 1],
        ['Bush fires in northern Ghana are most common during the', ['dry season (harmattan)', 'main rainy season', 'flood season', 'cold season'], 'A', 'Dry grass burns easily.', 1],
        ['Planting trees on degraded mining sites is an example of', ['land reclamation', 'land degradation', 'quarrying', 'mining'], 'A', 'Reclamation restores land for farming or forest.', 1],
    ],

    'SHS 3: Rivers and mass wasting' => [
        ['V-shaped valleys and waterfalls are typical of a river\'s', ['upper course', 'lower course', 'mouth', 'delta'], 'A', 'Fast water erodes downwards in the upper course.', 2],
        ['A horseshoe-shaped lake cut off from a meandering river is', ['an oxbow lake', 'a crater lake', 'a lagoon', 'a reservoir'], 'A', 'Oxbow lakes form in the lower course.', 2],
        ['Land built up from sediment at the mouth of a river, such as at the Volta estuary, is', ['a delta', 'a gorge', 'a meander', 'a waterfall'], 'A', 'It forms where the river slows and drops its load.', 1],
        ['The slow downhill movement of soil under gravity is', ['soil creep', 'a landslide', 'a rockfall', 'an avalanche'], 'A', 'Tilted fences and trees are signs of soil creep.', 2],
    ],

    'SHS 3: Population studies in Ghana' => [
        ['Ghana\'s population and housing census is conducted by the', ['Ghana Statistical Service', 'Electoral Commission', 'NCCE', 'Lands Commission'], 'A', 'The most recent census was in 2021.', 1],
        ['According to the 2021 census, Ghana\'s population was about', ['30.8 million', '18 million', '50 million', '24.6 million'], 'A', 'The 2010 census counted about 24.7 million.', 2],
        ['Population density is', ['the number of people per square kilometre', 'the total population', 'the birth rate', 'the number of houses'], 'A', 'Density = population ÷ land area.', 1],
        ['Which is a "push" factor in rural–urban migration?', ['lack of jobs in rural areas', 'better schools in cities', 'bright city lights', 'hospitals in towns'], 'A', 'Push factors drive people out of an area; pull factors attract them to a new one.', 2],
    ],

    'SHS 3: Climate change and waste management' => [
        ['The main human cause of recent climate change is', ['burning fossil fuels and deforestation', 'volcanic eruptions only', 'the earth\'s rotation', 'ocean tides'], 'A', 'These add carbon dioxide and other greenhouse gases.', 1],
        ['The 3Rs of waste management are', ['reduce, reuse and recycle', 'remove, refuse and relax', 'rinse, repeat and return', 'rot, rust and rain'], 'A', 'They reduce the amount of waste sent to landfills.', 1],
        ['A dot map shows the distribution of population by', ['dots that each stand for a fixed number of people', 'colours for different heights', 'lines of equal rainfall', 'arrows showing direction'], 'A', 'Areas with many dots have more people.', 2],
    ],

    'SHS 3: Desert encroachment and landslides' => [
        ['Desert encroachment in northern Ghana is worsened by', ['overgrazing, bush burning and cutting trees', 'tree planting', 'contour ploughing', 'irrigation with drip lines'], 'A', 'Bare land dries out and soil is blown away.', 1],
        ['Which measure helps to check desert encroachment?', ['planting shelter belts of trees', 'burning bush', 'overgrazing', 'clearing vegetation'], 'A', 'Tree belts slow the wind and hold the soil.', 1],
        ['Landslides are most likely on steep slopes after', ['heavy rainfall', 'a long dry spell', 'tree planting', 'a cold night'], 'A', 'Water adds weight and reduces friction in the soil.', 1],
    ],
];
