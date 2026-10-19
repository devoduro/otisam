<?php

/*
| SHS Economics: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Basic concepts' => [
        ['Economics is best described as the study of', ['how to make money', 'how scarce resources are allocated to satisfy unlimited wants', 'government only', 'banking and finance only'], 'B', 'Economics studies choice under scarcity.', 1],
        ['A production possibility curve shows', ['the prices of goods', 'the combinations of two goods that can be produced with given resources', 'the demand for a good', 'the profit of a firm'], 'B', 'The PPC shows maximum output combinations with full use of resources.', 2],
        ['Which of the following is an example of a free good?', ['water from a tap', 'air', 'bread', 'electricity'], 'B', 'Air has no price and is not scarce in the economic sense.', 1],
        ['The reward for land as a factor of production is', ['wages', 'interest', 'rent', 'profit'], 'C', 'Land earns rent, labour wages, capital interest and the entrepreneur profit.', 1],
        ['The factor of production that bears risk and organises the others is the', ['labour', 'capital', 'entrepreneur', 'land'], 'C', 'The entrepreneur organises production and bears the risk.', 1],
    ],

    'Demand, supply, elasticity' => [
        ['A change in demand (a shift of the demand curve) can be caused by a change in', ['the price of the good itself', 'consumers\' income', 'the quantity supplied', 'the cost of production'], 'B', 'Income, tastes and the prices of related goods shift demand. A change in the good\'s own price is a movement along the curve.', 2],
        ['Goods that are used together, such as cars and petrol, are called', ['substitutes', 'complements', 'inferior goods', 'Giffen goods'], 'B', 'Complements are jointly demanded.', 1],
        ['If the price of tea rises and the demand for coffee increases, tea and coffee are', ['complements', 'substitutes', 'inferior goods', 'normal goods'], 'B', 'Substitutes can replace each other.', 2],
        ['The price of a good rises from GH₵ 10 to GH₵ 12 and quantity demanded falls from 100 to 80 units. Find the price elasticity of demand.', ['0.5', '1', '1.5', '2'], 'B', '%ΔQ = −20%, %ΔP = 20%, so PED = 20/20 = 1 (unitary).', 3],
        ['Demand for a good is price inelastic when', ['a small price change causes a large change in quantity demanded', 'the good has many close substitutes', 'quantity demanded changes by a smaller percentage than price', 'elasticity is greater than 1'], 'C', 'Inelastic demand has elasticity less than 1.', 2],
        ['Equilibrium price is the price at which', ['demand is greater than supply', 'quantity demanded equals quantity supplied', 'supply is greater than demand', 'the government fixes the price'], 'B', 'At equilibrium the market clears.', 1],
        ['A maximum price fixed by the government below the equilibrium price leads to', ['a surplus', 'a shortage', 'no change', 'falling demand'], 'B', 'At a lower price quantity demanded exceeds quantity supplied, so there is a shortage, often with black markets.', 3],
        ['An inferior good is one whose demand', ['falls as income rises', 'rises as income rises', 'never changes', 'rises as its price rises'], 'A', 'Consumers switch to better goods as their income rises.', 2],
    ],

    'Production and costs' => [
        ['The law of diminishing returns states that as more units of a variable factor are added to a fixed factor, the', ['total product always falls', 'fixed cost rises', 'average cost always rises', 'marginal product eventually falls'], 'D', 'After a point, each extra unit of the variable factor adds less output.', 3],
        ['Which of the following is a fixed cost?', ['rent of a factory', 'raw materials', 'wages of casual workers', 'fuel for machines'], 'A', 'Rent does not change with the level of output in the short run.', 1],
        ['A firm\'s total cost is GH₵ 500 when it produces 50 units. Its average cost is', ['GH₵ 5', 'GH₵ 450', 'GH₵ 50', 'GH₵ 10'], 'D', 'AC = TC/Q = 500/50 = GH₵ 10.', 1],
        ['Economies of scale lead to', ['lower average cost as output rises', 'higher average cost as output rises', 'higher fixed cost', 'lower total revenue'], 'A', 'Large-scale production spreads costs and lowers average cost.', 2],
        ['Division of labour means', ['dividing workers into groups for sports', 'reducing the number of workers', 'paying workers equally', 'breaking production into tasks done by different workers'], 'D', 'Specialisation by task raises efficiency.', 1],
    ],

    'Market structures' => [
        ['A market with only one seller of a product with no close substitutes is', ['monopoly', 'perfect competition', 'oligopoly', 'monopolistic competition'], 'A', 'A monopoly has a single seller.', 1],
        ['In perfect competition, firms are', ['price makers', 'few in number', 'price takers', 'selling differentiated products'], 'C', 'Many firms sell a homogeneous product and accept the market price.', 2],
        ['A market dominated by a few large firms is', ['monopoly', 'duopsony', 'perfect competition', 'oligopoly'], 'D', 'Oligopoly has a few interdependent sellers.', 2],
    ],

    'National income, money, banking' => [
        ['National income can be measured by the', ['income, output and expenditure methods', 'demand and supply methods', 'tax method only', 'import method'], 'A', 'The three methods are output, income and expenditure.', 2],
        ['Which of the following is a function of money?', ['a medium of exchange', 'a source of rent', 'a factor of production', 'a type of tax'], 'A', 'Money is a medium of exchange, a unit of account, a store of value and a standard of deferred payment.', 1],
        ['A major problem of the barter system is', ['the double coincidence of wants', 'too much money', 'inflation', 'high interest rates'], 'A', 'Each trader must want what the other has.', 1],
        ['The lender of last resort in Ghana is the', ['GCB Bank', 'Ministry of Finance', 'Bank of Ghana', 'Ghana Stock Exchange'], 'C', 'The central bank lends to commercial banks in difficulty.', 2],
        ['To reduce inflation, the central bank is most likely to', ['lower interest rates', 'reduce reserve requirements', 'print more money', 'raise the policy rate'], 'D', 'Higher interest rates reduce borrowing and spending.', 3],
        ['Inflation caused by rising costs of production is called', ['demand-pull inflation', 'hyperinflation', 'cost-push inflation', 'deflation'], 'C', 'Higher wages and input prices push up prices.', 2],
    ],

    'Public finance and international trade' => [
        ['A tax whose rate increases as income increases is', ['regressive', 'proportional', 'progressive', 'indirect'], 'C', 'Progressive taxes take a larger share of higher incomes.', 2],
        ['A budget deficit occurs when government', ['revenue exceeds expenditure', 'stops collecting taxes', 'revenue equals expenditure', 'expenditure exceeds revenue'], 'D', 'Spending more than it earns creates a deficit.', 1],
        ['The theory that countries should specialise in goods they can produce at a lower opportunity cost is the theory of', ['absolute advantage', 'diminishing returns', 'demand', 'comparative advantage'], 'D', 'Comparative advantage (Ricardo) explains gains from trade.', 3],
        ['The balance of trade is the difference between', ['visible exports and visible imports', 'total savings and investment', 'taxes and spending', 'invisible imports and capital flows'], 'A', 'It compares the value of goods exported with goods imported.', 2],
        ['A tax on imported goods is called', ['excise duty', 'income tax', 'tariff (import duty)', 'VAT only'], 'C', 'Tariffs are taxes on imports.', 1],
        ['Devaluation of a country\'s currency makes its exports', ['more expensive abroad', 'illegal', 'unchanged in price', 'cheaper abroad'], 'D', 'Foreign buyers need less of their currency to buy the exports.', 3],
    ],

    'Development and population' => [
        ['A high dependency ratio means', ['few workers support many dependants', 'many workers support few dependants', 'everyone is employed', 'the population is falling'], 'A', 'Many children and elderly people depend on a small working population.', 2],
        ['The optimum population is the population size at which', ['the death rate is zero', 'the birth rate is highest', 'output per head is highest', 'everyone is a farmer'], 'C', 'With given resources, output per head is greatest.', 3],
    ],

    'SHS 1: Introduction to economics' => [
        ['The basic economic problem arises because', ['money is unlimited', 'human wants are unlimited while resources are scarce', 'goods are free', 'everybody is rich'], 'B', 'Scarcity forces people to make choices.', 1],
        ['A student chooses to buy a textbook instead of a pair of shoes. The shoes are the', ['utility', 'real income', 'opportunity cost', 'demand'], 'C', 'Opportunity cost is the next best alternative given up.', 1],
        ['A point inside a production possibility curve shows', ['economic growth', 'full employment', 'an impossible output', 'unemployed or inefficiently used resources'], 'D', 'The economy could produce more of both goods.', 2],
        ['The questions "what to produce, how to produce and for whom to produce" are the', ['functions of money', 'basic questions every economy must answer', 'types of taxes', 'laws of demand'], 'B', 'Every economic system answers them in its own way.', 1],
    ],

    'SHS 1: Demand' => [
        ['The law of demand states that, other things being equal,', ['price does not affect demand', 'as price rises, quantity demanded rises', 'as price rises, quantity demanded falls', 'supply creates demand'], 'C', 'The demand curve slopes downwards from left to right.', 1],
        ['A fall in the price of a good itself causes', ['no change', 'a shift of its demand curve to the right', 'a shift of its supply curve', 'a movement along its demand curve'], 'D', 'Changes in other factors, such as income, shift the curve.', 2],
        ['Cars and petrol are', ['substitutes', 'complementary goods', 'inferior goods', 'Giffen goods'], 'B', 'They are used together, so dearer petrol reduces the demand for cars.', 1],
        ['An increase in consumers\' income usually shifts the demand curve for a normal good', ['nowhere', 'to the left', 'to the right', 'downwards along itself'], 'C', 'More is demanded at every price.', 2],
    ],

    'SHS 1: Utility' => [
        ['Total utility from 1, 2 and 3 oranges is 10, 18 and 24 units. What is the marginal utility of the third orange?', ['10', '8', '24', '6'], 'D', 'MU = 24 − 18 = 6.', 2],
        ['The law of diminishing marginal utility states that', ['satisfaction always rises equally', 'each extra unit consumed gives less additional satisfaction', 'total utility always falls', 'prices fall as income rises'], 'B', 'The fifth glass of water satisfies less than the first.', 1],
        ['A consumer gets the most satisfaction from a budget when', ['total utility is zero', 'all money is spent on one good', 'the marginal utility per cedi is equal for all goods', 'the cheapest goods are bought'], 'C', 'This is the equi-marginal principle.', 3],
    ],

    'SHS 1: Production' => [
        ['The reward for labour as a factor of production is', ['profit', 'rent', 'interest', 'wages'], 'D', 'Land earns rent, capital earns interest and the entrepreneur earns profit.', 1],
        ['Breaking production into small tasks done by different workers is', ['mass unemployment', 'division of labour', 'barter', 'nationalisation'], 'B', 'It increases speed and skill but can make work boring.', 1],
        ['10 workers produce 250 bags. What is the average product of labour?', ['240 bags', '2,500 bags', '25 bags', '10 bags'], 'C', 'AP = total product ÷ number of workers = 250 ÷ 10 = 25.', 1],
        ['Who organises the other factors of production and bears the risk?', ['the consumer', 'the landlord', 'the worker', 'the entrepreneur'], 'D', 'The entrepreneur earns profit for taking the risk.', 1],
    ],

    'SHS 1: Supply' => [
        ['The law of supply states that, other things being equal,', ['a higher price leads to a smaller quantity supplied', 'a higher price leads to a larger quantity supplied', 'supply never changes', 'demand equals supply'], 'B', 'Higher prices make production more profitable.', 1],
        ['An improvement in production technology shifts the supply curve', ['nowhere', 'to the left', 'to the right', 'vertically upwards'], 'C', 'Lower costs mean more is supplied at each price.', 2],
        ['Beef and hides are an example of', ['composite demand', 'competitive demand', 'complementary demand', 'joint supply'], 'D', 'Producing one automatically produces the other.', 2],
    ],

    'SHS 1: Markets and prices' => [
        ['Demand is Qd = 100 − 2P and supply is Qs = 20 + 2P. What is the equilibrium price?', ['40', '20', '60', '10'], 'B', '100 − 2P = 20 + 2P, so 4P = 80 and P = 20. The quantity is 60.', 3],
        ['When there is excess demand in a market, the price tends to', ['stay the same', 'fall', 'rise', 'become zero'], 'C', 'Buyers compete for the limited goods.', 1],
        ['A maximum price set by the government below equilibrium usually leads to', ['higher supply', 'surpluses', 'lower demand', 'shortages and black markets'], 'D', 'At the low price, quantity demanded exceeds quantity supplied.', 2],
        ['A market where goods are bought and sold through the internet is', ['a commodity exchange only', 'an online (electronic) market', 'a black market', 'a barter market'], 'B', 'E-commerce links buyers and sellers without a physical market.', 1],
    ],

    'SHS 1: Economic systems and macroeconomic variables' => [
        ['An economy in which the state owns and plans most production is', ['a mixed economy', 'a free market economy', 'a command (planned) economy', 'a subsistence economy'], 'C', 'Prices and output are set by government planning.', 1],
        ['Ghana\'s economy, with both private businesses and state enterprises, is best described as', ['a traditional economy', 'a command economy', 'a pure market economy', 'a mixed economy'], 'D', 'Most countries today have mixed economies.', 1],
        ['Inflation in Ghana is usually measured with the', ['stock market index', 'Consumer Price Index (CPI)', 'exchange rate only', 'population census'], 'B', 'The CPI tracks the prices of a basket of goods and services.', 2],
        ['Unemployment that happens when workers are between jobs is', ['seasonal unemployment', 'structural unemployment', 'frictional unemployment', 'cyclical unemployment'], 'C', 'It is usually short-term.', 2],
    ],

    'SHS 1: Money and financial institutions' => [
        ['Under barter, trade is difficult mainly because of the need for', ['interest rates', 'a central bank', 'paper money', 'a double coincidence of wants'], 'D', 'Each trader must want exactly what the other offers.', 1],
        ['Money used to compare the values of different goods is serving as a', ['store of value', 'unit of account', 'standard of deferred payment', 'medium of exchange'], 'B', 'Prices express value in cedis.', 2],
        ['Which institution issues Ghana\'s currency?', ['Ghana Revenue Authority', 'GCB Bank', 'Bank of Ghana', 'Ministry of Trade'], 'C', 'The central bank issues the cedi and controls the money supply.', 1],
        ['Shares of public companies in Ghana are traded on the', ['Treasury', 'Bank of Ghana', 'Ghana Commodity Exchange only', 'Ghana Stock Exchange'], 'D', 'The Ghana Stock Exchange is in Accra.', 1],
    ],

    'SHS 2: Tools of economic analysis' => [
        ['Which is a positive economic statement?', ['The government ought to cut taxes.', 'Unemployment in Ghana rose last year.', 'Prices should be lower.', 'Everyone deserves a car.'], 'B', 'Positive statements can be tested with facts; normative statements express opinions.', 2],
        ['A normative statement in economics is one that', ['is always true', 'can be tested against data', 'contains a value judgement about what ought to be', 'uses only numbers'], 'C', 'Words such as "should" and "ought" signal normative statements.', 2],
    ],

    'SHS 2: Costs and revenue' => [
        ['A firm has fixed costs of GH¢200 and a variable cost of GH¢10 per unit. What is the total cost of 50 units?', ['GH¢2,500', 'GH¢500', 'GH¢210', 'GH¢700'], 'D', 'TC = FC + VC = 200 + 10 × 50 = GH¢700.', 2],
        ['For the firm making 50 units at a total cost of GH¢700, what is the average cost?', ['GH¢10', 'GH¢14', 'GH¢50', 'GH¢4'], 'B', 'AC = TC ÷ Q = 700 ÷ 50 = GH¢14.', 2],
        ['A firm maximises profit where', ['price is highest', 'total cost is lowest', 'marginal revenue equals marginal cost', 'average cost is zero'], 'C', 'Producing beyond that point adds more to cost than to revenue.', 3],
        ['Costs that do not change with output, such as rent, are', ['average costs', 'variable costs', 'marginal costs', 'fixed costs'], 'D', 'They must be paid even when output is zero.', 1],
    ],

    'SHS 2: Market structures' => [
        ['A market with many buyers and sellers of an identical product, and free entry, is', ['monopoly', 'perfect competition', 'oligopoly', 'duopoly'], 'B', 'Each firm is a price taker.', 2],
        ['In monopolistic competition, firms compete mainly through', ['a single seller', 'identical products', 'government control', 'product differentiation and branding'], 'D', 'Hairdressers and restaurants are examples.', 2],
        ['A single seller of a product with no close substitutes is a', ['wholesaler', 'monopolist', 'retailer', 'consumer'], 'B', 'Barriers to entry keep other firms out.', 1],
    ],

    'SHS 2: GDP, inflation and exchange rates' => [
        ['Gross Domestic Product (GDP) is the total value of', ['imports only', 'all money in the banks', 'final goods and services produced within a country in a year', 'government spending only'], 'C', 'It measures the size of the economy.', 2],
        ['The Consumer Price Index rises from 110 to 120. What is the rate of inflation?', ['120%', '10%', '12%', 'about 9.1%'], 'D', 'Inflation = (120 − 110) ÷ 110 × 100% ≈ 9.1%.', 3],
        ['A depreciation of the cedi against the dollar makes Ghana\'s imports', ['cheaper in cedis', 'more expensive in cedis', 'unaffected', 'free'], 'B', 'More cedis are needed to buy each dollar.', 2],
    ],

    'SHS 2: Public finance and the national budget' => [
        ['VAT is an example of', ['a subsidy', 'a direct tax', 'an indirect tax', 'a grant'], 'C', 'It is collected on goods and services and passed on to consumers.', 1],
        ['A tax that takes a higher percentage from people with higher incomes is', ['progressive', 'regressive', 'proportional', 'flat'], 'A', 'Ghana\'s personal income tax rates rise with income.', 2],
        ['When government spending is greater than its revenue, there is a', ['budget deficit', 'budget surplus', 'balanced budget', 'trade surplus'], 'A', 'Deficits are often financed by borrowing.', 1],
        ['In Ghana, the annual budget is presented to Parliament by the', ['Minister for Finance', 'Chief Justice', 'Governor of the Bank of Ghana', 'Speaker of Parliament'], 'A', 'Parliament must approve it before it takes effect.', 1],
    ],

    'SHS 2: Agriculture, industry and trade' => [
        ['A country should specialise in goods it can produce at a lower opportunity cost than others. This is the principle of', ['comparative advantage', 'absolute poverty', 'diminishing returns', 'price control'], 'A', 'Specialisation and trade make both countries better off.', 2],
        ['The difference between the value of a country\'s visible exports and visible imports is the', ['balance of trade', 'balance of payments', 'national debt', 'GDP'], 'A', 'The balance of payments includes services and capital flows too.', 2],
        ['A tax placed on imported goods is a', ['tariff', 'subsidy', 'quota', 'VAT refund'], 'A', 'Tariffs protect local industries but raise prices.', 1],
    ],

    'SHS 3: Elasticity of demand and supply' => [
        ['The price of a good rises from GH¢10 to GH¢12, and quantity demanded falls from 100 to 80. What is the price elasticity of demand?', ['−1 (unitary)', '−0.5', '−2', '0'], 'A', '%ΔQ = −20% and %ΔP = +20%, so PED = −20 ÷ 20 = −1.', 3],
        ['Demand for necessities such as salt is usually', ['price inelastic', 'perfectly elastic', 'highly elastic', 'zero'], 'A', 'People buy roughly the same amount whatever the price.', 2],
        ['An inferior good has an income elasticity of demand that is', ['negative', 'positive', 'zero', 'infinite'], 'A', 'As income rises, people buy less of it.', 3],
        ['Two goods with a positive cross elasticity of demand are', ['substitutes', 'complements', 'unrelated', 'inferior goods'], 'A', 'A rise in the price of one raises demand for the other.', 3],
        ['The supply of fresh tomatoes is usually inelastic in the short run because', ['they take time to grow and cannot be stored long', 'they are luxuries', 'they are imported', 'farmers dislike them'], 'A', 'Producers cannot quickly increase supply when prices rise.', 2],
    ],

    'SHS 3: Distribution and price control' => [
        ['A common channel of distribution for consumer goods is', ['manufacturer → wholesaler → retailer → consumer', 'consumer → retailer → manufacturer', 'retailer → manufacturer → wholesaler', 'wholesaler → consumer → manufacturer'], 'A', 'Middlemen break bulk, store, transport and finance goods.', 1],
        ['A function of the wholesaler is to', ['break bulk', 'set government prices', 'print money', 'collect income tax'], 'A', 'Wholesalers buy in large quantities and sell in smaller lots to retailers.', 1],
        ['A legal minimum wage set above the market wage is an example of', ['a price floor', 'a price ceiling', 'a subsidy', 'a tariff'], 'A', 'A price floor can create a surplus, here unemployment.', 3],
    ],

    'SHS 3: Firms and industrialisation' => [
        ['A group of firms producing the same or similar products is', ['an industry', 'a firm', 'a market price', 'a cooperative only'], 'A', 'For example, all the cement producers form the cement industry.', 1],
        ['Ghana\'s One District, One Factory policy is aimed mainly at', ['industrialisation and job creation across districts', 'reducing exports', 'closing farms', 'importing more goods'], 'A', 'Local factories add value to local raw materials.', 1],
    ],
];
