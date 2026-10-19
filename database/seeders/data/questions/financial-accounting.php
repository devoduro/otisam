<?php

/*
| SHS Financial Accounting: WASSCE-style objective questions written for Otisam, not copied from
| WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Concepts and principles
    ['The concept that a business is treated as separate from its owner is the', ['going concern concept', 'business entity concept', 'consistency concept', 'prudence concept'], 'B', 'Under the entity concept, the owner\'s private transactions are not business transactions.', 1],
    ['The concept that assumes a business will continue operating for the foreseeable future is the', ['going concern concept', 'matching concept', 'materiality concept', 'realisation concept'], 'A', 'Assets are valued on the assumption that the business will continue.', 2],
    ['The concept that matches expenses to the revenue of the same period is the', ['accruals (matching) concept', 'dual aspect concept', 'money measurement concept', 'cost concept'], 'A', 'Expenses are recorded in the period they help earn revenue, not when cash is paid.', 2],
    ['The prudence concept requires accountants to', ['overstate profits', 'anticipate losses but not profits', 'ignore losses', 'value assets at selling price'], 'B', 'Prudence avoids overstating assets and profit.', 3],
    ['Every transaction has two effects, one debit and one credit. This is the', ['business entity concept', 'dual aspect concept', 'realisation concept', 'going concern concept'], 'B', 'The dual aspect concept is the basis of double entry.', 1],

    // Double entry and books of prime entry
    ['When cash is paid to a supplier, the entry is', ['debit cash, credit supplier', 'debit supplier, credit cash', 'debit purchases, credit cash', 'debit cash, credit purchases'], 'B', 'The supplier\'s account (a liability) is reduced by a debit; cash leaving is credited.', 2],
    ['When goods are sold on credit to Kofi, the entry is', ['debit sales, credit Kofi', 'debit Kofi, credit sales', 'debit cash, credit sales', 'debit Kofi, credit cash'], 'B', 'Kofi becomes a receivable (debit) and sales revenue is credited.', 2],
    ['The book used to record the return of goods by customers is the', ['purchases returns day book', 'sales returns (returns inwards) day book', 'sales day book', 'journal proper'], 'B', 'Goods returned by customers are returns inwards.', 2],
    ['Which of the following is recorded in the journal proper?', ['credit sales', 'cash received', 'correction of errors', 'credit purchases'], 'C', 'The journal records non-routine items such as error corrections and opening entries.', 2],
    ['A cash book with columns for discount, cash and bank is a', ['single-column cash book', 'two-column cash book', 'three-column cash book', 'petty cash book'], 'C', 'The three columns are discount, cash and bank.', 1],
    ['The petty cash system in which the cashier is given a fixed float that is restored at the end of each period is the', ['imprest system', 'ledger system', 'control account system', 'suspense system'], 'A', 'Under the imprest system the amount spent is reimbursed to restore the float.', 2],
    ['Discount allowed is', ['an income to the business', 'an expense to the business', 'a liability', 'an asset'], 'B', 'Discount allowed is given to customers for prompt payment, so it is an expense.', 2],

    // Trial balance and errors
    ['Which of these errors will NOT affect the agreement of the trial balance?', ['an error of omission', 'a single-entry error', 'an addition error in one account', 'entering a debit as a credit in one account only'], 'A', 'Omitting a transaction entirely leaves both sides equally short.', 3],
    ['Posting a payment for repairs to the motor vehicles account is an error of', ['commission', 'principle', 'omission', 'original entry'], 'B', 'Treating revenue expenditure as capital expenditure is an error of principle.', 3],
    ['When a trial balance does not agree, the difference is placed temporarily in a', ['capital account', 'suspense account', 'drawings account', 'sales account'], 'B', 'A suspense account holds the difference until the errors are found.', 2],

    // Final accounts
    ['Gross profit is calculated as', ['sales − cost of sales', 'sales − expenses', 'capital − drawings', 'assets − liabilities'], 'A', 'Gross profit = net sales − cost of goods sold.', 1],
    ['Cost of sales = opening inventory + purchases − closing inventory. If opening inventory is GH₵ 2,000, purchases GH₵ 8,000 and closing inventory GH₵ 3,000, find cost of sales.', ['GH₵ 3,000', 'GH₵ 13,000', 'GH₵ 10,000', 'GH₵ 7,000'], 'D', '2,000 + 8,000 − 3,000 = GH₵ 7,000.', 2],
    ['Carriage inwards is treated in the final accounts as part of', ['cost of sales', 'selling expenses', 'capital', 'current liabilities'], 'A', 'Carriage inwards adds to the cost of purchases.', 3],
    ['Carriage outwards is shown in the income statement as', ['part of cost of sales', 'a deduction from sales', 'income', 'an expense (selling and distribution)'], 'D', 'Carriage outwards is a cost of delivering goods to customers.', 3],
    ['Rent of GH₵ 1,200 is due for the year, but only GH₵ 900 has been paid. The GH₵ 300 is', ['a prepayment', 'income received in advance', 'an accrual', 'drawings'], 'C', 'An expense owing at the year end is an accrued expense.', 2],
    ['Insurance paid in advance for next year is shown in the statement of financial position as', ['a current liability', 'capital', 'a non-current asset', 'a current asset (prepayment)'], 'D', 'A prepayment is a benefit still to be received, so it is a current asset.', 2],
    ['Which of the following is a non-current asset?', ['inventory', 'trade receivables', 'motor vehicles', 'cash at bank'], 'C', 'Motor vehicles are held for long-term use.', 1],
    ['Which of the following is a current liability?', ['bank loan repayable in 10 years', 'land', 'capital', 'trade payables'], 'D', 'Trade payables are due within a year.', 1],

    // Depreciation, bad debts, control accounts
    ['A machine costing GH₵ 10,000 has a residual value of GH₵ 2,000 and a useful life of 4 years. Find the annual depreciation by the straight-line method.', ['GH₵ 2,000', 'GH₵ 2,500', 'GH₵ 3,000', 'GH₵ 8,000'], 'A', '(10,000 − 2,000) ÷ 4 = GH₵ 2,000 per year.', 3],
    ['A vehicle costing GH₵ 20,000 is depreciated at 20% per year by the reducing balance method. Find the depreciation for the second year.', ['GH₵ 3,200', 'GH₵ 4,000', 'GH₵ 16,000', 'GH₵ 8,000'], 'A', 'Year 1: 20% of 20,000 = 4,000, leaving 16,000. Year 2: 20% of 16,000 = 3,200.', 4],
    ['A debt that cannot be collected and is written off is', ['a bad debt', 'a provision for discount', 'a prepayment', 'an accrual'], 'A', 'Irrecoverable debts are written off as an expense.', 1],
    ['The total of trade receivables is checked using the', ['purchases ledger control account', 'cash book', 'sales ledger control account', 'trading account'], 'C', 'The sales ledger control account summarises all receivables.', 3],

    // Bank reconciliation
    ['Cheques sent to suppliers but not yet presented to the bank are called', ['dishonoured cheques', 'standing orders', 'uncredited cheques', 'unpresented cheques'], 'D', 'They appear in the cash book but not yet on the bank statement.', 2],
    ['Bank charges shown on the bank statement but not in the cash book should be', ['ignored', 'debited in the cash book', 'credited in the cash book', 'added to capital'], 'C', 'They reduce the bank balance, so they are credited in the cash book.', 3],

    // Partnerships, companies, non-profit and incomplete records
    ['In the absence of a partnership agreement, the Partnership Act provides that profits are shared', ['in the capital ratio', 'according to experience', 'by age', 'equally'], 'D', 'Without an agreement, partners share profits and losses equally and get no interest on capital.', 3],
    ['The excess of income over expenditure in a club\'s accounts is called', ['net profit', 'gross profit', 'surplus', 'capital'], 'C', 'Non-profit organisations report a surplus or deficit.', 2],
    ['The receipts and payments account of a club is a summary of its', ['income and expenditure', 'profits', 'assets and liabilities', 'cash and bank transactions'], 'D', 'It records all cash received and paid, whatever period they relate to.', 2],
    ['A statement of affairs is prepared to find the', ['bank balance', 'gross profit', 'capital of a business that keeps incomplete records', 'value of inventory'], 'C', 'Capital = assets − liabilities when full books are not kept.', 3],
    ['Ordinary shareholders of a company receive a share of profits called', ['interest', 'rent', 'wages', 'dividend'], 'D', 'Dividends are distributions of profit to shareholders.', 1],
];
