<?php

/*
| JHS Computing: BECE-style objective questions written for Otisam, not copied from WAEC papers.
| Strands: computer systems, productivity software, the internet and safety, computational thinking.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Computer systems
    ['Which of the following is an input device?', ['monitor', 'printer', 'keyboard', 'speaker'], 'C', 'A keyboard sends data into the computer. Monitors, printers and speakers are output devices.', 1],
    ['The "brain" of the computer that processes instructions is the', ['RAM', 'CPU', 'hard disk', 'monitor'], 'B', 'The Central Processing Unit carries out instructions and calculations.', 1],
    ['Which of the following is a storage device?', ['flash drive', 'mouse', 'scanner', 'microphone'], 'A', 'A flash (USB) drive stores data. The others are input devices.', 1],
    ['1 kilobyte (KB) is equal to', ['100 bytes', '1000 bits', '1024 bytes', '1024 kilobits'], 'C', 'In computing, 1 KB = 1024 bytes (2¹⁰).', 2],
    ['Which unit is the largest?', ['megabyte', 'kilobyte', 'gigabyte', 'terabyte'], 'D', 'Order from small to large: kilobyte, megabyte, gigabyte, terabyte.', 2],
    ['Programs that control the computer\'s hardware and run other programs are called', ['application software', 'system software (operating systems)', 'utility hardware', 'firmware cables'], 'B', 'An operating system such as Windows, macOS or Linux is system software.', 2],
    ['Which of the following is an example of an operating system?', ['Microsoft Word', 'Windows', 'Google Chrome', 'Microsoft Excel'], 'B', 'Windows is an operating system; the others are application programs.', 1],
    ['A device that protects a computer from sudden power cuts by supplying power for a short time is', ['a UPS', 'a modem', 'a router', 'a scanner'], 'A', 'An Uninterruptible Power Supply (UPS) gives battery power so you can save work and shut down safely.', 2],
    ['Which port is most commonly used to connect a flash drive to a computer?', ['VGA', 'HDMI', 'USB', 'Ethernet'], 'C', 'Flash drives use the USB (Universal Serial Bus) port.', 1],

    // Productivity software
    ['Which program is best for typing a letter?', ['Microsoft Excel', 'Microsoft Word', 'Microsoft PowerPoint', 'Paint'], 'B', 'Word is a word processor for creating text documents.', 1],
    ['In a spreadsheet, the intersection of a row and a column is called a', ['cell', 'field', 'record', 'table'], 'A', 'A cell, such as B3, is where a column and a row meet.', 1],
    ['Which spreadsheet formula adds the values in cells A1 to A5?', ['=ADD(A1:A5)', '=SUM(A1:A5)', '=TOTAL(A1-A5)', '=A1+A5'], 'B', 'The SUM function adds a range: =SUM(A1:A5).', 2],
    ['The keyboard shortcut Ctrl + C is used to', ['cut', 'copy', 'close', 'centre'], 'B', 'Ctrl + C copies; Ctrl + X cuts and Ctrl + V pastes.', 1],
    ['The keyboard shortcut used to undo the last action is', ['Ctrl + Y', 'Ctrl + Z', 'Ctrl + U', 'Ctrl + S'], 'B', 'Ctrl + Z undoes; Ctrl + Y redoes; Ctrl + S saves; Ctrl + U underlines.', 1],
    ['Which program is mainly used to create slide presentations?', ['Microsoft Access', 'Microsoft PowerPoint', 'Notepad', 'Microsoft Excel'], 'B', 'PowerPoint creates slides for presentations.', 1],
    ['In a word processor, making text appear thicker and darker is called', ['italic', 'underline', 'bold', 'superscript'], 'C', 'Bold makes text thicker and darker.', 1],

    // Internet, communication and safety
    ['A program used to view web pages is called a', ['search engine', 'web browser', 'website', 'web server'], 'B', 'A web browser such as Chrome, Firefox or Edge displays web pages.', 1],
    ['Which of the following is a search engine?', ['Google', 'Windows', 'Excel', 'WhatsApp'], 'A', 'Google Search finds information on the web.', 1],
    ['What does "URL" stand for?', ['Uniform Resource Locator', 'Universal Reading Link', 'United Resource Line', 'User Register List'], 'A', 'A URL is the address of a resource on the web, such as https://example.com.', 2],
    ['In the e-mail address kofi@example.com, "example.com" is the', ['user name', 'subject', 'password', 'domain name'], 'D', 'The part after @ is the domain of the mail provider or organisation.', 2],
    ['An e-mail that tricks you into giving your password or bank details is called', ['spam', 'backup', 'cookies', 'phishing'], 'D', 'Phishing messages pretend to come from a trusted source to steal personal information.', 2],
    ['A program written to damage a computer or steal data is called', ['malware', 'freeware', 'firmware', 'shareware'], 'A', 'Malware ("malicious software") includes viruses, worms and spyware.', 2],
    ['Which of the following is the safest action when you receive a message from a stranger online asking to meet you?', ['go and meet them alone', 'send them your home address', 'tell a parent or teacher and do not reply', 'share your photos with them'], 'C', 'Never share personal details or meet strangers from the internet; tell a trusted adult.', 1],
    ['Which of these is the best example of a strong password?', ['12345678', 'password', 'Kofi2010', 'g7#Rt!q2Vm'], 'D', 'A strong password is long and mixes upper and lower case letters, numbers and symbols.', 1],
    ['A device that connects a home or school network to the internet is a', ['router', 'printer', 'projector', 'scanner'], 'A', 'A router directs data between the local network and the internet.', 2],
    ['A network that covers a small area such as a school computer lab is a', ['WAN', 'internet', 'MAN', 'LAN'], 'D', 'A Local Area Network (LAN) covers a small area such as a building.', 2],

    // Computational thinking and programming
    ['A step-by-step set of instructions for solving a problem is called', ['an algorithm', 'a database', 'a virus', 'a browser'], 'A', 'An algorithm is a sequence of clear steps for solving a problem.', 1],
    ['In a flowchart, the diamond-shaped symbol is used for', ['start and stop', 'input and output', 'a decision', 'a process'], 'C', 'A diamond shows a decision with yes/no branches.', 2],
    ['In a flowchart, the oval (terminator) symbol represents', ['a decision', 'storage', 'a process', 'start or end'], 'D', 'Ovals mark where the flowchart starts and ends.', 2],
    ['Breaking a big problem into smaller parts that are easier to solve is called', ['decomposition', 'debugging', 'compiling', 'encryption'], 'A', 'Decomposition is a key computational thinking skill.', 2],
    ['Finding and fixing errors in a program is called', ['coding', 'formatting', 'debugging', 'booting'], 'C', 'Debugging means locating and removing errors (bugs).', 1],
    ['Scratch is an example of a', ['operating system', 'spreadsheet program', 'web browser', 'block-based programming language'], 'D', 'Scratch lets learners build programs by snapping blocks together.', 2],
    ['In a program, an instruction that repeats a set of steps is called a', ['comment', 'variable', 'loop', 'input'], 'C', 'A loop repeats steps a number of times or until a condition is met.', 2],
    ['A named storage location in a program whose value can change is called a', ['constant', 'function key', 'loop', 'variable'], 'D', 'A variable holds a value, such as score = 10, that can change while the program runs.', 2],
    ['Writing everything as 0s and 1s is how computers represent data in', ['decimal', 'hexadecimal', 'binary', 'octal'], 'C', 'Computers store data in binary (base two) using 0s and 1s.', 1],
];
