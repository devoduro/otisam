<?php

/*
| SHS Financial Accounting: WASSCE-style objective questions written for Otisam, not copied from
| WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Concepts and principles' => [
        ['The concept that a business is treated as separate from its owner is the', ['going concern concept', 'business entity concept', 'consistency concept', 'prudence concept'], 'B', 'Under the entity concept, the owner\'s private transactions are not business transactions.', 1],
        ['The concept that assumes a business will continue operating for the foreseeable future is the', ['going concern concept', 'matching concept', 'materiality concept', 'realisation concept'], 'A', 'Assets are valued on the assumption that the business will continue.', 2],
        ['The concept that matches expenses to the revenue of the same period is the', ['accruals (matching) concept', 'dual aspect concept', 'money measurement concept', 'cost concept'], 'A', 'Expenses are recorded in the period they help earn revenue, not when cash is paid.', 2],
        ['The prudence concept requires accountants to', ['overstate profits', 'anticipate losses but not profits', 'ignore losses', 'value assets at selling price'], 'B', 'Prudence avoids overstating assets and profit.', 3],
        ['Every transaction has two effects, one debit and one credit. This is the', ['business entity concept', 'dual aspect concept', 'realisation concept', 'going concern concept'], 'B', 'The dual aspect concept is the basis of double entry.', 1],
    ],

    'Double entry and books of prime entry' => [
        ['When cash is paid to a supplier, the entry is', ['debit cash, credit supplier', 'debit supplier, credit cash', 'debit purchases, credit cash', 'debit cash, credit purchases'], 'B', 'The supplier\'s account (a liability) is reduced by a debit; cash leaving is credited.', 2],
        ['When goods are sold on credit to Kofi, the entry is', ['debit sales, credit Kofi', 'debit Kofi, credit sales', 'debit cash, credit sales', 'debit Kofi, credit cash'], 'B', 'Kofi becomes a receivable (debit) and sales revenue is credited.', 2],
        ['The book used to record the return of goods by customers is the', ['purchases returns day book', 'sales returns (returns inwards) day book', 'sales day book', 'journal proper'], 'B', 'Goods returned by customers are returns inwards.', 2],
        ['Which of the following is recorded in the journal proper?', ['credit sales', 'cash received', 'correction of errors', 'credit purchases'], 'C', 'The journal records non-routine items such as error corrections and opening entries.', 2],
        ['A cash book with columns for discount, cash and bank is a', ['single-column cash book', 'two-column cash book', 'three-column cash book', 'petty cash book'], 'C', 'The three columns are discount, cash and bank.', 1],
        ['The petty cash system in which the cashier is given a fixed float that is restored at the end of each period is the', ['imprest system', 'ledger system', 'control account system', 'suspense system'], 'A', 'Under the imprest system the amount spent is reimbursed to restore the float.', 2],
        ['Discount allowed is', ['an income to the business', 'an expense to the business', 'a liability', 'an asset'], 'B', 'Discount allowed is given to customers for prompt payment, so it is an expense.', 2],
    ],

    'Trial balance and errors' => [
        ['Which of these errors will NOT affect the agreement of the trial balance?', ['an error of omission', 'a single-entry error', 'an addition error in one account', 'entering a debit as a credit in one account only'], 'A', 'Omitting a transaction entirely leaves both sides equally short.', 3],
        ['Posting a payment for repairs to the motor vehicles account is an error of', ['commission', 'principle', 'omission', 'original entry'], 'B', 'Treating revenue expenditure as capital expenditure is an error of principle.', 3],
        ['When a trial balance does not agree, the difference is placed temporarily in a', ['capital account', 'suspense account', 'drawings account', 'sales account'], 'B', 'A suspense account holds the difference until the errors are found.', 2],
    ],

    'Final accounts' => [
        ['Gross profit is calculated as', ['sales − cost of sales', 'sales − expenses', 'capital − drawings', 'assets − liabilities'], 'A', 'Gross profit = net sales − cost of goods sold.', 1],
        ['Cost of sales = opening inventory + purchases − closing inventory. If opening inventory is GH₵ 2,000, purchases GH₵ 8,000 and closing inventory GH₵ 3,000, find cost of sales.', ['GH₵ 3,000', 'GH₵ 13,000', 'GH₵ 10,000', 'GH₵ 7,000'], 'D', '2,000 + 8,000 − 3,000 = GH₵ 7,000.', 2],
        ['Carriage inwards is treated in the final accounts as part of', ['cost of sales', 'selling expenses', 'capital', 'current liabilities'], 'A', 'Carriage inwards adds to the cost of purchases.', 3],
        ['Carriage outwards is shown in the income statement as', ['part of cost of sales', 'a deduction from sales', 'income', 'an expense (selling and distribution)'], 'D', 'Carriage outwards is a cost of delivering goods to customers.', 3],
        ['Rent of GH₵ 1,200 is due for the year, but only GH₵ 900 has been paid. The GH₵ 300 is', ['a prepayment', 'income received in advance', 'an accrual', 'drawings'], 'C', 'An expense owing at the year end is an accrued expense.', 2],
        ['Insurance paid in advance for next year is shown in the statement of financial position as', ['a current liability', 'capital', 'a non-current asset', 'a current asset (prepayment)'], 'D', 'A prepayment is a benefit still to be received, so it is a current asset.', 2],
        ['Which of the following is a non-current asset?', ['inventory', 'trade receivables', 'motor vehicles', 'cash at bank'], 'C', 'Motor vehicles are held for long-term use.', 1],
        ['Which of the following is a current liability?', ['bank loan repayable in 10 years', 'land', 'capital', 'trade payables'], 'D', 'Trade payables are due within a year.', 1],
    ],

    'Depreciation, bad debts, control accounts' => [
        ['A machine costing GH₵ 10,000 has a residual value of GH₵ 2,000 and a useful life of 4 years. Find the annual depreciation by the straight-line method.', ['GH₵ 2,000', 'GH₵ 2,500', 'GH₵ 3,000', 'GH₵ 8,000'], 'A', '(10,000 − 2,000) ÷ 4 = GH₵ 2,000 per year.', 3],
        ['A vehicle costing GH₵ 20,000 is depreciated at 20% per year by the reducing balance method. Find the depreciation for the second year.', ['GH₵ 3,200', 'GH₵ 4,000', 'GH₵ 16,000', 'GH₵ 8,000'], 'A', 'Year 1: 20% of 20,000 = 4,000, leaving 16,000. Year 2: 20% of 16,000 = 3,200.', 4],
        ['A debt that cannot be collected and is written off is', ['a bad debt', 'a provision for discount', 'a prepayment', 'an accrual'], 'A', 'Irrecoverable debts are written off as an expense.', 1],
        ['The total of trade receivables is checked using the', ['purchases ledger control account', 'cash book', 'sales ledger control account', 'trading account'], 'C', 'The sales ledger control account summarises all receivables.', 3],
    ],

    'Bank reconciliation' => [
        ['Cheques sent to suppliers but not yet presented to the bank are called', ['dishonoured cheques', 'standing orders', 'uncredited cheques', 'unpresented cheques'], 'D', 'They appear in the cash book but not yet on the bank statement.', 2],
        ['Bank charges shown on the bank statement but not in the cash book should be', ['ignored', 'debited in the cash book', 'credited in the cash book', 'added to capital'], 'C', 'They reduce the bank balance, so they are credited in the cash book.', 3],
    ],

    'Partnerships, companies, non-profit and incomplete records' => [
        ['In the absence of a partnership agreement, the Partnership Act provides that profits are shared', ['in the capital ratio', 'according to experience', 'by age', 'equally'], 'D', 'Without an agreement, partners share profits and losses equally and get no interest on capital.', 3],
        ['The excess of income over expenditure in a club\'s accounts is called', ['net profit', 'gross profit', 'surplus', 'capital'], 'C', 'Non-profit organisations report a surplus or deficit.', 2],
        ['The receipts and payments account of a club is a summary of its', ['income and expenditure', 'profits', 'assets and liabilities', 'cash and bank transactions'], 'D', 'It records all cash received and paid, whatever period they relate to.', 2],
        ['A statement of affairs is prepared to find the', ['bank balance', 'gross profit', 'capital of a business that keeps incomplete records', 'value of inventory'], 'C', 'Capital = assets − liabilities when full books are not kept.', 3],
        ['Ordinary shareholders of a company receive a share of profits called', ['interest', 'rent', 'wages', 'dividend'], 'D', 'Dividends are distributions of profit to shareholders.', 1],
    ],

    'SHS 1: The accounting equation and double entry' => [
        ['A business has assets of GH¢50,000 and liabilities of GH¢15,000. What is its capital?', ['GH¢65,000', 'GH¢35,000', 'GH¢15,000', 'GH¢50,000'], 'B', 'Capital = assets − liabilities = 50,000 − 15,000 = GH¢35,000.', 1],
        ['Goods bought on credit from Mensah are recorded by', ['debiting cash and crediting Mensah', 'debiting Mensah and crediting purchases', 'debiting purchases and crediting Mensah', 'debiting purchases and crediting cash'], 'C', 'Purchases increase (debit); Mensah becomes a creditor (credit).', 2],
        ['Credit sales are first recorded in the', ['journal proper', 'purchases day book', 'cash book', 'sales day book'], 'D', 'Day books are books of original entry; totals are later posted to the ledger.', 1],
        ['A petty cash book kept on the imprest system is', ['never balanced', 'topped up to the same float at the end of each period', 'used for large payments only', 'kept by the bank'], 'B', 'The amount spent is reimbursed, restoring the float.', 2],
    ],

    'SHS 1: Final accounts of a sole trader' => [
        ['Opening inventory is GH¢5,000, purchases GH¢20,000 and closing inventory GH¢3,000. What is the cost of sales?', ['GH¢18,000', 'GH¢28,000', 'GH¢22,000', 'GH¢25,000'], 'C', 'Cost of sales = 5,000 + 20,000 − 3,000 = GH¢22,000.', 2],
        ['Sales are GH¢30,000 and cost of sales is GH¢22,000. What is the gross profit?', ['GH¢30,000', 'GH¢52,000', 'GH¢22,000', 'GH¢8,000'], 'D', 'Gross profit = sales − cost of sales.', 1],
        ['A machine costs GH¢12,000, has a residual value of GH¢2,000 and a life of 5 years. What is the yearly straight-line depreciation?', ['GH¢2,400', 'GH¢2,000', 'GH¢2,800', 'GH¢10,000'], 'B', '(12,000 − 2,000) ÷ 5 = GH¢2,000 a year.', 2],
        ['In a statement of financial position, a bank overdraft is shown as', ['a current asset', 'a non-current asset', 'a current liability', 'capital'], 'C', 'It is repayable on demand.', 2],
    ],

    'SHS 2: Accounting concepts and conventions' => [
        ['The concept that treats the business as separate from its owner is the', ['consistency concept', 'going concern concept', 'prudence concept', 'business entity concept'], 'D', 'The owner\'s private spending is not a business expense.', 1],
        ['Recording income and expenses in the period they relate to, not when cash changes hands, follows the', ['money measurement concept', 'accruals (matching) concept', 'dual aspect concept', 'realisation concept only'], 'B', 'It gives a true picture of each period\'s profit.', 2],
        ['The concept that tells accountants not to overstate assets or profits is', ['materiality', 'going concern', 'prudence', 'entity'], 'C', 'Losses are provided for as soon as they are expected; gains are recorded only when realised.', 2],
        ['Assuming a business will continue to operate in the foreseeable future is the', ['accruals concept', 'consistency concept', 'historical cost concept', 'going concern concept'], 'D', 'It justifies valuing assets at cost rather than break-up value.', 2],
    ],

    'SHS 2: Correction of errors and bank reconciliation' => [
        ['A transaction left out of the books entirely is an error of', ['principle', 'commission', 'omission', 'original entry'], 'C', 'Errors of omission do not affect the agreement of the trial balance.', 2],
        ['Cheques sent to suppliers but not yet presented to the bank are', ['standing orders', 'uncredited lodgements', 'dishonoured cheques', 'unpresented cheques'], 'D', 'They explain why the bank balance is higher than the cash book balance.', 2],
        ['Bank charges shown on the bank statement but not yet in the cash book should be', ['debited in the cash book', 'credited in the cash book', 'ignored', 'added to capital'], 'B', 'They reduce the bank balance, so the cash book is updated with a credit entry.', 3],
    ],

    'SHS 2: Not-for-profit organisations and incomplete records' => [
        ['A club\'s summary of all cash received and paid during a year is the', ['trading account', 'income and expenditure account', 'receipts and payments account', 'appropriation account'], 'C', 'It is a summarised cash book.', 2],
        ['In a not-for-profit organisation, capital is called the', ['drawings', 'share capital', 'retained earnings', 'accumulated fund'], 'D', 'A surplus of income over expenditure is added to it.', 2],
        ['Where full double-entry records are not kept, capital is found by preparing a', ['trial balance', 'statement of affairs', 'cash budget', 'bank reconciliation'], 'B', 'Capital = assets − liabilities at a given date.', 2],
    ],

    'Cost accounting and inventory' => [
        ['Direct materials are GH¢4,000, direct labour GH¢3,000 and direct expenses GH¢500. What is the prime cost?', ['GH¢4,500', 'GH¢7,000', 'GH¢7,500', 'GH¢3,500'], 'C', 'Prime cost is the total of all direct costs.', 2],
        ['Overheads of GH¢60,000 are absorbed over 20,000 direct labour hours. What is the absorption rate?', ['GH¢1,200 per hour', 'GH¢0.33 per hour', 'GH¢30 per hour', 'GH¢3 per hour'], 'D', '60,000 ÷ 20,000 = GH¢3 per labour hour.', 2],
        ['Annual demand is 1,000 units, the cost of placing an order is GH¢20 and the holding cost is GH¢4 per unit a year. What is the economic order quantity?', ['50 units', '100 units', '200 units', '10 units'], 'B', 'EOQ = √(2 × 20 × 1,000 ÷ 4) = √10,000 = 100 units.', 3],
        ['Under FIFO, materials issued to production are priced at the cost of the', ['average inventory', 'newest inventory', 'oldest inventory', 'replacement cost'], 'C', 'First in, first out.', 2],
        ['A costing method used where production passes through a series of stages, as in soap making, is', ['batch costing only', 'job costing', 'contract costing', 'process costing'], 'D', 'Costs are gathered for each process and averaged over the units produced.', 2],
    ],

    'SHS 3: Professional ethics in accounting' => [
        ['Which is a fundamental principle of professional ethics for accountants?', ['secrecy from auditors', 'objectivity', 'profit at all costs', 'favouritism'], 'B', 'The five principles are integrity, objectivity, professional competence and due care, confidentiality, and professional behaviour.', 2],
        ['An accountant who audits work he prepared himself faces a', ['advocacy threat', 'intimidation threat', 'self-review threat', 'familiarity threat'], 'C', 'He may not spot his own errors.', 3],
        ['Not disclosing a client\'s information without proper authority is the principle of', ['competence', 'objectivity', 'integrity', 'confidentiality'], 'D', 'Information may be disclosed only with permission or when the law requires it.', 1],
    ],

    'SHS 3: Ratio analysis' => [
        ['Current assets are GH¢40,000 and current liabilities GH¢20,000. What is the current ratio?', ['2 : 1', '1 : 2', '0.5 : 1', '20 : 1'], 'A', 'Current ratio = current assets ÷ current liabilities.', 1],
        ['Gross profit is GH¢8,000 on sales of GH¢40,000. What is the gross profit margin?', ['20%', '25%', '5%', '80%'], 'A', '8,000 ÷ 40,000 × 100% = 20%.', 2],
        ['The quick (acid test) ratio leaves out', ['inventory', 'cash', 'receivables', 'current liabilities'], 'A', 'Inventory may take time to turn into cash.', 2],
    ],

    'SHS 3: Partnership accounts' => [
        ['Where there is no partnership agreement, profits and losses are usually shared', ['equally', 'in the ratio of capitals', 'according to age', 'by the senior partner only'], 'A', 'The law fills the gap: equal shares, no interest on capital and no salaries.', 2],
        ['The account that shows how a partnership\'s profit is divided among the partners is the', ['appropriation account', 'trading account', 'realisation account', 'suspense account'], 'A', 'It deals with interest on capital, salaries and profit shares.', 2],
    ],

    'SHS 3: Labour costs, budgets and break-even' => [
        ['Fixed costs are GH¢30,000, the selling price is GH¢50 per unit and the variable cost is GH¢30 per unit. What is the break-even point?', ['1,500 units', '600 units', '1,000 units', '3,000 units'], 'A', 'Contribution per unit = 50 − 30 = GH¢20, so BEP = 30,000 ÷ 20 = 1,500 units.', 3],
        ['The difference between actual sales and break-even sales is the', ['margin of safety', 'contribution', 'gross profit', 'overhead'], 'A', 'It shows how far sales can fall before losses begin.', 2],
        ['Paying workers according to the number of units they produce is', ['piece rate', 'time rate', 'salary', 'overtime premium'], 'A', 'Time rate pays by the hour regardless of output.', 1],
        ['When actual cost is less than standard cost, the variance is', ['favourable', 'adverse', 'zero', 'fixed'], 'A', 'Adverse variances mean costs were higher than planned.', 2],
        ['A budget that adjusts to the actual level of activity is a', ['flexible budget', 'fixed budget', 'cash budget only', 'master budget only'], 'A', 'It allows fair comparison of costs at different output levels.', 2],
    ],
];
