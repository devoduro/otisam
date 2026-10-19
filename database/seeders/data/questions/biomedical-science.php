<?php

/*
| SHS Biomedical Science (2023 curriculum STEM elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Biomedical Science curriculum.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Cells and molecules' => [
        ['The basic unit of life is the', ['tissue', 'organ', 'cell', 'atom'], 'C', 'All living things are made of cells, the smallest units that carry out life processes.', 1],
        ['Which organelle releases energy through aerobic respiration?', ['ribosome', 'mitochondrion', 'nucleus', 'Golgi body'], 'B', 'Mitochondria break down glucose with oxygen to release energy as ATP.', 1],
        ['Enzymes are biological catalysts made of', ['protein', 'fat', 'starch', 'salt'], 'A', 'Enzymes are proteins that speed up reactions in the body without being used up.', 1],
        ['In DNA, adenine always pairs with', ['thymine', 'cytosine', 'guanine', 'uracil'], 'A', 'A pairs with T and C pairs with G. Uracil replaces thymine only in RNA.', 2],
        ['How many chromosomes does a normal human body cell contain?', ['23', '44', '46', '92'], 'C', 'Body cells have 46 chromosomes in 23 pairs. Sperm and egg cells have 23.', 1],
    ],

    'Blood' => [
        ['Red blood cells carry oxygen using', ['haemoglobin', 'antibodies', 'platelets', 'insulin'], 'A', 'Haemoglobin, an iron-containing protein, binds oxygen in the lungs and releases it in the tissues.', 1],
        ['Which blood component helps blood to clot?', ['red blood cells', 'white blood cells', 'plasma water', 'platelets'], 'D', 'Platelets gather at a wound and start the clotting process.', 1],
        ['Which blood group is often called the universal donor for red cells?', ['O negative', 'AB positive', 'A positive', 'B negative'], 'A', 'O negative red cells carry no A, B or RhD antigens, so most recipients will not react against them.', 2],
        ['Which blood group is called the universal recipient?', ['O negative', 'A negative', 'AB positive', 'B positive'], 'C', 'AB positive blood has no anti-A or anti-B antibodies and is RhD positive, so it can receive red cells of any group.', 2],
        ['Anaemia is a condition in which', ['the blood has too little haemoglobin or too few red cells', 'blood sugar is too high', 'only the blood pressure is too low', 'white cells multiply too fast'], 'A', 'Anaemia lowers the blood\'s ability to carry oxygen, causing tiredness and pale skin.', 2],
        ['In a blood pressure reading of 120/80 mmHg, the 120 is the', ['diastolic pressure', 'systolic pressure', 'pulse rate', 'temperature'], 'B', 'The top number is the systolic pressure, when the heart contracts. The bottom number is diastolic, when it relaxes.', 2],
    ],

    'Body systems' => [
        ['The chamber of the heart that pumps oxygenated blood to the whole body is the', ['right atrium', 'right ventricle', 'left atrium', 'left ventricle'], 'D', 'The left ventricle has the thickest muscle wall and pumps blood into the aorta.', 2],
        ['A nurse counts 18 pulse beats in 15 seconds. What is the pulse rate?', ['18 beats/min', '72 beats/min', '33 beats/min', '54 beats/min'], 'B', 'There are four 15-second periods in a minute: 18 × 4 = 72 beats per minute.', 2],
        ['The functional unit of the kidney is the', ['alveolus', 'nephron', 'neurone', 'villus'], 'B', 'Each kidney has about a million nephrons that filter the blood and form urine.', 2],
        ['Cells that carry electrical impulses around the body are', ['erythrocytes', 'osteocytes', 'neurones', 'adipocytes'], 'C', 'Neurones (nerve cells) carry impulses. Erythrocytes are red blood cells, osteocytes bone cells and adipocytes fat cells.', 2],
        ['Insulin is produced by the', ['liver', 'pancreas', 'kidney', 'thyroid gland'], 'B', 'Beta cells in the islets of the pancreas make insulin, which lowers blood glucose.', 1],
        ['Type 1 diabetes results from', ['too much vitamin C', 'a bacterial infection of the lungs', 'too little insulin being produced', 'high iron levels'], 'C', 'In type 1 diabetes the immune system destroys the insulin-making cells of the pancreas.', 2],
        ['Normal human body temperature is about', ['30 °C', '40 °C', '37 °C', '25 °C'], 'C', 'Normal core temperature is about 37 °C. 40 °C would be a high fever.', 1],
    ],

    'Infection and immunity' => [
        ['Malaria is caused by', ['a virus', 'Plasmodium, a protozoan parasite', 'a bacterium', 'a fungus'], 'B', 'Malaria is caused by Plasmodium parasites, which infect liver and red blood cells.', 1],
        ['Malaria parasites are carried by the female', ['Aedes mosquito', 'tsetse fly', 'Anopheles mosquito', 'housefly'], 'C', 'Female Anopheles mosquitoes pass on Plasmodium when they feed on blood.', 1],
        ['Tuberculosis is caused by', ['Plasmodium', 'HIV', 'a worm', 'Mycobacterium tuberculosis, a bacterium'], 'D', 'TB is a bacterial disease that mainly attacks the lungs and spreads through the air.', 2],
        ['HIV mainly attacks', ['red blood cells', 'CD4 T-helper cells', 'platelets', 'bone cells'], 'B', 'HIV destroys CD4 T-helper cells, which weakens the immune system.', 2],
        ['Cholera is spread mainly through', ['mosquito bites', 'contaminated water and food', 'sharing clothes', 'sunlight'], 'B', 'Cholera bacteria pass from faeces into water or food, so clean water and sanitation prevent it.', 1],
        ['Antibodies are produced by', ['red blood cells', 'platelets', 'neurones', 'B lymphocytes'], 'D', 'B lymphocytes (B cells) make antibodies that bind to specific antigens.', 2],
        ['A vaccine works by', ['stimulating the immune system to make memory cells against a pathogen', 'killing all the bacteria in the body', 'replacing lost blood', 'lowering body temperature'], 'A', 'A vaccine exposes the body to a harmless form or part of a pathogen, so it can respond quickly to a real infection later.', 2],
        ['Antibiotics are effective against', ['viruses', 'bacteria', 'all diseases', 'genetic disorders'], 'B', 'Antibiotics kill or stop the growth of bacteria. They do not work on viruses such as those causing colds or flu.', 1],
    ],

    'Genetics' => [
        ['A person with sickle cell disease usually has the genotype', ['AA', 'SS', 'AS', 'AC'], 'B', 'Sickle cell disease is usually SS: two copies of the sickle haemoglobin gene. AS is sickle cell trait (a carrier).', 2],
        ['If both parents have the genotype AS, what is the probability that a child will be SS?', ['1/4', '1/2', '3/4', '0'], 'A', 'The cross AS × AS gives AA, AS, AS and SS, so the chance of SS is 1 in 4.', 3],
    ],

    'Laboratory practice and ethics' => [
        ['A microscope has a ×10 eyepiece and a ×40 objective lens. What is the total magnification?', ['×50', '×30', '×4', '×400'], 'D', 'Total magnification = eyepiece × objective = 10 × 40 = 400.', 2],
        ['Laboratory equipment is sterilised with high-pressure steam in an', ['autoclave', 'incubator', 'centrifuge', 'spectrometer'], 'A', 'An autoclave uses steam at about 121 °C under pressure to kill microorganisms and spores.', 2],
        ['In the laboratory, a centrifuge is used to', ['heat samples', 'measure pH', 'magnify cells', 'separate the parts of a sample by spinning it fast'], 'D', 'Spinning forces denser parts, such as blood cells, to the bottom of the tube.', 2],
        ['Before taking part in a medical study, a person must give', ['informed consent', 'a fee', 'a blood transfusion', 'a vaccine'], 'A', 'Informed consent means the person understands the risks and benefits and agrees freely.', 1],
    ],

    'SHS 1: Biosafety in the laboratory' => [
        ['Laboratories that work with the most dangerous pathogens, such as Ebola virus, are rated', ['Biosafety Level 1', 'Biosafety Level 4', 'Biosafety Level 2', 'Biosafety Level 0'], 'B', 'BSL-4 labs use full-body suits and airlocks.', 2],
        ['Used needles and blades must be thrown away in', ['the sink', 'an ordinary dustbin', 'a puncture-proof sharps container', 'a paper bag'], 'C', 'This prevents needle-stick injuries and infection.', 1],
        ['The international biohazard symbol warns of', ['slippery floors', 'radioactive material', 'electric shock', 'biological materials that may cause infection'], 'D', 'It is placed on containers of infectious waste.', 1],
        ['Which practice best protects a laboratory worker handling blood samples?', ['eating in the laboratory', 'wearing gloves and a lab coat', 'pipetting by mouth', 'touching the face'], 'B', 'Personal protective equipment stops contact with infectious material.', 1],
    ],

    'SHS 2: Careers and professional practice' => [
        ['A professional who designs and maintains medical equipment is a', ['radiographer only', 'pharmacist', 'biomedical engineer', 'dietitian'], 'C', 'Biomedical engineers apply engineering to medicine.', 1],
        ['A professional who tests blood and other samples to help diagnose disease is a', ['physiotherapist', 'surgeon', 'midwife', 'medical laboratory scientist'], 'D', 'Lab results guide doctors\' decisions.', 1],
        ['Keeping a patient\'s test results private, sharing them only with those who need them, is', ['quality control', 'confidentiality', 'calibration', 'triage'], 'B', 'It is a key ethical duty of health workers.', 1],
    ],

    'SHS 2: Communication systems of the body' => [
        ['The gland often called the "master gland", because it controls other glands, is the', ['adrenal gland', 'thyroid gland', 'pituitary gland', 'pancreas'], 'C', 'It sits at the base of the brain.', 2],
        ['Hormones travel from the glands to their target organs through the', ['air', 'nerves', 'lymph nodes only', 'blood'], 'D', 'Nerve messages are faster but shorter-lasting.', 1],
        ['Chemicals that carry signals across a synapse between neurones are', ['antibodies', 'neurotransmitters', 'enzymes', 'hormones only'], 'B', 'Acetylcholine is one example.', 2],
    ],

    'SHS 2: Diseases and disorders' => [
        ['A stroke occurs when', ['a bone breaks', 'the stomach is infected', 'the blood supply to part of the brain is blocked or a vessel bursts', 'the skin is burnt'], 'C', 'Brain cells start to die within minutes without oxygen.', 2],
        ['Cancer is caused by', ['too much exercise', 'a lack of vitamin C', 'mosquito bites', 'cells dividing in an uncontrolled way'], 'D', 'Tumours can spread to other parts of the body.', 1],
        ['A disease present from birth, such as sickle cell disease, that is passed on through genes is', ['an infectious disease', 'a hereditary (genetic) disorder', 'a deficiency disease', 'an injury'], 'B', 'It cannot be caught from another person.', 1],
    ],

    'Diagnostic and therapeutic devices' => [
        ['Blood pressure is measured with a', ['thermometer', 'stethoscope only', 'sphygmomanometer', 'glucometer'], 'C', 'It uses an inflatable cuff around the upper arm.', 1],
        ['An electrocardiogram (ECG) records the', ['brain structure', 'blood sugar level', 'lung volume', 'electrical activity of the heart'], 'D', 'It helps detect irregular heart rhythms.', 2],
        ['An MRI scanner forms images of the body using', ['X-rays', 'strong magnetic fields and radio waves', 'sound waves only', 'radioactive tracers only'], 'B', 'MRI gives detailed images of soft tissues without ionising radiation.', 2],
        ['Ultrasound scans in pregnancy use', ['gamma rays', 'X-rays', 'high-frequency sound waves', 'magnetism'], 'C', 'They are safe for the developing baby.', 1],
        ['A device that gives the heart an electric shock to restore a normal rhythm is a', ['defibrillator', 'nebuliser', 'centrifuge', 'microscope'], 'A', 'Automated defibrillators can be used by trained members of the public.', 2],
    ],

    'Research and design' => [
        ['In a drug trial, the group that receives a dummy treatment is given a', ['placebo', 'vaccine', 'booster', 'antibiotic'], 'A', 'Comparing with the placebo group shows the drug\'s real effect.', 2],
        ['A trial in which neither the patients nor the researchers know who gets the real drug is', ['double-blind', 'open-label', 'single-blind', 'observational'], 'A', 'It prevents bias in the results.', 3],
        ['Before research involving people begins, it must usually be approved by', ['an ethics committee', 'a football club', 'the tax office', 'a radio station'], 'A', 'The committee checks that participants are protected.', 1],
    ],

    'SHS 3: Digestion and energy' => [
        ['The molecule that cells use as their immediate source of energy is', ['ATP', 'DNA', 'glucose only', 'urea'], 'A', 'Respiration releases energy from glucose to make ATP.', 2],
        ['The first stage of respiration, glycolysis, takes place in the', ['cytoplasm', 'nucleus', 'ribosome', 'cell wall'], 'A', 'Glucose is split into pyruvate; later stages happen in the mitochondria.', 3],
        ['Salivary amylase starts the digestion of', ['starch', 'protein', 'fat', 'vitamins'], 'A', 'It breaks starch into maltose in the mouth.', 1],
    ],
];
