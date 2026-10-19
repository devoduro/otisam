<?php

/*
| SHS Computing (2023 curriculum elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Computing curriculum.
| Python is used for the programming questions.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Data representation' => [
        ['Convert the binary number 1011 to decimal.', ['9', '11', '13', '10'], 'B', '1011 = 8 + 0 + 2 + 1 = 11.', 2],
        ['Convert the decimal number 25 to binary.', ['11001', '10101', '11010', '10011'], 'A', '25 = 16 + 8 + 1, so the bits for 16, 8 and 1 are set: 11001.', 2],
        ['What is the hexadecimal number 2F in decimal?', ['32', '45', '47', '215'], 'C', '2F = 2 × 16 + 15 = 47.', 3],
        ['How many bits make up one byte?', ['4', '16', '2', '8'], 'D', 'A byte is a group of 8 bits.', 1],
        ['How many bytes are in 1 kibibyte (KiB)?', ['1000', '1024', '100', '512'], 'B', 'The binary prefix kibi means 2¹⁰ = 1024. A kilobyte (kB) in SI units is 1000 bytes.', 2],
        ['In 8-bit two\'s complement, how is −1 written?', ['00000001', '10000001', '11111111', '11111110'], 'C', 'Invert 00000001 to get 11111110, then add 1: 11111111.', 4],
    ],

    'Computer architecture' => [
        ['Which part of the CPU carries out arithmetic and logical operations?', ['control unit', 'arithmetic logic unit', 'program counter', 'memory address register'], 'B', 'The ALU does calculations and comparisons. The control unit directs the other parts.', 1],
        ['The program counter holds', ['the address of the next instruction', 'the result of the last calculation', 'data being sent to memory', 'the number of programs running'], 'A', 'After each fetch the program counter moves on to point at the next instruction.', 2],
        ['In the fetch–decode–execute cycle, which step comes immediately after an instruction is fetched?', ['execute', 'store', 'decode', 'interrupt'], 'C', 'The fetched instruction must be decoded so the control unit knows what to do before it is executed.', 2],
        ['Which memory loses its contents when the power is switched off?', ['ROM', 'hard disk', 'flash drive', 'RAM'], 'D', 'RAM is volatile. ROM, hard disks and flash drives keep their data without power.', 1],
    ],

    'Logic' => [
        ['An AND gate gives an output of 1 only when', ['either input is 1', 'both inputs are 0', 'the inputs are different', 'both inputs are 1'], 'D', 'AND needs every input to be 1.', 1],
        ['A NOT gate with an input of 1 gives an output of', ['1', '0', '2', '10'], 'B', 'A NOT gate inverts its input, so 1 becomes 0.', 1],
        ['What is the output of an XOR gate when both inputs are 1?', ['0', '1', '10', '11'], 'A', 'XOR gives 1 only when the inputs differ. With both inputs 1 the output is 0.', 3],
    ],

    'Programming in Python' => [
        ['What does print(7 // 2) display in Python?', ['3.5', '4', '3', '1'], 'C', '// is floor division: 7 ÷ 2 = 3.5, rounded down to 3.', 2],
        ['What does print(7 % 3) display in Python?', ['2', '1', '0', '3'], 'B', '% gives the remainder: 7 = 2 × 3 + 1, so the remainder is 1.', 2],
        ['In Python, what is the data type of the value 3.0?', ['int', 'str', 'bool', 'float'], 'D', 'A number with a decimal point is a float, even when the fraction part is zero.', 1],
        ['If x = [5, 8, 2, 9] in Python, what is x[2]?', ['8', '2', '9', '5'], 'B', 'Python lists start at index 0, so x[0] = 5, x[1] = 8 and x[2] = 2.', 2],
        ['How many times does the body of the loop for i in range(1, 6): run?', ['6', '4', '5', '1'], 'C', 'range(1, 6) gives 1, 2, 3, 4, 5. The end value 6 is not included, so the loop runs 5 times.', 2],
        ['A program that translates all of the source code into machine code before it runs is', ['an interpreter', 'an assembler', 'a compiler', 'a linker'], 'C', 'A compiler translates the whole program first. An interpreter translates and runs one statement at a time.', 2],
    ],

    'Algorithms and data structures' => [
        ['Binary search only works if the list is', ['sorted', 'of even length', 'stored in a linked list', 'free of negative numbers'], 'A', 'Binary search halves the list each step by comparing with the middle item, which only makes sense when the items are in order.', 2],
        ['In the worst case, how many comparisons does a linear search of n items make?', ['log₂ n', 'n²', '1', 'n'], 'D', 'Linear search may have to check every item once, so n comparisons.', 3],
        ['Which sorting algorithm repeatedly compares neighbouring items and swaps them if they are in the wrong order?', ['merge sort', 'bubble sort', 'binary sort', 'quick search'], 'B', 'Bubble sort makes passes through the list, swapping adjacent items until no swaps are needed.', 2],
        ['A stack removes items in which order?', ['first in, first out', 'random order', 'last in, first out', 'smallest item first'], 'C', 'A stack is LIFO: the last item pushed on is the first popped off.', 2],
        ['Which data structure removes items in the same order they were added?', ['queue', 'stack', 'tree', 'graph'], 'A', 'A queue is FIFO: first in, first out.', 2],
    ],

    'Networks and the internet' => [
        ['Which version of the Internet Protocol uses 128-bit addresses?', ['IPv4', 'IPv6', 'MAC', 'DNS'], 'B', 'IPv6 addresses are 128 bits long. IPv4 addresses are 32 bits.', 2],
        ['The main job of DNS is to', ['translate domain names into IP addresses', 'encrypt emails', 'assign MAC addresses', 'block viruses'], 'A', 'The Domain Name System looks up the IP address that belongs to a name such as example.com.', 2],
        ['A network in which every device connects to a central switch or hub uses a', ['ring topology', 'bus topology', 'mesh topology', 'star topology'], 'D', 'In a star topology every node has its own link to the central device.', 1],
        ['Which protocol is used to send email from a client to a mail server?', ['FTP', 'SMTP', 'HTTP', 'DHCP'], 'B', 'SMTP (Simple Mail Transfer Protocol) sends mail. FTP moves files and DHCP hands out IP addresses.', 2],
    ],

    'Databases' => [
        ['A field that uniquely identifies each record in a table is the', ['foreign key', 'index', 'primary key', 'query'], 'C', 'The primary key has a different value for every record. A foreign key links to a primary key in another table.', 2],
        ['Which SQL command retrieves data from a table?', ['INSERT', 'UPDATE', 'DELETE', 'SELECT'], 'D', 'SELECT reads data. INSERT adds rows, UPDATE changes them and DELETE removes them.', 1],
    ],

    'Security, software development and emerging technology' => [
        ['An email that pretends to come from a bank and asks for your PIN is an example of', ['phishing', 'a firewall', 'encryption', 'a backup'], 'A', 'Phishing tricks people into giving away private details by pretending to be a trusted sender.', 1],
        ['Turning readable data into a coded form that only authorised people can read is called', ['compression', 'encryption', 'validation', 'formatting'], 'B', 'Encryption scrambles data with a key, and only someone with the right key can decrypt it.', 1],
        ['Which is usually the first stage of the software development life cycle?', ['testing', 'maintenance', 'coding', 'requirements analysis'], 'D', 'Developers first find out what the system must do, then design, code, test and maintain it.', 2],
        ['Machine learning is best described as', ['a computer learning patterns from data to make predictions', 'training people to type faster', 'building computer hardware', 'a type of operating system'], 'A', 'In machine learning a program improves at a task by finding patterns in example data instead of following only fixed rules.', 2],
    ],

    'SHS 1: Computer structure' => [
        ['In the von Neumann architecture, programs and data are', ['kept on separate machines', 'stored together in the same main memory', 'stored only on paper', 'never stored'], 'B', 'This is the stored-program concept.', 2],
        ['Which bus carries the address of the memory location being read or written?', ['control bus', 'data bus', 'address bus', 'power bus'], 'C', 'The data bus carries the data; the control bus carries signals such as read and write.', 2],
        ['The register that holds data just read from, or about to be written to, memory is the', ['accumulator only', 'program counter', 'memory address register', 'memory data register (MDR)'], 'D', 'The MAR holds the address; the MDR holds the data.', 3],
        ['A processor with a clock speed of 3 GHz performs about', ['3 million cycles per second', '3 billion cycles per second', '3 cycles per second', '3,000 cycles per second'], 'B', 'Giga means 10⁹.', 2],
    ],

    'SHS 1: Hardware and software' => [
        ['Which of these is system software?', ['a web browser', 'a word processor', 'an operating system', 'a game'], 'C', 'System software runs the computer; application software does tasks for the user.', 1],
        ['Antivirus and disk-cleanup programs are examples of', ['firmware only', 'application suites', 'programming languages', 'utility software'], 'D', 'Utilities help maintain and protect the computer.', 1],
        ['Software stored permanently in ROM to start up the computer, such as the BIOS, is', ['freeware', 'firmware', 'malware', 'shareware'], 'B', 'It runs before the operating system loads.', 2],
        ['Software whose source code anyone may view, change and share is', ['malware', 'proprietary software', 'open-source software', 'a device driver'], 'C', 'Linux and LibreOffice are open source.', 1],
    ],

    'SHS 1: Computer networking' => [
        ['A network covering a single school building is a', ['GAN', 'WAN', 'MAN', 'LAN'], 'D', 'A Local Area Network covers a small area; the internet is a WAN.', 1],
        ['Which device connects different networks and directs data between them?', ['keyboard', 'router', 'monitor', 'scanner'], 'B', 'A home router links the local network to the internet.', 1],
        ['Network speed (bandwidth) is commonly measured in', ['volts', 'grams', 'bits per second', 'pixels'], 'C', 'For example, 100 Mbps.', 1],
        ['In a client–server network,', ['clients never send requests', 'all computers are equal', 'there is no network', 'a central server provides services to client computers'], 'D', 'In a peer-to-peer network every computer has equal status.', 2],
    ],

    'SHS 1: Computational thinking and programming logic' => [
        ['Breaking a big problem into smaller parts is', ['abstraction', 'decomposition', 'compilation', 'encryption'], 'B', 'Each smaller part is easier to solve.', 1],
        ['Ignoring unnecessary detail and focusing on what matters is', ['iteration', 'decomposition', 'abstraction', 'debugging'], 'C', 'A map leaves out details that do not help you find your way.', 2],
        ['A table used to follow the values of variables step by step through an algorithm is a', ['hash table', 'truth table', 'flowchart', 'trace table'], 'D', 'Trace tables help find logic errors.', 2],
        ['Finding similarities between problems so one solution can be reused is', ['decomposition', 'pattern recognition', 'compression', 'sorting'], 'B', 'It is one of the four pillars of computational thinking.', 1],
    ],

    'SHS 1: Web development' => [
        ['In HTML, which tag creates a hyperlink?', ['<img>', '<p>', '<a>', '<h1>'], 'C', 'For example: <a href="https://example.com">Visit</a>.', 1],
        ['Which language is mainly used to style the appearance of web pages?', ['Python', 'HTML', 'SQL', 'CSS'], 'D', 'HTML gives structure; CSS gives colours, fonts and layout.', 1],
        ['HTTPS differs from HTTP because it', ['is slower always', 'encrypts the data sent between browser and server', 'does not use the internet', 'only sends images'], 'B', 'A padlock in the address bar shows HTTPS is in use.', 2],
        ['Which HTML tag displays an image?', ['<ul>', '<a>', '<img>', '<table>'], 'C', 'The src attribute gives the image file.', 1],
    ],

    'SHS 2: Boolean algebra' => [
        ['Simplify the Boolean expression A + A.', ['0', '2A', '1', 'A'], 'D', 'The idempotent law: A OR A = A.', 2],
        ['What is A · 1 equal to?', ['1', 'A', '0', 'Ā'], 'B', 'The identity law for AND.', 2],
        ['What is A + 1 equal to?', ['0', 'A', '1', 'Ā'], 'C', 'Anything OR 1 is 1.', 2],
        ['Simplify A·B + A·B̄.', ['1', 'B', 'A·B', 'A'], 'D', 'A·B + A·B̄ = A(B + B̄) = A × 1 = A.', 3],
    ],

    'SHS 2: Barcodes and input technologies' => [
        ['A QR code differs from a traditional barcode because it', ['can only store prices', 'stores data in two dimensions', 'needs no scanner', 'is printed in colour only'], 'B', 'QR codes hold much more data, such as web links.', 2],
        ['Tags that are read by radio waves, for example on goods in a warehouse, use', ['OMR', 'OCR', 'RFID', 'MICR'], 'C', 'Radio-frequency identification works without a direct line of sight.', 2],
        ['Reading the marks on multiple-choice answer sheets automatically uses', ['MICR', 'RFID', 'voice recognition', 'optical mark recognition (OMR)'], 'D', 'WAEC objective answer sheets are read this way.', 2],
    ],

    'SHS 2: Network topologies' => [
        ['In a full mesh network of 5 computers, how many links are needed?', ['5', '10', '20', '25'], 'B', 'Links = n(n − 1) ÷ 2 = 5 × 4 ÷ 2 = 10.', 3],
        ['A bus topology needs terminators at the ends of the cable to', ['store data', 'power the network', 'stop signals bouncing back', 'speed up the router'], 'C', 'Reflected signals would cause collisions.', 3],
        ['If the central switch in a star network fails,', ['the network gets faster', 'only one computer stops', 'nothing happens', 'the whole network stops working'], 'D', 'The central device is a single point of failure.', 2],
    ],

    'SHS 2: Main memory and data structures' => [
        ['Small, very fast memory between the CPU and main memory is', ['virtual memory', 'cache memory', 'ROM', 'a hard disk'], 'B', 'It holds frequently used data and instructions.', 2],
        ['Using part of the hard disk as if it were extra RAM is', ['ROM', 'cache memory', 'virtual memory', 'flash memory'], 'C', 'It allows bigger programs to run but is slower than RAM.', 2],
        ['A data structure that stores items of the same type under one name, accessed by index, is', ['a loop', 'a record', 'a file', 'an array'], 'D', 'For example, scores[0], scores[1] and so on.', 1],
    ],

    'SHS 2: Programming in Python' => [
        ['What does len("Ghana") return in Python?', ['6', '5', '4', '"Ghana"'], 'B', 'len counts the characters in the string.', 1],
        ['If word = "computer", what is word[0:3]?', ['"omp"', '"comp"', '"com"', '"co"'], 'C', 'Slicing includes the start index and stops before the end index.', 2],
        ['Which keyword defines a function in Python?', ['define', 'func', 'function', 'def'], 'D', 'For example: def greet(name):', 1],
        ['What is printed by: x = 1; while x < 10: x = x * 2; then print(x)?', ['8', '16', '10', '2'], 'B', 'x goes 1 → 2 → 4 → 8 → 16; the loop stops when x is no longer less than 10.', 3],
        ['Which method adds an item to the end of a Python list?', ['push()', 'add()', 'append()', 'insert_end()'], 'C', 'For example: names.append("Esi").', 1],
    ],

    'SHS 3: System software' => [
        ['Which is a function of an operating system?', ['designing websites', 'editing photos', 'writing letters', 'managing memory and running programs'], 'D', 'The OS also manages files, devices and security.', 1],
        ['A small program that lets the operating system communicate with a printer is a', ['device driver', 'compiler', 'virus', 'spreadsheet'], 'A', 'Each piece of hardware needs a suitable driver.', 1],
        ['A signal that tells the CPU to stop what it is doing and deal with an event is an', ['interrupt', 'algorithm', 'array', 'operand'], 'A', 'For example, a key press or a printer running out of paper.', 2],
        ['The core part of an operating system that controls the hardware is the', ['kernel', 'shell', 'desktop', 'browser'], 'A', 'The shell is the interface between the user and the kernel.', 2],
    ],

    'SHS 3: Network design and implementation' => [
        ['An IPv4 address is made up of', ['32 bits', '128 bits', '8 bits', '64 bits'], 'A', 'It is usually written as four numbers from 0 to 255, such as 192.168.1.10.', 2],
        ['Which network service automatically gives devices an IP address?', ['DHCP', 'DNS', 'FTP', 'SMTP'], 'A', 'The Dynamic Host Configuration Protocol assigns addresses as devices join.', 2],
        ['Dividing a large network into smaller logical networks is', ['subnetting', 'encrypting', 'formatting', 'compiling'], 'A', 'It improves performance and security.', 3],
    ],

    'SHS 3: Mass storage and artificial intelligence' => [
        ['Optical discs such as DVDs are read using', ['a laser', 'a magnet', 'a flash chip', 'a punch card'], 'A', 'The laser detects pits and lands on the disc surface.', 1],
        ['Machine learning in which a model is trained on examples that already have the correct answers is', ['supervised learning', 'unsupervised learning', 'reinforcement learning', 'manual programming'], 'A', 'For example, photos labelled "cat" or "dog".', 3],
        ['The Turing test asks whether', ['a machine\'s responses can be told apart from a human\'s', 'a computer can be switched on', 'a program has bugs', 'a network is secure'], 'A', 'Alan Turing proposed it in 1950.', 2],
        ['Compared with a hard disk drive, a solid-state drive is', ['faster and has no moving parts', 'always cheaper per gigabyte', 'magnetic', 'larger and heavier'], 'A', 'SSDs use flash memory.', 2],
    ],

    'SHS 3: Web technologies and databases' => [
        ['In SQL, which clause filters the rows returned by a query?', ['WHERE', 'FROM', 'ORDER BY', 'SELECT'], 'A', 'For example: SELECT name FROM students WHERE form = 3.', 2],
        ['Organising database tables to reduce repeated data is', ['normalisation', 'encryption', 'compilation', 'fragmentation'], 'A', 'It prevents inconsistent data.', 3],
        ['Software used to create and manage databases, such as MySQL, is a', ['database management system (DBMS)', 'web browser', 'compiler', 'spreadsheet'], 'A', 'A DBMS stores, retrieves and secures data.', 1],
        ['A web page that adjusts its layout to fit phones, tablets and computers uses', ['responsive design', 'fixed design', 'printed design', 'static images only'], 'A', 'CSS media queries help pages respond to screen size.', 2],
    ],
];
