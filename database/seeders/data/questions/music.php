<?php

/*
| SHS Music (2023 curriculum elective): objective questions written for Otisam, not copied from any paper.
| There are no WAEC papers for this subject yet; topics follow the NaCCA SHS Music curriculum
| (rudiments and theory, African and Ghanaian music, instruments, and music history).
| British note names are used (semibreve, minim, crotchet, quaver).
|
| ['Topic' => [[stem, [A, B, C, D], correct letter, explanation, difficulty 1-5], ...], ...]
| A topic named like 'SHS 2: Rigid motion' also files its questions under that year.
*/

return [
    'Note values and time' => [
        ['How many crotchet beats does a semibreve last?', ['1', '2', '4', '8'], 'C', 'A semibreve = 2 minims = 4 crotchets.', 1],
        ['A minim is equal to how many quavers?', ['2', '4', '8', '1'], 'B', 'A minim = 2 crotchets, and each crotchet = 2 quavers, so a minim = 4 quavers.', 2],
        ['A dotted crotchet is equal to', ['one crotchet', 'two crotchets', 'a crotchet and a semiquaver', 'a crotchet and a quaver'], 'D', 'A dot adds half the note\'s value. Half a crotchet is a quaver.', 2],
        ['In 3/4 time, each bar has', ['three crotchet beats', 'three minim beats', 'four crotchet beats', 'three quaver beats'], 'A', 'The top number gives 3 beats and the bottom number 4 means each beat is a crotchet.', 1],
        ['6/8 is an example of', ['simple duple time', 'compound duple time', 'simple triple time', 'compound triple time'], 'B', '6/8 has two dotted-crotchet beats in a bar, each dividing into three quavers: compound duple.', 3],
    ],

    'The staff and clefs' => [
        ['The notes on the lines of the treble staff, from bottom to top, are', ['E G B D F', 'F A C E', 'G B D F A', 'A C E G'], 'A', 'Lines of the treble staff: E, G, B, D, F. The spaces spell F A C E.', 1],
        ['The notes in the spaces of the treble staff, from bottom to top, are', ['E G B D', 'F A C E', 'A C E G', 'G B D F'], 'B', 'The four spaces of the treble staff spell FACE.', 1],
        ['The bass clef is also called the', ['G clef', 'C clef', 'tenor clef', 'F clef'], 'D', 'Its two dots sit either side of the F line, so it is the F clef.', 1],
    ],

    'Scales, keys and intervals' => [
        ['How many sharps are in the key signature of G major?', ['none', 'one (F♯)', 'two (F♯ and C♯)', 'one (C♯)'], 'B', 'G major has one sharp, F♯.', 1],
        ['The key signature of D major contains', ['F♯ and C♯', 'F♯ only', 'B♭ and E♭', 'C♯ and G♯'], 'A', 'D major has two sharps: F♯ and C♯.', 2],
        ['The key signature of F major has', ['one sharp (F♯)', 'two flats', 'one flat (B♭)', 'no sharps or flats'], 'C', 'F major has one flat, B♭.', 1],
        ['The pattern of tones (T) and semitones (S) in a major scale is', ['T T S T T T S', 'T S T T S T T', 'S T T T S T T', 'T T T S T T S'], 'A', 'Every major scale follows tone, tone, semitone, tone, tone, tone, semitone.', 2],
        ['The relative minor of C major is', ['E minor', 'D minor', 'A minor', 'G minor'], 'C', 'The relative minor starts on the sixth note of the major scale and shares its key signature. In C major the sixth note is A.', 2],
        ['The interval from C up to G is a', ['perfect 4th', 'perfect 5th', 'major 3rd', 'major 6th'], 'B', 'Count C, D, E, F, G: five letter names, so a 5th. It is perfect because G is in the C major scale.', 2],
        ['The interval from C up to E is a', ['minor 3rd', 'perfect 4th', 'major 2nd', 'major 3rd'], 'D', 'C to E spans three letter names and two whole tones, so it is a major 3rd.', 2],
        ['The tonic triad of C major is', ['C E G', 'C D E', 'C F A', 'C E♭ G'], 'A', 'A triad stacks the 1st, 3rd and 5th notes of the scale: C, E and G. C E♭ G is C minor.', 1],
    ],

    'Dynamics and tempo' => [
        ['The term forte (f) means', ['soft', 'very soft', 'loud', 'gradually slower'], 'C', 'Forte is Italian for loud or strong.', 1],
        ['The term piano (p) means', ['loud', 'fast', 'soft', 'detached'], 'C', 'Piano means soft. Detached notes are marked staccato.', 1],
        ['A crescendo means', ['gradually getting louder', 'gradually getting softer', 'suddenly loud', 'very slow'], 'A', 'Crescendo means growing louder. Getting softer is a diminuendo or decrescendo.', 1],
        ['The tempo marking allegro means', ['slow', 'fast and lively', 'at a walking pace', 'very slow and solemn'], 'B', 'Allegro means quick and cheerful.', 1],
        ['The tempo marking andante means', ['very fast', 'gradually slower', 'extremely loud', 'at a walking pace'], 'D', 'Andante comes from the Italian for walking: a moderate, flowing speed.', 1],
    ],

    'Instruments' => [
        ['A drum with a skin stretched over it is classified as a', ['chordophone', 'aerophone', 'membranophone', 'idiophone'], 'C', 'Membranophones sound when a stretched membrane vibrates.', 1],
        ['An instrument that sounds when its own body vibrates, like the gankogui (double bell), is', ['a membranophone', 'an idiophone', 'a chordophone', 'an aerophone'], 'B', 'Idiophones such as bells, rattles and xylophones vibrate as a whole when struck or shaken.', 2],
        ['The seperewa (Akan harp-lute) is a', ['chordophone', 'aerophone', 'membranophone', 'idiophone'], 'A', 'The seperewa has strings, so it is a chordophone.', 2],
        ['The atenteben is a', ['talking drum', 'xylophone', 'bell', 'bamboo flute (an aerophone)'], 'D', 'The atenteben is a Ghanaian bamboo flute, played by blowing: an aerophone.', 1],
    ],

    'Ghanaian music and dance' => [
        ['Adowa is a traditional dance and music of the', ['Ewe', 'Akan', 'Dagomba', 'Ga'], 'B', 'Adowa is performed by the Akan at funerals and festivals.', 1],
        ['Agbadza is a traditional recreational music and dance of the', ['Akan', 'Ga', 'Ewe', 'Gonja'], 'C', 'Agbadza comes from the Ewe and grew out of an older war dance.', 1],
        ['Kpanlogo music and dance comes from the', ['Dagaaba', 'Asante', 'Nzema', 'Ga'], 'D', 'Kpanlogo developed among the Ga of Accra in the 1960s.', 1],
        ['Which Ghanaian composer wrote "Yen Ara Asase Ni"?', ['Ephraim Amu', 'E. T. Mensah', 'J. H. Kwabena Nketia', 'Philip Gbeho'], 'A', 'Ephraim Amu composed this well-known patriotic song.', 2],
        ['Ghana\'s national anthem was composed by', ['Ephraim Amu', 'Philip Gbeho', 'Kwame Nkrumah', 'Nana Ampadu'], 'B', 'Philip Gbeho composed the music of "God Bless Our Homeland Ghana".', 2],
        ['E. T. Mensah is often called the King of', ['hiplife', 'gospel', 'highlife', 'reggae'], 'C', 'E. T. Mensah and his Tempos band made dance-band highlife popular across West Africa.', 2],
    ],

    'Form and history' => [
        ['A piece in the form A B A is in', ['binary form', 'ternary form', 'rondo form', 'strophic form'], 'B', 'Ternary form has three sections; the first returns after a contrasting middle. Binary form is A B.', 2],
        ['Which of these composers belongs to the Baroque period?', ['Frédéric Chopin', 'Claude Debussy', 'Johann Sebastian Bach', 'Igor Stravinsky'], 'C', 'Bach (1685–1750) is a Baroque composer. Chopin is Romantic, Debussy Impressionist and Stravinsky 20th-century.', 2],
    ],

    'SHS 1: Musical expression and articulation' => [
        ['A dot placed above or below a note tells the performer to play it', ['legato (smoothly)', 'staccato (short and detached)', 'very loudly', 'twice as long'], 'B', 'A dot after a note lengthens it instead.', 2],
        ['A curved line joining notes of different pitch, showing they are played smoothly, is a', ['beam', 'tie', 'slur', 'bar line'], 'C', 'A tie joins two notes of the same pitch into one longer sound.', 2],
        ['The sign that tells a performer to hold a note longer than its value, at the player\'s choice, is a', ['coda', 'repeat sign', 'accent', 'fermata (pause)'], 'D', 'It looks like an eye: a dot under a curve.', 2],
        ['The sign ">" above a note means', ['play it softer', 'play it with an accent (stress)', 'repeat it', 'slide to the next note'], 'B', 'Accents make particular notes stand out.', 1],
    ],

    'SHS 1: Harmony and four-part writing' => [
        ['In four-part (SATB) writing, the four voices are', ['treble, mezzo, alto and bass', 'soprano, alto, treble and baritone', 'soprano, alto, tenor and bass', 'tenor, bass, contralto and treble'], 'C', 'SATB is the standard four-part choir.', 1],
        ['The primary triads of a major key are built on degrees', ['V, VI and VII', 'II, III and VI', 'I, II and III', 'I, IV and V'], 'D', 'In C major they are C, F and G.', 2],
        ['A perfect cadence is the chord progression', ['IV to I', 'V to I', 'I to V', 'V to VI'], 'B', 'It sounds final, like a full stop.', 2],
        ['The "Amen" cadence (IV to I) is called the', ['imperfect cadence', 'perfect cadence', 'plagal cadence', 'interrupted cadence'], 'C', 'It is often sung at the end of hymns.', 2],
        ['In traditional four-part harmony, which movement between two voices is avoided?', ['stepwise motion', 'contrary motion', 'oblique motion', 'consecutive (parallel) fifths and octaves'], 'D', 'Parallel fifths and octaves weaken the independence of the parts.', 3],
        ['A cadence that ends on chord V is', ['a perfect cadence', 'an imperfect cadence', 'a plagal cadence', 'an interrupted cadence'], 'B', 'It sounds unfinished, like a comma.', 3],
    ],

    'SHS 2: Texture and listening' => [
        ['Music with a single melody line and no accompaniment has a', ['polyphonic texture', 'homophonic texture', 'monophonic texture', 'heterophonic texture'], 'C', 'Unison singing is monophonic.', 2],
        ['A melody with chordal accompaniment, as in most hymns, has a', ['no texture', 'monophonic texture', 'polyphonic texture', 'homophonic texture'], 'D', 'The parts move together in chords.', 2],
        ['Several independent melodies sounding together, as in a fugue, form a', ['monophonic texture', 'polyphonic (contrapuntal) texture', 'homophonic texture', 'drone'], 'B', 'J. S. Bach wrote much polyphonic music.', 2],
        ['In African music, a leader singing a line and the group answering is called', ['canon', 'counterpoint', 'call and response', 'ostinato'], 'C', 'It is common in work songs and church music in Ghana.', 1],
    ],

    'SHS 2: Digital music and notation software' => [
        ['MuseScore and Sibelius are examples of', ['web browsers', 'video editors', 'spreadsheets', 'music notation software'], 'D', 'They let composers write and play back scores.', 1],
        ['MIDI is a system that lets electronic instruments and computers', ['record only audio waves', 'exchange performance data such as notes and timing', 'print sheet music only', 'tune guitars automatically'], 'B', 'MIDI carries instructions, not sound itself.', 2],
        ['Software used to record, edit and mix music on a computer is a', ['compiler', 'word processor', 'digital audio workstation (DAW)', 'database'], 'C', 'Examples include FL Studio and Audacity.', 1],
    ],

    'SHS 2: The music industry and careers' => [
        ['The organisation that collects and pays royalties to music rights owners in Ghana is', ['GSA', 'GRA', 'NCA', 'GHAMRO'], 'D', 'The Ghana Music Rights Organisation collects fees for public performance and broadcasting.', 2],
        ['Money paid to a songwriter each time their song is broadcast or sold is a', ['royalty', 'salary', 'tax', 'loan'], 'A', 'Royalties reward the owners of copyright.', 1],
        ['The person who oversees the recording of a song, guiding the sound and arrangement, is the', ['music producer', 'promoter', 'disc jockey', 'critic'], 'A', 'Producers shape the final recording.', 1],
    ],

    'SHS 3: Global musical traditions' => [
        ['The gamelan is a traditional ensemble of tuned percussion from', ['Indonesia', 'Ghana', 'Brazil', 'Ireland'], 'A', 'It features gongs, metallophones and drums.', 2],
        ['The sitar is a plucked string instrument associated with the music of', ['India', 'Japan', 'Ghana', 'Spain'], 'A', 'It is used in Indian classical music.', 1],
        ['The four families of instruments in a Western symphony orchestra are', ['strings, woodwind, brass and percussion', 'drums, bells, rattles and flutes', 'keyboards, guitars, vocals and drums', 'chordophones only'], 'A', 'The strings form the largest section.', 1],
    ],

    'SHS 3: Advanced harmony and composition' => [
        ['Changing from one key to another within a piece of music is', ['modulation', 'transposition only', 'syncopation', 'improvisation'], 'A', 'Music often modulates to the dominant or relative minor.', 2],
        ['Rewriting a whole piece in a different key without changing anything else is', ['transposition', 'modulation', 'augmentation', 'diminution'], 'A', 'Singers often transpose songs to suit their voice range.', 2],
        ['Accenting weak beats or off-beats instead of strong beats is', ['syncopation', 'legato', 'a cadence', 'a drone'], 'A', 'Syncopation is common in highlife and jazz.', 2],
    ],
];
