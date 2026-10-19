<?php

/*
| SHS Robotics (2023 curriculum STEM elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Robotics curriculum and the sections
| of the Ministry of Education Year 1 and Year 2 learner materials. The learner materials are copyright, so they
| were used only as a guide to what is taught: every stem, option and explanation here is original.
| Arduino Uno is used for the microcontroller questions.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'What a robot is' => [
        ['Which best describes a robot?', ['a programmable machine that senses, decides and acts', 'any electric fan', 'a computer game', 'a type of battery'], 'A', 'A robot takes in information from sensors, processes it, and acts on its surroundings with actuators.', 1],
        ['A robot that can carry out its task without a person controlling it moment by moment is', ['remote-controlled', 'manual', 'tethered', 'autonomous'], 'D', 'An autonomous robot makes its own decisions from sensor data and its program.', 1],
        ['Asimov\'s First Law of Robotics says that a robot must not', ['injure a human being or, through inaction, allow a human being to come to harm', 'disobey any machine', 'run out of power', 'learn new tasks'], 'A', 'Isaac Asimov\'s laws are fictional, but they are often used to start discussions about robot safety and ethics.', 2],
        ['Which task is a common use of industrial robots?', ['marking exam scripts by hand', 'teaching history lessons', 'welding car bodies on an assembly line', 'farming by hand'], 'C', 'Robots do repetitive, precise or dangerous jobs such as welding and painting in factories.', 1],
    ],

    'Parts of a robot' => [
        ['The "brain" of a robot, which processes inputs and decides on outputs, is the', ['actuator', 'controller (microcontroller)', 'chassis', 'end effector'], 'B', 'The controller runs the program that links sensor inputs to actuator outputs.', 1],
        ['A part of a robot that produces movement, such as a motor, is', ['a sensor', 'a controller', 'a battery', 'an actuator'], 'D', 'Actuators turn energy into motion: motors, servos, pistons and so on.', 1],
        ['The tool at the end of a robotic arm, such as a gripper, is the', ['base', 'joint', 'encoder', 'end effector'], 'D', 'The end effector is what the arm uses to do its job: a gripper, welding torch, suction cup and so on.', 2],
        ['A robot arm has 6 joints, each giving one independent movement. How many degrees of freedom does it have?', ['3', '6', '12', '1'], 'B', 'Each independent joint motion adds one degree of freedom, so 6 in total.', 2],
    ],

    'Sensors' => [
        ['An ultrasonic sensor measures', ['distance, using sound echoes', 'colour', 'temperature', 'light intensity'], 'A', 'It sends out a high-pitched pulse and times how long the echo takes to return.', 1],
        ['An LDR (light-dependent resistor) senses', ['sound', 'light', 'pressure', 'humidity'], 'B', 'The resistance of an LDR falls as more light falls on it.', 1],
        ['A line-following robot usually detects a dark line on a light floor with', ['microphones', 'infrared (IR) reflectance sensors', 'thermometers', 'GPS receivers'], 'B', 'Light floors reflect more infrared than a dark line, so the sensor readings show where the line is.', 2],
        ['An ultrasonic pulse returns to the sensor after 0.002 s. If sound travels at 340 m/s, how far away is the obstacle?', ['0.68 m', '0.17 m', '0.34 m', '680 m'], 'C', 'The sound travels 340 × 0.002 = 0.68 m there and back, so the obstacle is 0.68 ÷ 2 = 0.34 m away.', 3],
        ['A rotary encoder on a wheel is used to measure', ['the colour of the floor', 'how far or how fast the wheel has turned', 'air pressure', 'battery temperature'], 'B', 'The encoder counts pulses as the wheel turns, giving distance and speed.', 2],
        ['A robot that uses a camera to recognise objects is using', ['pneumatics', 'hydraulics', 'computer vision', 'soldering'], 'C', 'Computer vision processes camera images to identify objects, often with machine learning.', 1],
    ],

    'Motors and power' => [
        ['A servo motor is the best choice when a robot needs', ['very fast continuous spinning only', 'to measure light', 'precise control of angle or position', 'to store data'], 'C', 'A standard servo turns to a set angle and holds it, using internal feedback.', 2],
        ['A stepper motor rotates in', ['fixed angular steps', 'random directions', 'only one full turn', 'a straight line'], 'A', 'Each electrical pulse moves a stepper motor by one step, so its position can be controlled by counting pulses.', 2],
        ['A stepper motor has a step angle of 1.8°. How many steps make one full revolution?', ['180', '360', '200', '100'], 'C', '360° ÷ 1.8° = 200 steps.', 2],
        ['An H-bridge circuit is used to', ['measure distance', 'store a program', 'charge a battery', 'reverse the direction of a DC motor'], 'D', 'An H-bridge switches the direction of current through the motor, so it can run forwards or backwards.', 3],
        ['PWM (pulse-width modulation) is often used to', ['control the speed of a DC motor', 'measure temperature', 'read a sensor\'s colour', 'connect to Wi-Fi'], 'A', 'Switching the power on and off very fast and changing the on-time changes the average voltage, and so the motor\'s speed.', 3],
        ['A motor drives a robot wheel through a 3 : 1 gear reduction. The wheel turns', ['3 times slower, with more torque', '3 times faster, with more torque', 'at the same speed', 'backwards only'], 'A', 'A reduction gear trades speed for torque: the wheel turns a third as fast with about three times the torque, less losses.', 3],
        ['A robot\'s battery is rated 2,000 mAh. If the robot draws a steady 500 mA, roughly how long will it run?', ['0.25 hours', '1 hour', '2,500 hours', '4 hours'], 'D', 'Time = capacity ÷ current = 2,000 mAh ÷ 500 mA = 4 hours (in practice a little less).', 2],
        ['A robot wheel has a diameter of 7 cm. Roughly how far does the robot move in one turn of the wheel? (Take π = 22/7.)', ['7 cm', '14 cm', '22 cm', '44 cm'], 'C', 'Distance per turn = circumference = πd = 22/7 × 7 = 22 cm.', 2],
    ],

    'Control' => [
        ['A control system that uses sensor feedback to correct its output is', ['an open-loop system', 'a manual system', 'a closed-loop system', 'a random system'], 'C', 'A closed-loop system compares what is happening with what should happen and adjusts.', 2],
        ['A toaster that runs for a set time without checking how brown the bread is, is an example of', ['open-loop control', 'closed-loop control', 'PID control', 'feedback control'], 'A', 'It has no feedback: it acts only on its setting, whatever the result.', 2],
        ['In PID control, the letters stand for', ['Power, Input, Drive', 'Proportional, Integral, Derivative', 'Position, Inertia, Distance', 'Program, Interface, Device'], 'B', 'A PID controller combines three terms based on the error to give smooth, accurate control.', 3],
        ['Working out where a robot arm\'s gripper will be from its joint angles is', ['inverse kinematics', 'forward kinematics', 'dynamics', 'calibration'], 'B', 'Forward kinematics goes from joint angles to position. Inverse kinematics finds the joint angles needed to reach a position.', 4],
    ],

    'Programming' => [
        ['On an Arduino, what does digitalWrite(13, HIGH) do?', ['reads the value of pin 13', 'turns off the board', 'sets pin 13 as an input', 'sets pin 13 to a high voltage (turns it on)'], 'D', 'digitalWrite sets an output pin HIGH (about 5 V on an Uno) or LOW (0 V).', 2],
        ['In an Arduino sketch, the code inside loop() runs', ['once, when the board starts', 'over and over again while the board has power', 'only when a button is pressed', 'once a day'], 'B', 'setup() runs once at the start. loop() then repeats for as long as the board is on.', 1],
        ['On an Arduino Uno, analogRead() returns values in the range', ['0 to 255', '0 to 100', '0 to 1023', '−512 to 512'], 'C', 'The Uno has a 10-bit analogue-to-digital converter: 2¹⁰ = 1,024 values, from 0 to 1023.', 3],
        ['In a flowchart, a decision (a yes/no question) is drawn as a', ['rectangle', 'diamond', 'oval', 'parallelogram'], 'B', 'Diamonds are decisions, rectangles are processes, ovals are start/stop and parallelograms are input/output.', 1],
        ['What path does a robot follow with this program? REPEAT 4 TIMES: move forward 10 cm, turn right 90°', ['a square', 'a triangle', 'a straight line 40 cm long', 'it spins on the spot'], 'A', 'Four equal sides with four 90° turns make a square and bring the robot back to its start.', 2],
    ],

    'Safety' => [
        ['Before working on a robot\'s moving parts, you should first', ['increase the motor speed', 'remove the safety guard while it runs', 'switch it off and disconnect its power', 'touch the motor while it runs'], 'C', 'Isolating the power stops the robot from moving unexpectedly and causing injury.', 1],
    ],

    'SHS 1: Industrial revolutions, robots at work, roboethics' => [
        ['Which development is most closely linked with the First Industrial Revolution (Industry 1.0)?', ['steam-powered machines and mechanisation', 'the Internet of Things', 'programmable logic controllers', 'cloud computing'], 'A', 'From about 1760, water and steam power replaced much hand work in textile mills and workshops.', 1],
        ['Electric power and moving assembly lines for mass production are features of which industrial revolution?', ['the first', 'the second', 'the third', 'the fourth'], 'B', 'The Second Industrial Revolution, from the late 1800s, brought electricity, steel and assembly-line mass production.', 2],
        ['Electronics, computers, programmable logic controllers and the first industrial robots are linked with the', ['First Industrial Revolution', 'Second Industrial Revolution', 'Third Industrial Revolution', 'Fourth Industrial Revolution'], 'C', 'Industry 3.0 used electronics and information technology to automate production.', 2],
        ['Cyber-physical systems, the Internet of Things and artificial intelligence are features of', ['Industry 1.0', 'Industry 2.0', 'Industry 3.0', 'Industry 4.0'], 'D', 'Industry 4.0 connects machines, sensors and data so that systems can monitor and adjust themselves.', 1],
        ['Which list gives four organisational performance indicators often used to judge the effect of robots at work?', ['speed, colour, size and weight', 'price, quality, flexibility and innovation', 'profit, tax, rent and wages', 'safety, noise, heat and light'], 'B', 'Robots can lower unit price, make quality more consistent, let a firm switch tasks (flexibility) and open up new products (innovation).', 2],
        ['A factory robot can be reprogrammed in an hour to pack a new product. Which performance indicator does this mainly improve?', ['price', 'quality', 'innovation', 'flexibility'], 'D', 'Flexibility is how quickly an organisation can change what or how it produces.', 2],
        ['Roboethics is mainly concerned with', ['using robots responsibly, safely and fairly', 'oiling robot joints', 'the colour of robots', 'robot battery sizes'], 'A', 'Roboethics looks at questions such as safety, privacy, job losses and who is responsible when a robot causes harm.', 1],
        ['Which of these is an example of a smart home device?', ['a wooden chair', 'a clay pot', 'a thermostat that adjusts the room temperature by itself', 'a hand broom'], 'C', 'Smart devices sense conditions and act on them automatically, often connected to a phone or the internet.', 1],
        ['Which job mainly involves operating, maintaining and repairing robots in a factory?', ['robotics technician', 'patent attorney', 'robot ethicist', 'sales manager'], 'A', 'Technicians keep robots running day to day. Robotics engineers design and develop new systems.', 1],
    ],

    'SHS 1: Robotic systems and control loops' => [
        ['Which subsystem of a robot gathers information about its surroundings?', ['the actuation subsystem', 'the power subsystem', 'the sensing subsystem', 'the mechanical structure'], 'C', 'Sensors are the robot\'s link to its environment. The controller uses their readings to decide what the actuators should do.', 1],
        ['A machine that runs a fixed sequence and cannot sense or respond to changes around it is best described as', ['an autonomous robot', 'a humanoid robot', 'a cobot', 'a non-robotic system'], 'D', 'A true robot senses, decides and acts. A machine that only repeats fixed steps without sensing is not a robot.', 2],
        ['A robot designed to work safely alongside people, sharing their workspace, is called a', ['collaborative robot (cobot)', 'drone', 'gantry crane', 'conveyor'], 'A', 'Cobots have force sensors and speed limits so that they can work next to people without safety cages.', 1],
        ['In a feedback control system, the error signal is', ['the power supplied to the motor', 'the difference between the desired output and the actual output', 'the time the task takes', 'the signal sent to a display'], 'B', 'The controller works to make the error zero: error = set point − measured value.', 2],
        ['In a control system, the reference input is', ['the desired value (set point) that the system aims for', 'noise from the motor', 'the final output', 'a faulty sensor reading'], 'A', 'For a fan speed controller, the reference input might be "1,000 rpm".', 2],
        ['A fire alarm rings when someone breaks the glass and keeps ringing until it is reset. It is usually treated as', ['a PID controller', 'a closed-loop speed controller', 'a non-feedback (open-loop) system', 'a feedback temperature controller'], 'C', 'The alarm does not measure its own output to adjust itself: an input triggers the output and that is all.', 2],
        ['In a finite-state machine (FSM) model of a traffic light, red, amber and green are the', ['inputs', 'states', 'sensors', 'loops'], 'B', 'An FSM is always in one of a fixed set of states and moves between them when conditions are met.', 2],
        ['In a finite-state machine, a change from one state to another is called a', ['loop', 'gain', 'calibration', 'transition'], 'D', 'A transition is triggered by an input or event, such as a timer running out.', 2],
        ['A room temperature controller that measures and adjusts the heating continuously, rather than in separate steps, is an example of', ['a continuous-time system', 'a two-state machine only', 'a non-feedback system', 'a manual system'], 'A', 'In a continuous-time system the signals change smoothly over time and are acted on all the time.', 3],
    ],

    'Sensors and signals' => [
        ['Which sensor would best tell a robot that it has bumped into a wall?', ['a light-dependent resistor', 'a microphone', 'a touch (bump) switch', 'a GPS receiver'], 'C', 'A bump switch closes when it is pressed by contact with an object.', 1],
        ['A thermistor is a sensor whose resistance changes with', ['light', 'temperature', 'sound', 'magnetism'], 'B', 'Thermistors are used to measure temperature in robots, phones and appliances.', 1],
        ['A temperature sensor reads 4 °C in melting ice (0 °C) and 96 °C in boiling water (100 °C). If its response is a straight line, what is the true temperature when it reads 50 °C?', ['46 °C', '54 °C', '52 °C', '50 °C'], 'D', 'True value = (reading − 4) × 100 ÷ (96 − 4) = 46 × 100 ÷ 92 = 50 °C.', 4],
        ['Two-point calibration is suitable for a sensor whose output is', ['linear (a straight-line response)', 'completely random', 'always zero', 'digital only'], 'A', 'Two known points fix a straight line, so the method only works when the sensor responds linearly.', 3],
        ['An active sensor differs from a passive sensor because it', ['needs no power at all', 'can only measure temperature', 'sends out its own energy, such as sound or infrared, and detects what returns', 'never needs calibrating'], 'C', 'An ultrasonic sensor is active: it emits a pulse. A passive infrared (PIR) sensor only detects heat given off by objects.', 3],
        ['Which type of sensor is best for detecting whether a button has been pressed?', ['an analogue temperature sensor', 'a digital (on/off) sensor', 'a GPS receiver', 'a microphone'], 'B', 'A button has only two states, pressed or not, so a digital input is enough.', 1],
        ['Converting an analogue signal into a digital one involves sampling and', ['amplification only', 'soldering', 'calibration only', 'quantisation'], 'D', 'Sampling measures the signal at regular times. Quantisation rounds each sample to one of a fixed set of levels.', 3],
        ['A sensor samples a water level of 74.78 cm, which is quantised to 70 cm. What is the percentage error?', ['6.4%', '4.8%', '6.8%', '93.6%'], 'A', 'Error = 74.78 − 70 = 4.78 cm. Percentage error = 4.78 ÷ 74.78 × 100% ≈ 6.4%. Divide by the true (sampled) value.', 4],
    ],

    'Electronics and digital logic' => [
        ['Which of these is an active electronic component?', ['resistor', 'capacitor', 'transistor', 'inductor'], 'C', 'A transistor can amplify or switch a signal, so it is active. Resistors, capacitors and inductors are passive.', 2],
        ['In a circuit, a capacitor', ['lets current flow in one direction only', 'blocks steady direct current but lets alternating current pass', 'gives out light', 'amplifies signals'], 'B', 'Once charged, a capacitor stops DC flowing. AC keeps charging and discharging it, so it passes.', 3],
        ['A solderless breadboard is mainly used to', ['build and test circuits quickly without soldering', 'store a robot\'s program', 'measure distance', 'charge batteries'], 'A', 'Parts push into the holes and can be moved easily, which suits prototypes.', 1],
        ['A potentiometer used to dim a lamp works as a', ['diode', 'fuse', 'switch only', 'variable resistor'], 'D', 'Turning the knob changes the resistance, which changes the current and so the brightness.', 1],
        ['In analogue circuits, an operational amplifier (op-amp) is mainly used to', ['store data permanently', 'amplify signals', 'convert AC to DC only', 'count pulses'], 'B', 'An op-amp makes a small input voltage much larger, for example to boost a weak sensor signal.', 2],
        ['A resistor has the colour bands brown, black and red. What is its resistance?', ['102 Ω', '10 kΩ', '1 kΩ', '120 Ω'], 'C', 'Brown = 1, black = 0, and red as the multiplier = × 100. So 10 × 100 = 1,000 Ω = 1 kΩ.', 3],
        ['Before measuring a resistor with a multimeter, you should', ['switch off the power and, if possible, disconnect one leg of the resistor', 'turn up the supply voltage', 'set the meter to the current (A) range', 'hold both probe tips with your fingers'], 'A', 'Resistance must be measured with no power, and other parts in the circuit can upset the reading. Your body\'s resistance can too.', 3],
        ['A dull, grainy solder joint that may not conduct properly is called a', ['bridge', 'pad', 'via', 'cold joint'], 'D', 'A cold joint forms when the solder or parts were not hot enough. A good joint is smooth and shiny.', 2],
        ['Simplify the Boolean expression A + A·B.', ['B', 'A', 'A·B', '1'], 'B', 'A + A·B = A(1 + B) = A × 1 = A (the absorption law).', 3],
        ['What is the value of A·Ā (A AND NOT A)?', ['1', 'A', '0', 'Ā'], 'C', 'A and NOT A can never both be 1, so the AND is always 0.', 2],
        ['By De Morgan\'s theorem, NOT(A·B) equals', ['Ā·B̄', 'A + B', 'A·B', 'Ā + B̄'], 'D', 'Breaking the bar changes AND to OR: NOT(A·B) = Ā + B̄. A NAND gate is the same as an OR gate with inverted inputs.', 3],
        ['A Karnaugh map is used to', ['measure resistance', 'draw robot parts in 3D', 'simplify Boolean expressions', 'tune a PID controller'], 'C', 'Grouping neighbouring 1s on a K-map gives a simpler expression, so the circuit needs fewer gates.', 2],
        ['How many rows does the truth table of a logic circuit with 3 inputs have?', ['3', '8', '6', '9'], 'B', 'Each input can be 0 or 1, so there are 2³ = 8 combinations.', 2],
    ],

    'Design tools and making parts' => [
        ['In the Arduino IDE, the tick (checkmark) button', ['uploads the code to the board', 'verifies (compiles) the code and checks for errors', 'opens the Serial Monitor', 'saves the sketch'], 'B', 'Verify compiles the sketch without uploading it. The arrow button compiles and uploads.', 1],
        ['The Arduino IDE\'s Serial Monitor is used to', ['view and send text data between the computer and the board', 'draw circuit diagrams', '3D print parts', 'simulate gears'], 'A', 'Printing sensor values to the Serial Monitor is a quick way to check and debug a program.', 1],
        ['FDM 3D printing builds parts by', ['curing liquid resin with light', 'cutting a block of metal', 'melting plastic filament and laying it down layer by layer', 'pouring metal into a mould'], 'C', 'Fused deposition modelling pushes melted filament such as PLA through a hot nozzle. Resin printers cure liquid resin with UV light.', 2],
        ['The corners of a 3D print lift off the print bed as the plastic cools. This fault is called', ['stringing', 'under-extrusion', 'layer shift', 'warping'], 'D', 'Warping comes from uneven shrinking as the plastic cools. A heated bed, better bed adhesion or a brim helps.', 2],
        ['Which type of manufacturing removes material from a solid block, as a CNC mill does?', ['additive', 'subtractive', 'formative', 'assembly'], 'B', 'Subtractive cuts material away, additive builds it up (3D printing) and formative shapes it (moulding, bending).', 1],
    ],

    'Algorithms and programming' => [
        ['In a flowchart, the start and the end of an algorithm are shown with', ['a diamond', 'a parallelogram', 'an oval (terminator)', 'a rectangle'], 'C', 'The rounded oval marks where the algorithm begins and ends.', 1],
        ['In the input–process–output model of a line-following robot, the light sensor readings are the', ['inputs', 'processes', 'outputs', 'loops'], 'A', 'The sensor readings go in, the program decides (process), and the motor speeds come out.', 1],
        ['Pseudocode is', ['a programming language that runs directly on an Arduino', 'a type of flowchart symbol', 'a 3D printing file', 'a plain-language outline of an algorithm, written like code'], 'D', 'Pseudocode sets out the steps in code-like English, so it can be planned before choosing a real language.', 1],
        ['A robot must keep driving forward until its distance sensor reads less than 10 cm. Which block-based structure fits best?', ['an If-Then block', 'a Repeat Until loop', 'a single Move block', 'a Sound block'], 'B', 'Repeat Until runs the move again and again and stops when the condition (distance < 10 cm) becomes true.', 2],
        ['An If-Then-Else block differs from an If-Then block because it', ['also gives an action for when the condition is false', 'repeats forever', 'cannot use sensors', 'only works with sound'], 'A', 'If-Then does something only when the condition is true. If-Then-Else chooses between two actions.', 1],
    ],

    'Construction, mechanisms and motion' => [
        ['Which change makes a robot less likely to tip over?', ['raising its heaviest parts', 'making its base narrower', 'lowering its centre of gravity and widening its base', 'adding weight to the top'], 'C', 'A robot stays upright while its centre of gravity is above its base. A low centre of gravity and a wide base give the most margin.', 2],
        ['Which gear pair turns the direction of rotation through 90°?', ['spur gears on parallel shafts', 'bevel gears', 'a rack and pinion', 'an idler gear'], 'B', 'Bevel gears have cone-shaped teeth that mesh at an angle, usually 90°.', 2],
        ['A rack and pinion converts', ['linear motion into heat', 'sound into motion', 'light into rotation', 'rotary motion into linear motion'], 'D', 'As the round pinion turns, it drives the straight toothed rack along, as in a sliding gate.', 1],
        ['A worm gear is often used in a robot arm joint because it', ['gives a large speed reduction and resists being turned back by the load', 'spins the output faster than the motor', 'changes motion into sound', 'has no teeth'], 'A', 'One turn of the worm moves the wheel by one tooth, and most worm drives cannot be back-driven, so the arm holds its position.', 3],
        ['For moving over rough, uneven ground, which drive is usually better than small wheels?', ['smaller wheels', 'a single castor', 'legs or tracks', 'skids'], 'C', 'Tracks spread the weight and grip over bumps, and legs can step over obstacles.', 1],
        ['A Geneva drive turns continuous rotation into', ['continuous linear motion', 'intermittent (step-by-step) rotation', 'heat', 'an electrical signal'], 'B', 'The driven wheel turns a fixed step, then pauses, once for every turn of the driver. Film projectors and indexing tables use it.', 3],
        ['A cam and follower changes rotary motion into', ['faster rotary motion', 'electrical energy', 'sound', 'reciprocating (up-and-down) motion'], 'D', 'As the shaped cam turns, it pushes the follower up and down.', 2],
        ['A crank and rocker mechanism turns the full rotation of the crank into', ['a back-and-forth swinging motion of the rocker', 'the straight-line motion of a rack', 'faster continuous rotation', 'an electrical pulse'], 'A', 'In this four-bar linkage the short crank turns all the way round while the longer rocker swings to and fro.', 3],
        ['A robot wheel of radius 0.2 m spins at 5 rad/s. What is the robot\'s linear speed?', ['25 m/s', '0.04 m/s', '1 m/s', '5.2 m/s'], 'C', 'v = ωr = 5 × 0.2 = 1 m/s.', 3],
        ['A wheel starts at 4 rad/s and has an angular acceleration of 1.5 rad/s². What is its angular velocity after 6 s?', ['9 rad/s', '13 rad/s', '10 rad/s', '31 rad/s'], 'B', 'ω = ω₀ + αt = 4 + 1.5 × 6 = 4 + 9 = 13 rad/s.', 3],
        ['A robot moves 12 m east in 8 s. What is its average velocity?', ['96 m/s east', '0.67 m/s east', '1.5 m/s west', '1.5 m/s east'], 'D', 'Average velocity = displacement ÷ time = 12 ÷ 8 = 1.5 m/s, in the direction of the displacement (east).', 2],
        ['A robot must travel 1.5 m and its wheels have a diameter of 6 cm. Roughly how many turns must each wheel make? (Take π = 3.14.)', ['25', '16', '8', '3'], 'C', 'Circumference = 3.14 × 6 = 18.84 cm. Turns = 150 ÷ 18.84 ≈ 8.', 3],
        ['A robot with larger wheels travels further for each wheel turn than one with smaller wheels because', ['each turn covers one circumference, and a larger wheel has a larger circumference', 'larger wheels spin faster', 'larger wheels are lighter', 'smaller wheels slip more'], 'A', 'Distance per turn = πd, so it grows with the wheel diameter.', 2],
    ],

    'SHS 2: PID control and troubleshooting' => [
        ['A line-following robot keeps over-correcting and wobbling from side to side. Which PID change is most likely to help?', ['increase Kp a lot', 'reduce Kp, or increase Kd', 'remove the sensor', 'drive faster'], 'B', 'Too much proportional gain makes the robot over-react. Lowering Kp, or adding derivative action to damp quick changes, smooths it.', 4],
        ['In a PID controller, the integral (I) term mainly helps to', ['make the robot lighter', 'measure distance', 'remove a small error that remains over time', 'reduce battery use'], 'C', 'The integral term adds up past error, so even a small lasting error builds up a correction until it is removed.', 4],
        ['When troubleshooting a robot, problems are usually grouped as', ['colour, size and shape problems', 'weather, time and place problems', 'price, tax and profit problems', 'hardware, software (coding) and algorithm (design) problems'], 'D', 'Checking each group in turn (parts and wiring, the code, then the logic of the plan) narrows down the cause.', 2],
    ],
];
