<?php

/*
| SHS Literature in English: WASSCE-style objective questions on literary terms and appreciation,
| written for Otisam. Set texts change every few years, so these test general literary knowledge
| rather than one prescribed book. Not copied from WAEC papers.
|
| [stem, [A, B, C, D], correct letter, explanation, difficulty 1-5]
*/

return [
    // Genres and forms
    ['A play that ends sadly, usually with the downfall of the main character, is a', ['comedy', 'tragedy', 'farce', 'melodrama'], 'B', 'A tragedy shows the fall of a hero, often through a flaw.', 1],
    ['A play written mainly to amuse, with a happy ending, is a', ['tragedy', 'comedy', 'elegy', 'epic'], 'B', 'Comedy aims to entertain and usually ends happily.', 1],
    ['A long narrative poem about the deeds of a hero is', ['a sonnet', 'an epic', 'a limerick', 'an ode'], 'B', 'Epics such as the Iliad tell heroic stories on a grand scale.', 2],
    ['A poem of fourteen lines with a fixed rhyme scheme is a', ['ballad', 'sonnet', 'haiku', 'elegy'], 'B', 'A sonnet has 14 lines, such as the Shakespearean form.', 1],
    ['A poem that mourns the dead is', ['an ode', 'an elegy', 'a lyric', 'a ballad'], 'B', 'An elegy is a lament for someone who has died.', 1],
    ['A story in which characters and events stand for ideas or moral qualities is', ['an allegory', 'a biography', 'a tragedy', 'a satire only'], 'A', 'In an allegory, such as "Animal Farm", events carry a hidden meaning.', 2],
    ['Writing that ridicules human weakness or society in order to correct it is', ['satire', 'elegy', 'lyric', 'tragedy'], 'A', 'Satire uses humour, irony and exaggeration to criticise.', 2],
    ['The story of a person\'s life written by himself or herself is', ['a biography', 'an autobiography', 'a novel', 'a fable'], 'B', 'An autobiography is self-written; a biography is written by someone else.', 1],
    ['A short story that teaches a moral, often with animals as characters, is', ['a fable', 'a sonnet', 'an epic', 'a tragedy'], 'A', 'Fables, such as Ananse stories, end with a lesson.', 1],

    // Figures of speech
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

    // Literary devices and elements
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

    // Poetry techniques
    ['The pattern of stressed and unstressed syllables in a line of poetry is its', ['rhyme', 'stanza', 'metre', 'diction'], 'C', 'Metre is the rhythmic structure of a line, such as iambic pentameter.', 2],
    ['A group of lines forming a unit in a poem is a', ['paragraph', 'scene', 'chapter', 'stanza'], 'D', 'A stanza is a verse paragraph.', 1],
    ['Poetry that does not follow a regular rhyme or metre is', ['blank verse', 'a sonnet', 'free verse', 'a couplet'], 'C', 'Free verse has no fixed rhyme scheme or metre.', 2],
    ['Unrhymed iambic pentameter is called', ['free verse', 'ballad metre', 'heroic couplet', 'blank verse'], 'D', 'Blank verse, used widely by Shakespeare, has metre but no rhyme.', 3],
    ['Two consecutive rhyming lines in a poem form a', ['quatrain', 'tercet', 'couplet', 'sestet'], 'C', 'A couplet is a pair of rhyming lines.', 1],
];
