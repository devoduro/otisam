<?php

/*
| SHS ICT (core): WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Hardware and data
    ['Which of the following is a non-volatile memory?', ['RAM', 'ROM', 'cache', 'registers'], 'B', 'ROM keeps its contents when power is switched off.', 2],
    ['The part of the CPU that performs calculations and logical comparisons is the', ['control unit', 'arithmetic logic unit (ALU)', 'register', 'bus'], 'B', 'The ALU carries out arithmetic and logic operations.', 2],
    ['The speed of a processor is usually measured in', ['megabytes', 'gigahertz', 'pixels', 'bits per inch'], 'B', 'Clock speed is measured in hertz, for example 3.2 GHz.', 2],
    ['The quality of a printed image is measured in', ['dpi (dots per inch)', 'rpm', 'Hz', 'MB'], 'A', 'Higher dpi means a sharper print.', 2],
    ['A touch screen is both an input and an', ['output device', 'storage device', 'processing device', 'operating system'], 'A', 'It accepts touch input and displays output.', 1],
    ['Which storage device uses laser light to read and write data?', ['hard disk', 'optical disc (CD/DVD)', 'flash drive', 'magnetic tape'], 'B', 'CDs, DVDs and Blu-ray discs are read by lasers.', 1],
    ['A barcode reader is mainly used in supermarkets to', ['print receipts', 'input product codes quickly', 'store sales data', 'display prices'], 'B', 'It scans barcodes to look up prices and update stock.', 1],
    ['The binary number 1010 is equal to the denary number', ['8', '10', '12', '20'], 'B', '1×8 + 0×4 + 1×2 + 0×1 = 10.', 2],
    ['How many bits make one byte?', ['4', '8', '16', '1024'], 'B', '1 byte = 8 bits.', 1],

    // Software
    ['Software that is free to use and whose source code is available to everyone is', ['proprietary software', 'open-source software', 'shareware', 'malware'], 'B', 'Open-source software, such as Linux, shares its source code.', 2],
    ['A program that translates a whole high-level program into machine code before it runs is a', ['interpreter', 'compiler', 'assembler', 'debugger'], 'B', 'A compiler translates the whole program at once; an interpreter translates line by line.', 3],
    ['Antivirus software is an example of', ['application software', 'a programming language', 'utility software', 'firmware'], 'C', 'Utilities maintain and protect the computer.', 2],
    ['In a database, a single piece of information about a record, such as "Surname", is a', ['table', 'report', 'query', 'field'], 'D', 'Fields are the columns; records are the rows.', 2],
    ['A field that uniquely identifies each record in a database table is the', ['foreign key', 'index', 'primary key', 'form'], 'C', 'A primary key, such as Student ID, is unique for each record.', 2],
    ['In Microsoft Excel, the formula =AVERAGE(B2:B6) calculates', ['the total of B2 to B6', 'the number of cells', 'the largest value', 'the mean of B2 to B6'], 'D', 'AVERAGE returns the arithmetic mean.', 1],
    ['In a spreadsheet, the reference $A$1 is', ['an absolute reference', 'a relative reference', 'a mixed reference', 'a range'], 'A', 'Dollar signs fix both column and row when the formula is copied.', 3],
    ['Which feature of a word processor automatically creates personalised letters from a list of names?', ['spell check', 'word art', 'mail merge', 'track changes'], 'C', 'Mail merge combines a template with a data source.', 2],

    // Networks and the internet
    ['A network topology in which all computers are connected to a central hub or switch is a', ['bus topology', 'mesh topology', 'ring topology', 'star topology'], 'D', 'In a star topology every node links to a central device.', 2],
    ['The protocol used for transferring web pages is', ['HTTP', 'FTP', 'SMTP', 'POP3'], 'A', 'HTTP (or HTTPS) transfers web pages.', 2],
    ['The protocol used to send e-mail is', ['HTTP', 'FTP', 'SMTP', 'DNS'], 'C', 'SMTP sends mail; POP3 or IMAP retrieves it.', 3],
    ['The system that converts domain names such as ghana.gov.gh into IP addresses is', ['DNS', 'HTML', 'ISP', 'URL'], 'A', 'The Domain Name System maps names to IP addresses.', 3],
    ['A company that provides internet access to customers is an', ['ISP', 'IP', 'OS', 'LAN'], 'A', 'An Internet Service Provider supplies internet connections.', 1],
    ['The language used to create web pages is', ['HTML', 'SQL', 'BASIC', 'COBOL'], 'A', 'HTML (HyperText Markup Language) structures web pages.', 1],
    ['Which of these is an example of a wireless technology?', ['Bluetooth', 'Ethernet cable', 'fibre optic cable', 'coaxial cable'], 'A', 'Bluetooth connects devices over short distances without cables.', 1],
    ['A secure website address usually begins with', ['http://', 'www only', 'ftp://', 'https://'], 'D', 'The "s" in HTTPS means the connection is encrypted.', 1],

    // Security, ethics and society
    ['Making copies of important files and storing them elsewhere in case of loss is called', ['backup', 'formatting', 'defragmentation', 'compression'], 'A', 'A backup protects against data loss.', 1],
    ['A security system that monitors and controls traffic between a network and the internet is a', ['modem', 'router', 'firewall', 'hub'], 'C', 'Firewalls block unauthorised access.', 2],
    ['Converting data into a code so that only authorised people can read it is', ['formatting', 'compression', 'decryption', 'encryption'], 'D', 'Encrypted data needs a key to be read.', 2],
    ['Copying and distributing software without permission from the owner is', ['open source', 'freeware', 'software piracy', 'licensing'], 'C', 'Piracy breaks copyright law.', 1],
    ['Sending threatening or hurtful messages to someone online is', ['e-learning', 'netiquette', 'blogging', 'cyberbullying'], 'D', 'Cyberbullying is bullying through digital technology.', 1],
    ['Pain in the wrist from long periods of typing is called', ['backache only', 'eye strain', 'repetitive strain injury (RSI)', 'malaria'], 'C', 'RSI comes from repeated movements; good posture and breaks help prevent it.', 2],
    ['The use of ICT to buy and sell goods over the internet is', ['e-health', 'e-learning', 'e-governance', 'e-commerce'], 'D', 'E-commerce means online trading, such as online shops and mobile money payments.', 1],
    ['Ghana\'s mobile money service is an example of ICT used in', ['sports', 'farming only', 'banking and finance', 'weather forecasting'], 'C', 'Mobile money lets people send, receive and save money by phone.', 1],
];
