<?php

/*
| SHS Literature in English: WASSCE-style objective questions on literary terms and appreciation,
| written for Otisam. Set texts change every few years, so these test general literary knowledge
| rather than one prescribed book. Not copied from WAEC papers.
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Genres and forms' => [
        ['A play that ends sadly, usually with the downfall of the main character, is a', ['comedy', 'tragedy', 'farce', 'melodrama'], 'B', 'A tragedy shows the fall of a hero, often through a flaw.', 1],
        ['A play written mainly to amuse, with a happy ending, is a', ['tragedy', 'comedy', 'elegy', 'epic'], 'B', 'Comedy aims to entertain and usually ends happily.', 1],
        ['A long narrative poem about the deeds of a hero is', ['a sonnet', 'an epic', 'a limerick', 'an ode'], 'B', 'Epics such as the Iliad tell heroic stories on a grand scale.', 2],
        ['A poem of fourteen lines with a fixed rhyme scheme is a', ['ballad', 'sonnet', 'haiku', 'elegy'], 'B', 'A sonnet has 14 lines, such as the Shakespearean form.', 1],
        ['A poem that mourns the dead is', ['an ode', 'an elegy', 'a lyric', 'a ballad'], 'B', 'An elegy is a lament for someone who has died.', 1],
        ['A story in which characters and events stand for ideas or moral qualities is', ['an allegory', 'a biography', 'a tragedy', 'a satire only'], 'A', 'In an allegory, such as "Animal Farm", events carry a hidden meaning.', 2],
        ['Writing that ridicules human weakness or society in order to correct it is', ['satire', 'elegy', 'lyric', 'tragedy'], 'A', 'Satire uses humour, irony and exaggeration to criticise.', 2],
        ['The story of a person\'s life written by himself or herself is', ['a biography', 'an autobiography', 'a novel', 'a fable'], 'B', 'An autobiography is self-written; a biography is written by someone else.', 1],
        ['A short story that teaches a moral, often with animals as characters, is', ['a fable', 'a sonnet', 'an epic', 'a tragedy'], 'A', 'Fables, such as Ananse stories, end with a lesson.', 1],
    ],

    'Figures of speech' => [
        ['Identify the figure of speech: "The wind whispered through the trees."', ['simile', 'personification', 'hyperbole', 'metonymy'], 'B', 'The wind is given a human action, whispering.', 1],
        ['Identify the figure of speech: "She is as busy as a bee."', ['metaphor', 'simile', 'irony', 'oxymoron'], 'B', 'A simile compares using "as" or "like".', 1],
        ['Identify the figure of speech: "My brother is a lion in battle."', ['simile', 'metaphor', 'alliteration', 'pun'], 'B', 'A metaphor makes a direct comparison without "like" or "as".', 1],
        ['Identify the figure of speech: "I have told you a million times."', ['hyperbole', 'litotes', 'irony', 'euphemism'], 'A', 'Hyperbole is deliberate exaggeration.', 1],
        ['Identify the figure of speech: "Peter Piper picked a peck of pickled peppers."', ['assonance', 'alliteration', 'onomatopoeia', 'rhyme'], 'B', 'Alliteration repeats initial consonant sounds.', 1],
        ['Identify the figure of speech: "The bees buzzed in the garden."', ['onomatopoeia', 'metaphor', 'antithesis', 'apostrophe'], 'A', '"Buzzed" imitates the sound it describes.', 2],
        ['Identify the figure of speech: "a deafening silence"', ['oxymoron', 'simile', 'hyperbole', 'synecdoche'], 'A', 'An oxymoron joins two contradictory words.', 2],
        ['Identify the figure of speech: "He passed away last night" (meaning he died).', ['euphemism', 'hyperbole', 'irony', 'paradox'], 'A', 'A euphemism uses a mild phrase for something unpleasant.', 2],
        ['Identify the figure of speech: "All hands on deck!" (meaning all sailors)', ['metaphor', 'simile', 'synecdoche', 'litotes'], 'C', 'Synecdoche uses a part (hands) for the whole (sailors).', 3],
        ['Identify the figure of speech: "The pen is mightier than the sword."', ['metonymy', 'simile', 'onomatopoeia', 'alliteration'], 'A', 'Metonymy uses a related object for an idea: the pen for writing, the sword for war.', 3],
        ['Identify the figure of speech: "O Death, where is thy sting?"', ['apostrophe', 'simile', 'litotes', 'euphemism'], 'A', 'Apostrophe addresses an absent person or an abstract idea.', 3],
        ['Identify the figure of speech: "He is not a bad singer" (meaning he is a good singer).', ['litotes', 'hyperbole', 'oxymoron', 'paradox'], 'A', 'Litotes is understatement through a negative.', 3],
    ],

    'Literary devices and elements' => [
        ['The main character in a story or play is the', ['antagonist', 'foil', 'narrator', 'protagonist'], 'D', 'The protagonist is the central character; the antagonist opposes them.', 1],
        ['The character who opposes the main character is the', ['protagonist', 'hero', 'antagonist', 'narrator'], 'C', 'The antagonist creates conflict with the protagonist.', 1],
        ['The time and place in which a story happens is its', ['plot', 'tone', 'theme', 'setting'], 'D', 'Setting is when and where the action takes place.', 1],
        ['The central idea or message of a literary work is its', ['plot', 'setting', 'theme', 'style'], 'C', 'Theme is the main idea the writer explores.', 1],
        ['A hint of what will happen later in a story is', ['flashback', 'climax', 'suspense', 'foreshadowing'], 'D', 'Foreshadowing prepares the reader for later events.', 2],
        ['A scene that goes back to events that happened earlier is a', ['denouement', 'foreshadowing', 'flashback', 'prologue'], 'C', 'A flashback interrupts the present to show past events.', 2],
        ['The highest point of tension in a plot is the', ['exposition', 'prologue', 'resolution', 'climax'], 'D', 'The climax is the turning point of the conflict.', 2],
        ['When the audience knows something a character does not, it is called', ['verbal irony', 'sarcasm', 'dramatic irony', 'paradox'], 'C', 'Dramatic irony creates tension because the audience knows more.', 3],
        ['A speech in which a character alone on stage speaks his or her thoughts aloud is a', ['dialogue', 'chorus', 'aside', 'soliloquy'], 'D', 'In a soliloquy the character reveals inner thoughts to the audience.', 2],
        ['A short remark made by a character to the audience, unheard by the other characters, is an', ['soliloquy', 'epilogue', 'aside', 'interlude'], 'C', 'An aside is heard only by the audience.', 2],
        ['The tragic flaw that leads to the downfall of a hero is called', ['catharsis', 'denouement', 'hubris only', 'hamartia'], 'D', 'Hamartia is Aristotle\'s term for the hero\'s fatal error or flaw.', 4],
    ],

    'Poetry techniques' => [
        ['The pattern of stressed and unstressed syllables in a line of poetry is its', ['rhyme', 'stanza', 'metre', 'diction'], 'C', 'Metre is the rhythmic structure of a line, such as iambic pentameter.', 2],
        ['A group of lines forming a unit in a poem is a', ['paragraph', 'scene', 'chapter', 'stanza'], 'D', 'A stanza is a verse paragraph.', 1],
        ['Poetry that does not follow a regular rhyme or metre is', ['blank verse', 'a sonnet', 'free verse', 'a couplet'], 'C', 'Free verse has no fixed rhyme scheme or metre.', 2],
        ['Unrhymed iambic pentameter is called', ['free verse', 'ballad metre', 'heroic couplet', 'blank verse'], 'D', 'Blank verse, used widely by Shakespeare, has metre but no rhyme.', 3],
        ['Two consecutive rhyming lines in a poem form a', ['quatrain', 'tercet', 'couplet', 'sestet'], 'C', 'A couplet is a pair of rhyming lines.', 1],
    ],

    'SHS 1: Oral literature' => [
        ['Folktales, proverbs, riddles and dirges passed on by word of mouth are examples of', ['written prose', 'oral literature', 'drama scripts', 'printed poetry'], 'B', 'Oral literature is performed and remembered rather than written down.', 1],
        ['A dirge is a song or poem performed', ['to praise a chief at his installation', 'at a wedding', 'to mourn the dead', 'to welcome visitors'], 'C', 'Dirges are sung at funerals.', 1],
        ['Ananse stories usually end with', ['a list of names', 'a song only', 'a riddle only', 'a lesson or explanation, often about why things are as they are'], 'D', 'Many folktales teach morals or explain origins.', 1],
        ['A short, wise saying that expresses a general truth is a', ['riddle', 'proverb', 'ballad', 'sonnet'], 'B', 'Proverbs are used to advise and to add weight to speech.', 1],
    ],

    'SHS 1: Prose and narrative' => [
        ['Things Fall Apart was written by', ['Ngũgĩ wa Thiong\'o', 'Wole Soyinka', 'Chinua Achebe', 'Ama Ata Aidoo'], 'C', 'It was published in 1958.', 1],
        ['In Things Fall Apart, Okonkwo\'s village is', ['Verona', 'Mbanta', 'Ilujinle', 'Umuofia'], 'D', 'Okonkwo spends his seven-year exile in Mbanta, his mother\'s village.', 2],
        ['In Things Fall Apart, the boy given to Umuofia as compensation and who lives with Okonkwo\'s family is', ['Nwoye', 'Ikemefuna', 'Obierika', 'Unoka'], 'B', 'Okonkwo later takes part in killing him, which haunts Nwoye.', 2],
        ['The sequence of events in a story is its', ['theme', 'setting', 'plot', 'tone'], 'C', 'The plot shows how events connect, often through cause and effect.', 1],
        ['A story in which the narrator knows the thoughts and feelings of all the characters uses', ['no narrator', 'a first-person narrator', 'stream of consciousness only', 'an omniscient narrator'], 'D', 'An omniscient narrator is "all-knowing".', 2],
    ],

    'SHS 1: Elements of drama and performance' => [
        ['In The Lion and the Jewel, the young village belle whom both Lakunle and Baroka want to marry is', ['Sadiku', 'Sidi', 'Ama', 'Juliet'], 'B', 'The play by Wole Soyinka is set in the village of Ilujinle.', 2],
        ['In The Lion and the Jewel, Baroka is the', ['photographer', 'village schoolteacher', 'Bale (chief) of Ilujinle', 'senior wife'], 'C', 'Lakunle is the schoolteacher and Sadiku is Baroka\'s senior wife.', 2],
        ['Instructions in a play script about movements, settings and how lines are said are', ['prologues', 'soliloquies', 'asides', 'stage directions'], 'D', 'They are usually printed in italics or brackets.', 1],
        ['In An Inspector Calls, the young woman whose death the Inspector investigates is', ['Sheila Birling', 'Eva Smith', 'Sybil Birling', 'Edna'], 'B', 'J. B. Priestley\'s play shows how each Birling contributed to her fate.', 2],
        ['In Romeo and Juliet, the two feuding families are the', ['Okonkwos and Obierikas', 'Birlings and Crofts', 'Montagues and Capulets', 'Lakunles and Barokas'], 'C', 'Shakespeare sets the play in Verona.', 1],
    ],

    'SHS 1: Poetic elements and appreciation' => [
        ['The continuation of a sentence from one line of poetry into the next without a pause is', ['caesura', 'enjambment', 'refrain', 'rhyme'], 'B', 'A caesura is a pause within a line.', 2],
        ['A line or group of lines repeated at intervals in a poem is a', ['pun', 'couplet', 'refrain', 'apostrophe'], 'C', 'Refrains are common in songs and ballads.', 1],
        ['Addressing an absent person, an idea or an object as if it could hear is', ['euphemism', 'irony', 'litotes', 'apostrophe'], 'D', 'For example, "O Death, where is thy sting?"', 2],
        ['The attitude of the poet towards the subject, such as sad or bitter, is the', ['tone', 'metre', 'rhyme scheme', 'stanza'], 'A', 'Tone is shown through word choice and imagery.', 1],
    ],

    'SHS 2: Prose, drama and poetry analysis' => [
        ['A character who changes and develops during a story is a', ['round (dynamic) character', 'flat character', 'stock character', 'foil'], 'A', 'Flat characters show only one or two traits and stay the same.', 2],
        ['A character whose qualities contrast with another\'s, highlighting them, is a', ['foil', 'protagonist', 'narrator', 'chorus'], 'A', 'In Romeo and Juliet, the hot-tempered Tybalt contrasts with the peace-making Benvolio.', 3],
        ['When the audience knows something that a character does not, this is', ['dramatic irony', 'verbal irony', 'situational irony', 'satire'], 'A', 'It builds suspense and tension.', 2],
        ['Writing that mocks people\'s faults or society to bring about change is', ['satire', 'elegy', 'tragedy', 'ode'], 'A', 'Satire often uses humour and exaggeration.', 2],
        ['A story told through a sequence of letters is', ['an epistolary novel', 'a picaresque novel', 'a novella', 'an allegory'], 'A', 'Mariama Bâ\'s So Long a Letter is an example.', 3],
        ['A story in which characters and events stand for ideas, such as good and evil, is', ['an allegory', 'a biography', 'a sonnet', 'a farce'], 'A', 'George Orwell\'s Animal Farm is a famous allegory.', 2],
    ],
];
