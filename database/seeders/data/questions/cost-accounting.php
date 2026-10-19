<?php

/*
| SHS Cost Accounting: WASSCE-style objective questions written for Otisam, not copied from WAEC
| papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Cost concepts' => [
        ['Cost accounting is mainly concerned with', ['recording, analysing and controlling the costs of products and services', 'preparing tax returns only', 'auditing the accounts', 'paying dividends'], 'A', 'Cost accounting helps management to know and control costs.', 1],
        ['Costs that can be traced directly to a unit of product are', ['direct costs', 'overheads', 'fixed costs', 'period costs'], 'A', 'Direct materials, labour and expenses go straight into the product.', 1],
        ['Prime cost is the total of', ['direct materials, direct labour and direct expenses', 'all overheads', 'fixed and variable costs', 'selling and distribution costs'], 'A', 'Prime cost = all direct costs.', 1],
        ['Indirect costs that cannot be traced to a specific product are', ['overheads', 'prime costs', 'direct wages', 'direct materials'], 'A', 'Overheads include factory rent, supervision and electricity.', 1],
        ['A cost that stays the same in total whatever the level of output (within a range) is a', ['variable cost', 'fixed cost', 'semi-variable cost', 'marginal cost'], 'B', 'Rent and salaries of supervisors are typical fixed costs.', 1],
        ['A cost that changes in direct proportion to output is a', ['fixed cost', 'variable cost', 'sunk cost', 'step cost'], 'B', 'Direct materials vary with output.', 1],
        ['A telephone bill with a fixed monthly rental plus charges per call is an example of a', ['fixed cost', 'variable cost', 'semi-variable cost', 'sunk cost'], 'C', 'It has both a fixed and a variable element.', 2],
        ['A unit of product or service for which costs are collected is a', ['cost unit', 'cost centre', 'cost driver', 'profit centre'], 'A', 'Examples include one chair, one passenger-kilometre or one batch.', 2],
        ['A department or location to which costs are charged is a', ['cost centre', 'cost unit', 'invoice', 'ledger'], 'A', 'Cost centres include the machining or assembly departments.', 2],
    ],

    'Materials' => [
        ['The level of inventory at which a new order should be placed is the', ['reorder level', 'maximum level', 'minimum level', 'economic order quantity'], 'A', 'The reorder level allows for usage during the lead time.', 2],
        ['Daily usage is 50 units and the lead time is 6 days. With no safety stock, find the reorder level.', ['56 units', '300 units', '44 units', '8 units'], 'B', 'Reorder level = usage × lead time = 50 × 6 = 300 units.', 3],
        ['The order quantity that minimises the total of ordering and holding costs is the', ['reorder level', 'economic order quantity (EOQ)', 'safety stock', 'buffer stock'], 'B', 'EOQ = √(2CoD/Ch).', 3],
        ['Annual demand is 1,800 units, the cost of placing an order is GH₵ 25 and the holding cost is GH₵ 1 per unit per year. Find the economic order quantity.', ['150 units', '300 units', '600 units', '90,000 units'], 'B', 'EOQ = √(2 × 25 × 1,800 ÷ 1) = √90,000 = 300 units.', 4],
        ['Under FIFO, materials issued are priced at', ['the latest purchase prices', 'the earliest purchase prices', 'the average price', 'the standard price'], 'B', 'First in, first out issues the oldest stock first.', 2],
        ['In times of rising prices, FIFO gives a closing inventory value that is', ['lower than under LIFO', 'higher than under LIFO', 'the same as LIFO', 'zero'], 'B', 'Closing inventory is valued at the latest (higher) prices.', 3],
        ['The document used to request materials from the store is the', ['material requisition note', 'goods received note', 'purchase order', 'delivery note'], 'A', 'Departments use requisitions to draw materials from the store.', 2],
        ['The document prepared when goods are received from a supplier is the', ['goods received note', 'material requisition', 'credit note', 'bin card'], 'A', 'The GRN records the quantity and condition of goods received.', 2],
        ['A bin card records', ['sales made', 'wages paid', 'overheads absorbed', 'quantities of each item received, issued and in stock'], 'D', 'Bin cards are kept by storekeepers and show quantities only.', 2],
    ],

    'Labour' => [
        ['A worker paid by the number of units produced is on a', ['time rate', 'piece rate', 'salary', 'bonus only'], 'B', 'Piece rate pays per unit of output.', 1],
        ['A worker earns GH₵ 15 per hour and works 40 hours. Find the gross wage.', ['GH₵ 55', 'GH₵ 400', 'GH₵ 600', 'GH₵ 1,500'], 'C', '15 × 40 = GH₵ 600.', 1],
        ['The rate at which workers leave an organisation and are replaced is', ['bonus', 'idle time', 'overtime', 'labour turnover'], 'D', 'High labour turnover raises recruitment and training costs.', 2],
        ['Time for which workers are paid but no production takes place, for example due to a machine breakdown, is', ['overtime', 'idle time', 'piece time', 'lead time'], 'B', 'Idle time is paid non-productive time.', 2],
    ],

    'Overheads' => [
        ['Charging overheads to cost centres on a fair basis is', ['allocation', 'absorption', 'apportionment', 'under-absorption'], 'C', 'Apportionment shares common overheads, such as rent by floor area.', 3],
        ['Charging a whole overhead to a single cost centre that caused it is', ['reapportionment', 'apportionment', 'absorption', 'allocation'], 'D', 'Allocation charges costs wholly to one centre.', 3],
        ['Factory rent is best apportioned to departments on the basis of', ['number of employees', 'value of machinery', 'floor area', 'direct labour hours'], 'C', 'Rent relates to the space each department occupies.', 2],
        ['Budgeted overheads are GH₵ 40,000 and budgeted machine hours are 8,000. Find the overhead absorption rate.', ['GH₵ 0.20 per hour', 'GH₵ 48,000', 'GH₵ 32,000', 'GH₵ 5 per hour'], 'D', 'OAR = 40,000 ÷ 8,000 = GH₵ 5 per machine hour.', 2],
        ['When overheads absorbed are less than overheads actually incurred, there is', ['over-absorption', 'a profit', 'under-absorption', 'no effect'], 'C', 'Under-absorbed overheads are added to cost (charged against profit).', 3],
    ],

    'Marginal costing and costing methods' => [
        ['Contribution is', ['profit plus tax', 'sales minus fixed costs', 'fixed costs minus variable costs', 'sales minus variable costs'], 'D', 'Contribution covers fixed costs and then provides profit.', 2],
        ['A product sells for GH₵ 25 and its variable cost is GH₵ 15. Fixed costs are GH₵ 20,000. Find the break-even point in units.', ['800 units', '1,333 units', '2,000 units', '20,000 units'], 'C', 'Break-even = 20,000 ÷ (25 − 15) = 2,000 units.', 3],
        ['The contribution to sales ratio is also known as the', ['margin of safety', 'current ratio', 'gearing ratio', 'profit/volume (P/V) ratio'], 'D', 'C/S ratio = contribution ÷ sales.', 3],
        ['The costing method used by a building contractor for large, long-term projects is', ['batch costing', 'process costing', 'contract costing', 'service costing'], 'C', 'Contract costing tracks each long-term contract.', 2],
        ['The costing method used where production passes through a series of continuous stages, as in oil refining, is', ['job costing', 'batch costing', 'contract costing', 'process costing'], 'D', 'Process costing averages costs over continuous output.', 2],
        ['A predetermined cost used to measure performance by comparison with actual cost is a', ['sunk cost', 'historical cost', 'standard cost', 'opportunity cost'], 'C', 'Variances show the difference between standard and actual costs.', 3],
    ],
];
