<?php

/*
| SHS ICT (core): WASSCE-style objective questions written for Otisam, not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Hardware and data' => [
        ['Which of the following is a non-volatile memory?', ['RAM', 'ROM', 'cache', 'registers'], 'B', 'ROM keeps its contents when power is switched off.', 2],
        ['The part of the CPU that performs calculations and logical comparisons is the', ['control unit', 'arithmetic logic unit (ALU)', 'register', 'bus'], 'B', 'The ALU carries out arithmetic and logic operations.', 2],
        ['The speed of a processor is usually measured in', ['megabytes', 'gigahertz', 'pixels', 'bits per inch'], 'B', 'Clock speed is measured in hertz, for example 3.2 GHz.', 2],
        ['The quality of a printed image is measured in', ['dpi (dots per inch)', 'rpm', 'Hz', 'MB'], 'A', 'Higher dpi means a sharper print.', 2],
        ['A touch screen is both an input and an', ['output device', 'storage device', 'processing device', 'operating system'], 'A', 'It accepts touch input and displays output.', 1],
        ['Which storage device uses laser light to read and write data?', ['hard disk', 'optical disc (CD/DVD)', 'flash drive', 'magnetic tape'], 'B', 'CDs, DVDs and Blu-ray discs are read by lasers.', 1],
        ['A barcode reader is mainly used in supermarkets to', ['print receipts', 'input product codes quickly', 'store sales data', 'display prices'], 'B', 'It scans barcodes to look up prices and update stock.', 1],
        ['The binary number 1010 is equal to the denary number', ['8', '10', '12', '20'], 'B', '1×8 + 0×4 + 1×2 + 0×1 = 10.', 2],
        ['How many bits make one byte?', ['4', '8', '16', '1024'], 'B', '1 byte = 8 bits.', 1],
    ],

    'Software' => [
        ['Software that is free to use and whose source code is available to everyone is', ['proprietary software', 'open-source software', 'shareware', 'malware'], 'B', 'Open-source software, such as Linux, shares its source code.', 2],
        ['A program that translates a whole high-level program into machine code before it runs is a', ['interpreter', 'compiler', 'assembler', 'debugger'], 'B', 'A compiler translates the whole program at once; an interpreter translates line by line.', 3],
        ['Antivirus software is an example of', ['application software', 'a programming language', 'utility software', 'firmware'], 'C', 'Utilities maintain and protect the computer.', 2],
        ['In a database, a single piece of information about a record, such as "Surname", is a', ['table', 'report', 'query', 'field'], 'D', 'Fields are the columns; records are the rows.', 2],
        ['A field that uniquely identifies each record in a database table is the', ['foreign key', 'index', 'primary key', 'form'], 'C', 'A primary key, such as Student ID, is unique for each record.', 2],
        ['In Microsoft Excel, the formula =AVERAGE(B2:B6) calculates', ['the total of B2 to B6', 'the number of cells', 'the largest value', 'the mean of B2 to B6'], 'D', 'AVERAGE returns the arithmetic mean.', 1],
        ['In a spreadsheet, the reference $A$1 is', ['an absolute reference', 'a relative reference', 'a mixed reference', 'a range'], 'A', 'Dollar signs fix both column and row when the formula is copied.', 3],
        ['Which feature of a word processor automatically creates personalised letters from a list of names?', ['spell check', 'word art', 'mail merge', 'track changes'], 'C', 'Mail merge combines a template with a data source.', 2],
    ],

    'Networks and the internet' => [
        ['A network topology in which all computers are connected to a central hub or switch is a', ['bus topology', 'mesh topology', 'ring topology', 'star topology'], 'D', 'In a star topology every node links to a central device.', 2],
        ['The protocol used for transferring web pages is', ['HTTP', 'FTP', 'SMTP', 'POP3'], 'A', 'HTTP (or HTTPS) transfers web pages.', 2],
        ['The protocol used to send e-mail is', ['HTTP', 'FTP', 'SMTP', 'DNS'], 'C', 'SMTP sends mail; POP3 or IMAP retrieves it.', 3],
        ['The system that converts domain names such as ghana.gov.gh into IP addresses is', ['DNS', 'HTML', 'ISP', 'URL'], 'A', 'The Domain Name System maps names to IP addresses.', 3],
        ['A company that provides internet access to customers is an', ['ISP', 'IP', 'OS', 'LAN'], 'A', 'An Internet Service Provider supplies internet connections.', 1],
        ['The language used to create web pages is', ['HTML', 'SQL', 'BASIC', 'COBOL'], 'A', 'HTML (HyperText Markup Language) structures web pages.', 1],
        ['Which of these is an example of a wireless technology?', ['Bluetooth', 'Ethernet cable', 'fibre optic cable', 'coaxial cable'], 'A', 'Bluetooth connects devices over short distances without cables.', 1],
        ['A secure website address usually begins with', ['http://', 'www only', 'ftp://', 'https://'], 'D', 'The "s" in HTTPS means the connection is encrypted.', 1],
    ],

    'Security, ethics and society' => [
        ['Making copies of important files and storing them elsewhere in case of loss is called', ['backup', 'formatting', 'defragmentation', 'compression'], 'A', 'A backup protects against data loss.', 1],
        ['A security system that monitors and controls traffic between a network and the internet is a', ['modem', 'router', 'firewall', 'hub'], 'C', 'Firewalls block unauthorised access.', 2],
        ['Converting data into a code so that only authorised people can read it is', ['formatting', 'compression', 'decryption', 'encryption'], 'D', 'Encrypted data needs a key to be read.', 2],
        ['Copying and distributing software without permission from the owner is', ['open source', 'freeware', 'software piracy', 'licensing'], 'C', 'Piracy breaks copyright law.', 1],
        ['Sending threatening or hurtful messages to someone online is', ['e-learning', 'netiquette', 'blogging', 'cyberbullying'], 'D', 'Cyberbullying is bullying through digital technology.', 1],
        ['Pain in the wrist from long periods of typing is called', ['backache only', 'eye strain', 'repetitive strain injury (RSI)', 'malaria'], 'C', 'RSI comes from repeated movements; good posture and breaks help prevent it.', 2],
        ['The use of ICT to buy and sell goods over the internet is', ['e-health', 'e-learning', 'e-governance', 'e-commerce'], 'D', 'E-commerce means online trading, such as online shops and mobile money payments.', 1],
        ['Ghana\'s mobile money service is an example of ICT used in', ['sports', 'farming only', 'banking and finance', 'weather forecasting'], 'C', 'Mobile money lets people send, receive and save money by phone.', 1],
    ],

    'SHS 1: Multimedia tools and applications' => [
        ['Multimedia combines', ['only printed text', 'text, images, sound, video and animation', 'only numbers', 'only hardware'], 'B', 'Multimedia presents information in several forms at once.', 1],
        ['Which file format is commonly used for compressed music?', ['XLSX', 'DOCX', 'MP3', 'EXE'], 'C', 'MP3 is a compressed audio format. DOCX is a Word document and XLSX a spreadsheet.', 1],
        ['Which application is mainly used to edit photographs?', ['a database server', 'a spreadsheet', 'a compiler', 'an image editor such as GIMP or Photoshop'], 'D', 'Image editors crop, resize and adjust photographs.', 1],
        ['In a word processor, the shortcut Ctrl + Z usually', ['saves the file', 'undoes the last action', 'prints the page', 'closes the program'], 'B', 'Ctrl + Z undoes; Ctrl + S saves and Ctrl + P prints.', 1],
    ],

    'SHS 1: Digital devices' => [
        ['Which of these is an input device?', ['speaker', 'printer', 'scanner', 'projector'], 'C', 'A scanner brings images into the computer. The others output information.', 1],
        ['A smartphone touchscreen is both', ['only a cooling device', 'a storage and a power device', 'only an output device', 'an input and an output device'], 'D', 'It shows output and accepts touch input.', 1],
        ['Which storage device has no moving parts?', ['hard disk drive', 'solid-state drive (SSD)', 'floppy disk drive', 'DVD drive'], 'B', 'An SSD stores data in flash memory chips, so it is faster and tougher than a spinning hard disk.', 2],
        ['A device that lets a computer connect to a network through a cable or Wi-Fi is', ['a mouse', 'a monitor', 'a network interface card', 'a sound card'], 'C', 'The network interface card (NIC) handles the connection to the network.', 2],
    ],

    'SHS 1: The internet and internet service providers' => [
        ['The network that grew into the internet, started in the USA in 1969, was', ['GSM', 'Ethernet', 'Bluetooth', 'ARPANET'], 'D', 'ARPANET linked research computers and pioneered packet switching.', 2],
        ['The World Wide Web was invented by', ['Bill Gates', 'Tim Berners-Lee', 'Charles Babbage', 'Steve Jobs'], 'B', 'Tim Berners-Lee proposed the web at CERN in 1989.', 1],
        ['The main function of an internet service provider (ISP) is to', ['write operating systems', 'build computers', 'give customers access to the internet', 'print documents'], 'C', 'ISPs connect homes and businesses to the internet, often with email and hosting too.', 1],
        ['A web browser is used to', ['compress files', 'type letters offline only', 'design circuits', 'view web pages'], 'D', 'Browsers such as Chrome and Firefox fetch and display web pages.', 1],
    ],

    'SHS 1: Guided and unguided network media' => [
        ['Which of these is a guided (wired) transmission medium?', ['radio waves', 'fibre-optic cable', 'microwaves', 'infrared'], 'B', 'Guided media carry signals along a physical path: twisted pair, coaxial and fibre-optic cable.', 1],
        ['Fibre-optic cables carry data as', ['water pressure', 'sound waves', 'pulses of light', 'magnetic tape'], 'C', 'Light travels along glass fibres with little loss and no electrical interference.', 1],
        ['Which is an unguided (wireless) medium?', ['fibre-optic cable', 'coaxial cable', 'twisted-pair cable', 'satellite links'], 'D', 'Unguided media send signals through the air or space.', 1],
        ['Bluetooth is mainly used for', ['connecting continents', 'short-range wireless connections between nearby devices', 'satellite TV', 'undersea cables'], 'B', 'Bluetooth links devices a few metres apart, such as earphones and phones.', 1],
        ['Which medium is least affected by electrical interference?', ['radio', 'unshielded twisted pair', 'fibre-optic cable', 'coaxial cable'], 'C', 'Light signals are not affected by electromagnetic interference.', 2],
    ],

    'SHS 1: Physical safety when using computers' => [
        ['Long hours of typing without breaks can cause pain in the wrists and fingers known as', ['sunburn', 'malaria', 'asthma', 'repetitive strain injury (RSI)'], 'D', 'RSI comes from repeating the same movements; breaks and good posture help prevent it.', 1],
        ['To reduce eye strain, a computer user should', ['sit very close to the screen', 'take regular breaks and look at distant objects', 'use the computer in total darkness', 'never blink'], 'B', 'A common guide is to look about 6 m away for 20 seconds every 20 minutes.', 1],
        ['Cables trailing across the floor of a computer lab are a hazard because they can', ['delete files', 'slow down the internet', 'cause people to trip and fall', 'cool the room'], 'C', 'Cables should be tucked away or covered to prevent trips.', 1],
        ['Plugging many devices into one socket can cause', ['cleaner air', 'faster computers', 'better Wi-Fi', 'overheating and fire'], 'D', 'Overloaded sockets draw too much current and may overheat.', 1],
    ],

    'SHS 2: Spreadsheets: cells, formulas and functions' => [
        ['In a spreadsheet, the cell reference C5 means', ['row C, column 5', 'column C, row 5', 'the 5th sheet', 'cell number 35'], 'B', 'Columns are letters and rows are numbers.', 1],
        ['Every spreadsheet formula must begin with', ['@', '#', '=', '&'], 'C', 'The equals sign tells the spreadsheet that what follows is a formula.', 1],
        ['What does =SUM(A1:A4) do?', ['finds the average of A1 and A4', 'counts the cells A1 to A4', 'multiplies A1 by A4', 'adds the values in cells A1 to A4'], 'D', 'SUM adds every value in the range A1, A2, A3 and A4.', 1],
        ['What is the result of the formula =2+3*4?', ['20', '14', '24', '9'], 'B', 'Multiplication comes before addition: 3 × 4 = 12, then 2 + 12 = 14.', 2],
        ['Which is an absolute cell reference?', ['B$', 'B2', '$B$2', '2B'], 'C', 'The $ signs stop the column and row from changing when the formula is copied.', 2],
        ['A cell shows #DIV/0!. This means the formula', ['is correct', 'has too many digits', 'contains text', 'tried to divide by zero or an empty cell'], 'D', 'Check the cell used as the divisor.', 2],
        ['Which function gives the average of the cells B2 to B10?', ['=SUM(B2:B10)', '=AVERAGE(B2:B10)', '=COUNT(B2:B10)', '=MAX(B2:B10)'], 'B', 'AVERAGE adds the values and divides by how many there are.', 1],
    ],

    'SHS 2: Spreadsheets: charts, sorting and filtering' => [
        ['Which chart best shows how each part makes up a whole, such as shares of a budget?', ['scatter chart', 'line chart', 'pie chart', 'histogram'], 'C', 'A pie chart shows each part as a slice of the whole.', 1],
        ['Filtering a list of students so that only those in Form 2 show is done with', ['a macro virus', 'a chart', 'a hyperlink', 'a filter'], 'D', 'A filter hides the rows that do not match the chosen condition.', 1],
        ['Sorting names from A to Z is sorting in', ['descending order', 'ascending order', 'random order', 'reverse order'], 'B', 'Ascending order runs from smallest to largest, or A to Z.', 1],
    ],

    'SHS 2: ICTs in society' => [
        ['Using mobile money to pay bills is an example of ICT in', ['weather forecasting', 'sport', 'finance', 'mining only'], 'C', 'Mobile money lets people send, receive and pay with their phones.', 1],
        ['Telemedicine means', ['training nurses in person only', 'making medicines in a factory', 'measuring temperature', 'providing health care at a distance using ICT'], 'D', 'Doctors can consult and advise patients by video or phone.', 1],
        ['One negative effect of ICT on society is', ['faster communication', 'cyberbullying', 'online learning', 'easy access to information'], 'B', 'ICT can be misused, for example to harass people online.', 1],
    ],

    'SHS 2: Mobile platforms and browser plug-ins' => [
        ['Android and iOS are examples of', ['spreadsheet programs', 'web browsers', 'mobile operating systems', 'search engines'], 'C', 'They run smartphones and tablets.', 1],
        ['A browser extension (plug-in) is', ['a virus', 'a type of monitor', 'an internet cable', 'a small program that adds features to a web browser'], 'D', 'Extensions add features such as ad blocking or translation.', 1],
        ['Apps for Android phones are usually installed from the', ['BIOS', 'Google Play Store', 'Recycle Bin', 'Control Panel'], 'B', 'Official app stores check apps for safety; iPhone apps come from the App Store.', 1],
    ],

    'SHS 2: Ethernet and network security' => [
        ['The connector at the end of an Ethernet network cable is', ['HDMI', 'USB-C', 'RJ45', 'VGA'], 'C', 'RJ45 plugs connect twisted-pair Ethernet cables.', 2],
        ['A network device that forwards data only to the computer it is meant for is a', ['speaker', 'hub', 'repeater', 'switch'], 'D', 'A switch learns which device is on each port, unlike a hub, which sends data to every port.', 2],
        ['A firewall is used to', ['block unauthorised access to or from a network', 'cool the server', 'speed up printing', 'store backups'], 'A', 'A firewall filters traffic according to security rules.', 1],
        ['Which is the strongest password?', ['K7#pz!q9Lm', 'password', '123456', 'myname'], 'A', 'Long passwords that mix letters, numbers and symbols are hardest to guess.', 1],
        ['Two-factor authentication means logging in with', ['two different kinds of proof, such as a password and a code sent to your phone', 'two passwords typed twice', 'two usernames', 'two computers'], 'A', 'A second factor protects the account even if the password is stolen.', 2],
    ],

    'SHS 3: Presentation software' => [
        ['In presentation software, a transition is an effect that', ['appears when moving from one slide to the next', 'moves an object within a slide', 'checks spelling', 'prints handouts'], 'A', 'Transitions apply between slides; animations apply to objects on a slide.', 2],
        ['In PowerPoint, pressing F5 usually', ['starts the slide show from the beginning', 'saves the file', 'inserts a picture', 'closes the program'], 'A', 'F5 starts the show from slide 1; Shift + F5 starts from the current slide.', 2],
        ['Which view shows small versions of all the slides so you can rearrange them?', ['Slide Sorter view', 'Reading view', 'Notes Page view', 'Outline view only'], 'A', 'Slide Sorter lets you drag slides into a new order.', 2],
        ['Changing the slide master changes', ['the layout and design of all slides based on it', 'only the first slide', 'the computer\'s wallpaper', 'the printer settings'], 'A', 'The slide master holds the fonts, colours and placeholders shared by slides.', 2],
        ['A hyperlink in a presentation lets you', ['jump to another slide, file or web page', 'record sound', 'add a transition', 'change the theme'], 'A', 'Hyperlinks make presentations interactive.', 1],
    ],

    'SHS 3: Desktop publishing' => [
        ['Desktop publishing software is mainly used to', ['design printed materials such as flyers and newsletters', 'calculate payroll', 'browse the web', 'write program code'], 'A', 'DTP programs such as Publisher and Scribus combine text and images on a page.', 1],
        ['Which colour model is used for professional printing?', ['CMYK', 'RGB', 'HTML', 'ASCII'], 'A', 'Printers mix cyan, magenta, yellow and black inks.', 1],
        ['In desktop publishing, text is placed in', ['text frames (boxes)', 'cells', 'slides', 'fields'], 'A', 'Text frames can be moved, resized and linked so that text flows between them.', 2],
        ['Extending a background image slightly beyond the trim edge of a page so that no white edge shows is called', ['bleed', 'kerning', 'gutter', 'leading'], 'A', 'Bleed allows for small shifts when the printed sheet is cut.', 3],
    ],

    'SHS 3: Collaboration tools' => [
        ['Which tool lets several people edit the same document online at the same time?', ['Google Docs', 'Notepad', 'Paint', 'Calculator'], 'A', 'Cloud word processors save changes for everyone in real time.', 1],
        ['Storing files on remote servers and reaching them over the internet is', ['cloud storage', 'local storage', 'RAM', 'cache'], 'A', 'Cloud storage lets you reach your files from any device.', 1],
        ['Zoom and Google Meet are examples of', ['video-conferencing tools', 'spreadsheets', 'antivirus programs', 'operating systems'], 'A', 'They allow online meetings with video, audio and screen sharing.', 1],
    ],
];
