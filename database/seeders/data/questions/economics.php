<?php

/*
| SHS Economics: WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Basic concepts
    ['Economics is best described as the study of', ['how to make money', 'how scarce resources are allocated to satisfy unlimited wants', 'government only', 'banking and finance only'], 'B', 'Economics studies choice under scarcity.', 1],
    ['A production possibility curve shows', ['the prices of goods', 'the combinations of two goods that can be produced with given resources', 'the demand for a good', 'the profit of a firm'], 'B', 'The PPC shows maximum output combinations with full use of resources.', 2],
    ['Which of the following is an example of a free good?', ['water from a tap', 'air', 'bread', 'electricity'], 'B', 'Air has no price and is not scarce in the economic sense.', 1],
    ['The reward for land as a factor of production is', ['wages', 'interest', 'rent', 'profit'], 'C', 'Land earns rent, labour wages, capital interest and the entrepreneur profit.', 1],
    ['The factor of production that bears risk and organises the others is the', ['labour', 'capital', 'entrepreneur', 'land'], 'C', 'The entrepreneur organises production and bears the risk.', 1],

    // Demand, supply, elasticity
    ['A change in demand (a shift of the demand curve) can be caused by a change in', ['the price of the good itself', 'consumers\' income', 'the quantity supplied', 'the cost of production'], 'B', 'Income, tastes and the prices of related goods shift demand. A change in the good\'s own price is a movement along the curve.', 2],
    ['Goods that are used together, such as cars and petrol, are called', ['substitutes', 'complements', 'inferior goods', 'Giffen goods'], 'B', 'Complements are jointly demanded.', 1],
    ['If the price of tea rises and the demand for coffee increases, tea and coffee are', ['complements', 'substitutes', 'inferior goods', 'normal goods'], 'B', 'Substitutes can replace each other.', 2],
    ['The price of a good rises from GH₵ 10 to GH₵ 12 and quantity demanded falls from 100 to 80 units. Find the price elasticity of demand.', ['0.5', '1', '1.5', '2'], 'B', '%ΔQ = −20%, %ΔP = 20%, so PED = 20/20 = 1 (unitary).', 3],
    ['Demand for a good is price inelastic when', ['a small price change causes a large change in quantity demanded', 'the good has many close substitutes', 'quantity demanded changes by a smaller percentage than price', 'elasticity is greater than 1'], 'C', 'Inelastic demand has elasticity less than 1.', 2],
    ['Equilibrium price is the price at which', ['demand is greater than supply', 'quantity demanded equals quantity supplied', 'supply is greater than demand', 'the government fixes the price'], 'B', 'At equilibrium the market clears.', 1],
    ['A maximum price fixed by the government below the equilibrium price leads to', ['a surplus', 'a shortage', 'no change', 'falling demand'], 'B', 'At a lower price quantity demanded exceeds quantity supplied, so there is a shortage, often with black markets.', 3],
    ['An inferior good is one whose demand', ['falls as income rises', 'rises as income rises', 'never changes', 'rises as its price rises'], 'A', 'Consumers switch to better goods as their income rises.', 2],

    // Production and costs
    ['The law of diminishing returns states that as more units of a variable factor are added to a fixed factor, the', ['total product always falls', 'fixed cost rises', 'average cost always rises', 'marginal product eventually falls'], 'D', 'After a point, each extra unit of the variable factor adds less output.', 3],
    ['Which of the following is a fixed cost?', ['rent of a factory', 'raw materials', 'wages of casual workers', 'fuel for machines'], 'A', 'Rent does not change with the level of output in the short run.', 1],
    ['A firm\'s total cost is GH₵ 500 when it produces 50 units. Its average cost is', ['GH₵ 5', 'GH₵ 450', 'GH₵ 50', 'GH₵ 10'], 'D', 'AC = TC/Q = 500/50 = GH₵ 10.', 1],
    ['Economies of scale lead to', ['lower average cost as output rises', 'higher average cost as output rises', 'higher fixed cost', 'lower total revenue'], 'A', 'Large-scale production spreads costs and lowers average cost.', 2],
    ['Division of labour means', ['dividing workers into groups for sports', 'reducing the number of workers', 'paying workers equally', 'breaking production into tasks done by different workers'], 'D', 'Specialisation by task raises efficiency.', 1],

    // Market structures
    ['A market with only one seller of a product with no close substitutes is', ['monopoly', 'perfect competition', 'oligopoly', 'monopolistic competition'], 'A', 'A monopoly has a single seller.', 1],
    ['In perfect competition, firms are', ['price makers', 'few in number', 'price takers', 'selling differentiated products'], 'C', 'Many firms sell a homogeneous product and accept the market price.', 2],
    ['A market dominated by a few large firms is', ['monopoly', 'duopsony', 'perfect competition', 'oligopoly'], 'D', 'Oligopoly has a few interdependent sellers.', 2],

    // National income, money, banking
    ['National income can be measured by the', ['income, output and expenditure methods', 'demand and supply methods', 'tax method only', 'import method'], 'A', 'The three methods are output, income and expenditure.', 2],
    ['Which of the following is a function of money?', ['a medium of exchange', 'a source of rent', 'a factor of production', 'a type of tax'], 'A', 'Money is a medium of exchange, a unit of account, a store of value and a standard of deferred payment.', 1],
    ['A major problem of the barter system is', ['the double coincidence of wants', 'too much money', 'inflation', 'high interest rates'], 'A', 'Each trader must want what the other has.', 1],
    ['The lender of last resort in Ghana is the', ['GCB Bank', 'Ministry of Finance', 'Bank of Ghana', 'Ghana Stock Exchange'], 'C', 'The central bank lends to commercial banks in difficulty.', 2],
    ['To reduce inflation, the central bank is most likely to', ['lower interest rates', 'reduce reserve requirements', 'print more money', 'raise the policy rate'], 'D', 'Higher interest rates reduce borrowing and spending.', 3],
    ['Inflation caused by rising costs of production is called', ['demand-pull inflation', 'hyperinflation', 'cost-push inflation', 'deflation'], 'C', 'Higher wages and input prices push up prices.', 2],

    // Public finance and international trade
    ['A tax whose rate increases as income increases is', ['regressive', 'proportional', 'progressive', 'indirect'], 'C', 'Progressive taxes take a larger share of higher incomes.', 2],
    ['A budget deficit occurs when government', ['revenue exceeds expenditure', 'stops collecting taxes', 'revenue equals expenditure', 'expenditure exceeds revenue'], 'D', 'Spending more than it earns creates a deficit.', 1],
    ['The theory that countries should specialise in goods they can produce at a lower opportunity cost is the theory of', ['absolute advantage', 'diminishing returns', 'demand', 'comparative advantage'], 'D', 'Comparative advantage (Ricardo) explains gains from trade.', 3],
    ['The balance of trade is the difference between', ['visible exports and visible imports', 'total savings and investment', 'taxes and spending', 'invisible imports and capital flows'], 'A', 'It compares the value of goods exported with goods imported.', 2],
    ['A tax on imported goods is called', ['excise duty', 'income tax', 'tariff (import duty)', 'VAT only'], 'C', 'Tariffs are taxes on imports.', 1],
    ['Devaluation of a country\'s currency makes its exports', ['more expensive abroad', 'illegal', 'unchanged in price', 'cheaper abroad'], 'D', 'Foreign buyers need less of their currency to buy the exports.', 3],

    // Development and population
    ['A high dependency ratio means', ['few workers support many dependants', 'many workers support few dependants', 'everyone is employed', 'the population is falling'], 'A', 'Many children and elderly people depend on a small working population.', 2],
    ['The optimum population is the population size at which', ['the death rate is zero', 'the birth rate is highest', 'output per head is highest', 'everyone is a farmer'], 'C', 'With given resources, output per head is greatest.', 3],
];
