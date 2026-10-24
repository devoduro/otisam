<?php

/*
| Every public upload on the Otisam Mathematics YouTube channel (@otisammathematics9887), read from
| the channel's Videos, Shorts and Live tabs on 24 October 2026: 400 videos, 160 Shorts, 1 live
| stream. One "youtube id <tab> title" per line, newest first, as listed on the channel.
|
| VideoCatalogueSeeder adds any that are missing and files each one under a curriculum topic with
| App\Services\Videos\VideoTopicClassifier. New uploads after this date arrive through the daily
| videos:import-youtube job.
*/

$parse = fn (string $tsv) => array_values(array_filter(array_map(function ($line) {
    $parts = explode("\t", trim($line), 2);

    return count($parts) === 2 ? ['id' => $parts[0], 'title' => trim($parts[1])] : null;
}, explode("\n", $tsv))));

return [
    'channel' => 'otisam-maths',
    'videos' => $parse(<<<'TSV'
clX8RMq9MLE	Sat episode 4
_nde18m0DAU	Sat episode 3
dHLJb4dLV4I	SAT EPISODE 2
JaaMMMTkoO8	The Quotient rule
utgZjjGQz_k	The Product Rule
N2kYvtnhD0Y	Finding the derivative of aconstant and square root
56ygIFBkC88	MATH OLYMPIAD Q210
m6Akkeis58k	Basic rules of differentiation
ipJ8tZIhdPg	99% will get it wrong
JorvCwU7XIQ	Solve this in 2 seconds
zpPjTPd3UOw	Differentiating from First principle
jL7hQxZ-P44	Sat episode 1
jPD2pQ2_5vU	Limit at infinity
rN6rkYU7eBg	Multiplication of matrices in simple terms
5vnrEsvyZps	THE CONCEPT OF LIMITS
PSx70GhMCwA	Learn how to solve this quickly
gRP60L_LEMI	Commission explained
zyhAhNlYGaU	Tricky indicial equation
tani2iLtKNw	Profit and Loss made easy
4qaE24fJK5g	For Only genius
XI3fZ0VjV88	Discount
zUuJSEsc_NA	Simple interest
cf1Iog7qLTI	How to find multiple powers of a number
vxvaLzT1e8w	Percentage increase, decrease error
15y_6ifPKiw	introduction to percentages
6jiMwIPvugI	Math Olympiad
43sgcokOx0g	Pythagorean tripple
Z6jGn-HBPA8	Solve in 1 second
b_1tT8Mz3Cc	Can you solve this ?
eQFPKDwmnEg	Can you solve this
qkDUh77VAHk	The seven building blocks of mathematics
ao48B3pENuM	Block 1:Arithmetics
brkrEovMEdQ	Block 1: Fractions
Nl-50qIOSB0	Basic building block 2: Algebraic expression
pC_8O8VY-FM	Block 2: Algebraic Expression
qXfgeYX5elA	Block 2: Factorization Case 1
6xXVAd5dAzU	Block 2: Algebraic Expression || Factorization || Case 2: Re-factorization
GxU45VEXcSw	Quadratic trinomial
SYQIoOpIMCM	Building block 2|Algebraic expression||Factorization||| Difference of two squares
u-RSFE24rMM	Building block 2|Algebraic expression|| Application of difference of two squares
k82-YE4Smo0	Building block 2 |Algebraic expression|| Algebraic fractions||| Multiplication and Division
9UKG7z-5tvk	Building block 2|Algebraic expression||Application of concepts
1OI5EqGWVVE	Math Olympiad|Q1 ||
SiSCcrXuhRw	Block 3 |Substitution and Change of Subject ||Algebraic substitution
foI7esfLxws	Block 3|Change of Subject||Case 1||Linear form
nHnZqkyVO0Y	Building Block 3| Change of subject|| Relations involving fraction||
BmoZjc58FM8	Building block 3|Change of subject||case 3:Relations involving exponents|||
RClFDUiOpFg	Building block 3| Change of subject||Substitution||
GiHFo9olI0A	Math olympiad|Q2||
WPZVray5t9A	Building block 4 | Linear Equation || Case 1|| Method of transposing
4Y8ws8FVfY8	Building block 4|Linear equation| Case 2|Method of grouping like terms
UpGOSngZhSg	Building block 3|Linear equation | Equation involving brackets||
8G2AbShVQnc	Building block 4|Linear Equation ||Case 4 fractional equation|||
KPopigmZT-0	Math Olympiad |Q3|
eurFOLXG9wA	Building block 4| Linear equation || case 5| Equation involving ratios
jEmpgzL_6rQ	Math Olympiad |Q4||
uZNoGsxBl-Y	Math Olympiad | Q5||
mmTJrvr6I1I	Building block 4 | Linear Equation || Word Problem|||
5wIV4A8N5nw	Math Olympiad |Q6 ||
Q3nApZjZZ9U	Block 5 | Linear Inequality || Number line
mmCKgfrL3rE	Math olympiad |Q7||
jWMh_4pnRTc	Block 5 | Linear inequality in one variable || Single inequality|||
0CYBvan_2G8	Math Olympiad |Q8||
C_lV6OIyK3g	Building block 5|Linear Inequality || Double inequality |||
8SAZvhhG94M	Math Olympiad |Q9 ||
TlMm4c5-BOE	Building block 6 |Simultaneous equation || Elimination method|||
Yf-ldpxHlxM	Math Olympiad |Q10||
BsXMjeIkPL4	Block 6 | Simultaneous equation || substitution method |||
HvHSNRHeKdM	Block 6 |Simultaneous equation|| General questions ||
MbSzBEOQuco	Block 6| Simultaneous equation || Word problems |||
zEMQ48dal2E	Math Olympiad |Q11||
jgJqBsw78kA	Block 6 | Quadratic Equation|| Method of factorization
aXG50ouqxKY	Math Olympiad |Q12||
-D3mv83AV20	Block 7 | Quadratic equation || Completing of square |||
Tuc0954-jqk	Math Olympiad |Q13||
LG4yI5F4x_A	Block 6 | Quadratic equation || Almighty formula
feUzRzE_0xI	Math Olympiad |Q14||
HuLXcq6pYM8	Block 7 | Quadratic Equation || Discriminant |||
tLrZO8UrIsU	Block 7|Quadratic equation || Application of nature of root|||
DzDWa86L-Jw	Math Olympiad |Q15||
H5--1EReUOU	Block 7| Quadratic equation || Formation of quadratic equation |||
aFIOvCEz9As	Math Olympiad |Q16||
z-taVuX27yY	Block 7 | Quadratic Equation || Symmetric Functions ||
u57jq6jTZys	Math Olympiad|Q17||
cGkMQ8F8dw4	Block 7| Quadratic Equation ||Application of Symmetric function|||
DM1xziZIkIw	Block 7 | Quadratic Equation||Maximum and Minimum Values|||
NyWSEpSul-E	Block 7|Quadratic Equation|| Quadratic simultaneous equation|||
sOrkfkBCivQ	Math Olympiad |Q18||
MeWJPjurJIc	Lesson 1 |Quadratic Inequality ||
VSnwM3LVXBM	Math Olympiad |Q19||
DTNx19ly5tY	Lesson 2A| Operation on surds ||
Nc9QPO5mfik	Lesson 2B| Radicals ||
7TLJbHHpN_0	Lesson 2C|Likely Examination questions ||
1xYoWQRgbhI	Lesson 3A|Polynomial functions||Arithmetics|||
IcSlZi0NaWA	Lesson 3B |Division of Polynomials||
qjsLMMqvb1U	Lesson 3C| Remainder theorem||
oXMRRnuOioo	Maths Shortcut
WaKgCcjxgl0	Math Olympiad |Q20||
MgCwVSIuKqk	Lesson 3D|Factor theorem||
A7MJs9FF9ws	Polynomial functions |Likelily examination questions ||
_bJb6HXKPaQ	Math Olympiad |Q21||
GH-HmogWZyk	Math Olympiad| Q22||
jXFamrQRCck	Lesson 4A|Functional notation||
iTcuk7h7dRU	Lesson 4B|Functions||
ly2lXEBdb8g	Lesson 4C | Range of a function ||
oYGiL-q4rj4	Math Olympiad |Q23||
mU2dkXGpWu8	Lesson 4D| One - to - one function
cCt5H67M_cM	Lesson 4E|Inverse function||
zSUnT_mGKMM	Lesson 4F|Composite function||
v9ShulXZtgw	Lesson 4G|Likelily examination question||
-IhCkSAbBXs	Math Olympiad |Q24||
10zG5tbN2sE	Lesson 5| Indices || Part 1|||
FsswFlRXakc	Lesson 5 |Indices || Part 2 |||
P08yuiqOkaY	Lesson 5 | Indices || Part 3 |||
R9AMGP-sDUk	Lesson 5 | Indices || Part 4 |||
rZ2DUWjorAg	Lesson 5 | Indices || Part 5 |||
F-08rXbaUkg	Lesson 5 |Indices || Part 6|||
r0I9NMbfkq8	Math Olympiad | Q25 ||
l2zmarVJruQ	Lesson 6 | Logarithm|| Part 1 |||
guFIRebmaMw	Logarithm | Part 2 || Further evaluations
PkrCh6mxqUI	Math Olympiad|  Q26 ||
LbsTbkFXfLI	Math Olympiad |Q27||
OdErFKM6HMc	Logarithm | Part 3 || Equations
bxHPIA9OxnQ	Math Olympiad |Q28||
s1UWlJRgYDw	Logarithm | Part 4 || Simultaneous equation
pT5DB6Or2fA	Logarithm |Part 5 || Change of base
BJ6Ylyx0su0	Math Olympiad | Q29 || Hard square root
kWr4-IampRg	Logarithm | Part 6|| Application |||
zp-s9nIFST4	Math Olympiad | Q30 ||
5O6x_p98I34	Math Olympiad IQ31 || Algebraic identities|||
MU8EZLDrBvQ	Maths Olympiad | Q32 ||
2TN6Jc5T-rE	Math Olympiad | Q33 ||
-_jblYs775M	Math Olympiad | Q34 ||
kJARvNk0xWg	Four figure table | Characteristics and Mantissa||
nEY4LscBLGo	Math Olympiad |Q35||
utyab75qfGc	Four figure table| Negative characteristics||
lJSGbOMmNmQ	Math Olympiad |Q36||
H8eIe9zcZT4	Math Olympiad| Q37||
M2AnLmJ70mE	Math Olympiad |Q38 ||
i284yQ5VWq0	Four figure table| Application of negative characteristics ||
GkHSqKhukMY	SAT 01
O1fOhsY7fR8	Math Olympiad| Q39||
raOZbXHhNl8	Math Olympiad |Q40||
isT0ahaDl90	Math Olympiad| Q41||
c3ZxkJRY0WU	Math Olympiad| Q42||
MwPNG8ZkNWA	Math Olympiad| Q43||
Jo32N7l0oUs	Binary Operation | Part 1 ||
mDr4qqoRMTE	Binary Operation | Part 2 ||
HB0R0PRkTH8	Math Olympiad | Q44 ||
A1HvHUgnPKc	Binary Operation | Part 3 ||
vb1Al51PRto	Binary Operation | Part 4 ||
V-GHBk4-9Y0	Binary Operation |Part 5 |
pSCcOjMNL3s	Binary Operation| Part 6 ||
iw6eQfr-u6Q	Math Olympiad| Q54 ||
TQCiQjbFlGk	Binomial Expansion | Part 1 || Pascal triangle |||
Vxhmu3FlAoQ	BODMAS
WOeVtcN7agg	Binomial expansion | Part 2 || theorem
egBkgVxED_A	Binomial expansion |Part 3 || the binomial theorem
yPuFsNZndPI	Binomial Expansion | Part 4 ||
-tOKLiB9vBI	Math Olympiad 81
U3iogEuagvE	Application of binomial theorem
vhZU-bYbZNI	Binomial theorem |part 6 | comparing coefficients
igoDYrQDPyc	Math Olympiad
kB1q_fFRCg8	Math Olympiad| Q90||
0lwiRQzwmk4	Rational function |Part 1||
Z2rDqEgRHEY	Rational function| part 2 ||
Ffwm0tYiHRc	Math Olympiad |Q94||
WXr5hvc1a90	Math Olympiad |Q95||
SXTs6huTyi0	|Rational function| part 3 |||
5_SJ1c8Pmu0	Rational function| Part 4||
gsmCBI2olos	Math Olympiad|Q97||
tJRyIHFXNk0	Rational function | Part 5 ||
QLlfowBPSdA	Math Olympiad| Q98||
SWSauWmH_sY	Rational function| part 6||
36l9aEBzj64	Math Olympiad |Q99||
R7vsA2zV7KU	Math Olympiad| Q100||
B94uQ8lWMPw	Math Olympiad |Q101 ||
0zSe8t4JiI4	Rational function | Part 7 ||
6zsfo0KhJDg	Math Olympiad |Q102||
lQu2rw83HX4	Math Olympiad| Q103||
70jfIFGxiNI	Math Olympiad104||
cOYnNfNdcII	Quadratic Indicial equation | 01 ||
GFGdHRJcQmQ	Math Olympiad |Q105||
WLSU6xBNgPI	Math Olympiad| Q106||
8oRNeOrUUts	Algebraic expression |01 ||
oW2JK3IxR5A	Math Olympiad | Q108||
xrBAwBbBwy4	Binomial Expansion | 01 ||
oCmcLU6CCnE	Math olympiad |107
1n1cqv38JWs	Math Olympiad |Q108||
AsErlkhrXXs	Coordinate geometry| part 1||
rawNQ4aTvJM	Math Olympiad |Q109||
smwFLTXfTCU	Logarithm |01||
_cIAynDk_jw	Coordinate geometry 1 | part 2||
UeAjThg-99I	Math Olympiad |Q110||
fqUEhitZ5vU	Coordinate Geometry 1|Part 3||
edLyyez8TwM	surd 01
E_VPBON_Egs	Logarithm |Q02||
u19F8ZxU0DQ	Coordinate geometry 1|Part 4||
bRJFAwlVTJQ	coordinate geometry 1 | part 5 ||
VxuEhMcW4sg	Math Olympiad |Q112||
sHrZN4tyYAY	Math Olympiad|Q113||
vsXZeJsF0eM	Coordinate geometry 1|part 6||
kSJ_H5PtWxM	coordinate geometry 1|part 7||
TlBE2FL2yvc	Math Olympiad |Q114||
BPpuAUz722Y	coordinate geometry 1| part 8||
LASVkNDnzow	Math Olympiad | Q115||
PzjwGfVq7Jc	Equation of circle Part 1
d_CeCgxfExc	Merry Christmas
kL_vlWnwyD8	Math Olympiad 116
_6XHbkVcGCI	Equation of circle| Part 2|| centre and point |||
Z2jJZdWPnsU	Math Olympiad |Q117||
jmunLiXnjLo	HAPPY NEW YEAR|2024||
0IAJGUPHi2Y	Equation of circle | Part 3|| Ends of diameter|||
kJ6FGuirzn8	Math Olympiad|118||
7yltHXrroNM	Equation of circle| Part 4 || Given three points|||
9mq2KLUwb2M	Math Olympiad| Q119
cNAqo4bhR-k	Math Olympiad |Q120||
Xpho4XpC5r4	Equation of circle|Centre and radius||
1DTIYLZCDkI	Math Olympiad| Q121||
mHJnB3I2Ykc	Math Olympiad |Q122||
MDfaD3Pq7-Y	Math Olympiad| Q124||
SwdttxVUYg4	Math Olympiad| Q123||
CPwM9QPiQ8w	equation of circle part 6 tangent and normal
j_JMzmTBw2k	Log 03
tGMPfSDIS8A	MATH OLYMPIAD Q125
fY9flRnEMOs	MATH OLYMPIAD |Q126||
C339jI4CZPw	Math Olympiad |Q127||
JbCAWP5sY30	Math Olympiad| Q128||
r0GdTrWrzdE	Direct Variation
70IR4SkiyaY	Math Olympiad| Q129||
kzW3GYoFUFY	Math Olympiad Q130
NIKs8n4VBpY	Math Olympiad| Q131 ||
SIk50xeoJjo	F01 |functions||
78Pz-XFKd6k	Alg02|Algebraic expression ||
wNhBB0bIO6o	Qe01|quadratic equation||
MYlddne91vg	Math Olympiad| Q132||
f1ffdyy7lWU	Math Olympiad Q135
56t5JjMR6pE	Math Olympiad |Q136||
qYK6GMtlAUI	Math Olympiad |Q137||
PgpNBIeoPeY	Math Olympiad |Q138||
enMQqBdWrzg	Alg03|Algebraic Fraction
Psitg4vmi7E	Math Olympiad| Q139||
rbSgtS-YarQ	Eoc01 |Equation of circle ||
PsMSdg0Ft5M	Math Olympiad |Q140||
JbVR1QvR8xU	Log04|Logarithm||
zKUkvsZteF4	Math Olympiad| Q141 ||
8HUaqGtB9tc	Math Olympiad Q142
Dq4ICrA1_vA	Log05
CgxXuH-f-z0	Circle theorem| Theorem 1 ||
30ueukYVnMI	Math Olympiad | Q142||
it3kCXE0WXs	Math Olympiad Q144
PNt-LLhCDYI	Circle theorem 2|Plane Geometry 2||
Bg2lG1qsDpo	Math Olympiad Q145
Ak3i6mNwjW0	Math Olympiad| Q146||
N91DXe5_ZqM	fathers day
Im76Ppaves8	Math Olympiad 147
nuKXhxg26II	Log06
UxkkOWu8NO8	Math Olympiad Q148
7ikr69dS_QY	Advance Quadratic equation|qe02||
erJ2aHYyO9Y	Math Olympiad 149
MygZ7_99SQU	Math Olympiad Q150
hxjVId1fKxA	Math Olympiad Q151
hwoNdXURN0E	Math Olympiad Q152
qLyiZ49qLd8	Quadratic equation|Qe04||
Z4DhacGlSbc	Math Olympiad| Q153||
vqYe1vwtrjs	Math Olympiad| Q154||
ja3toOOwnGQ	Circle theorem 3|Plane Geometry 2||
KV9dFI3_skA	Math Olympiad |Q155||
j5_iSGZtHpg	Algebraic Expression|Alge04||
rifd3eintGw	Math Olympiad| 156||
vvk9lG7S8DQ	Math Olympiad |Q157||
_OjztDK1dPA	Circle theorem 4
ZC4AwMRstzM	Math Olympiad |Q159||
MMjxrmmcD-Y	Math Olympiad |Q160||
JipXAciR_xU	Math Olympiad |Q161||
fRiHxiIKzb0	Math Olympiad| Q162||
Rcms4YAxCz8	Math Olympiad| Q163||
Nd5iDZJ2gl4	Math Olympiad |Q164||
jWKSJccc6KI	Alg05|Algebraic expression||
3gPS-83wEuQ	Circle theorem 5
yPg3uFPVFNI	Math Olympiad Q165
zmlQIuVckek	Circle theorem 6| Tangent and radius||
W_UmP9Do-vk	Innovative indices| In03||
sF6IQ2OMJdM	Math Olympiad| Q166||
3Bwh8clHe5w	Math Olympiad |Q167||
vj6c8KP5imo	Licencial examination | prep 1||
s_eQCME8pAo	Math Olympiad |Q168||
GQre4855Hqs	GTLE 2021|Q11- Q20||
Tb021OytjHY	GTLE 2021| Q21- 30||
hG6hmmQHR6M	Math Olympiad |Q169||
Zj4IyuJe2gQ	REMINDER THEOREM
A-51JVC6BSs	SHORTCUT04
Zxc2nFX_TrM	Shortcut05
5Ldt2MRj-5Y	ADISCO OUTSHINES OWASS, AUGUSCO
BHGhrBUoOEE	PREMPEH COLLEGE TAMED PRESEC AGAIN
Ryj3_-DXCRg	Log07
7TXdp7qpy5g	True/False King |NSMQ2021|| ABSOLUTE BRILLIANCE
1GMsvSSsTGM	Math Olympiad |Q170||
yLKLfBY4xqg	WHY|PRSEC Legon, OWASS, ADISCO||Did not come to the party|||
qe2sKNlP0Yg	|PRESEC MESMERIZED BY THE SPEED OF PREMPEH COLLEGE ||
XkTQ3zwc8xI	FIRST YEAR STUDENT OF OWASS BROKE THE HEART OF PREMPEH COLLEGE
w4JOevMMqzY	Maths Shortcut| 07||
j-XUjnM8Bog	THE EASIEST WAY OF LEARNING MATHEMATICS
HbPKCjTHEZo	SIMMS SINKS KPANDO, HOPE| AFTER A GREAT COME BACK||
j73scJFNRVY	KOFI AGYEI SHTS WOWS THE AUDIENCE AS THEY PROGRESS TO THE NEXT STAGE
8xZBQep5O5E	HOW TO USE ALGEBRAIC EXPRESSION TO SOLVE REAL LIFE PROBLEMS
di3MlcA36jI	WHAT AN EXCITING SPEED RACE|KETA SHTS VS KUMACA||
8VXhzL6wkWg	PREMPEH COLLEGE ANNOUNCE THEIR PRESENCE BY DEMOLISHING AWUDOME
G-crkV3r1Ts	NSMQ2024||OWASS VS OLAG VS ZION COLLEGE
P8Qv9mkfQIA	MFANTSIPIM TAKES PREMPEH COLLEGE BACK TO SOFOLINE
8ABV4-W9YTg	PRESEC, LEGON SENDS THE SAINTS HOME
OVZuYOm4cZA	Math Olympiad| Q172||
5Sfkh1SLZa4	KETA SHTS CONTINUES ITS DEMOLISHING EXERCISE AS THEY KICKED ACHIMOTA SCHOOL OUT
nlf0FFQelTU	MFANTSIPIM SENDS REIGNING CHAMPION(PRESEC, LEGON) HOME
8lTHUcyEIG4	GHANA NATIONAL TOOK THE SHINE FROM AUGUSCO'S WIN IN THE RIDDLE
Uzg5kN-fJog	WHO WINS 2024 NSMQ FINAL
GRujr9Gw4NQ	MFANTSIPIM WINS NSMQ 2024 AFTER DEFEATING AUGUSCO AND KETA SHTS
adIb3QJ5nT0	Math Olympiad |Q173||
5sFGaAbAhp0	Math Olympiad|Q174||
Aon-a60yTZw	Math Olympiad| Q175||
vsGD3RvQ2iw	Math Olympiad |Q176||
XsBlKgC_x3k	Math Olympiad |Q177||
v9NKkcbn9Bs	LENGTH OF A TANGENT OF A CIRCLE
ipMe3l4qXkM	CONDITION FOR A LINE TO BE TANGENT TO A CIRCLE
lZFmHxYRoAs	Internal touch of a circle
Ph38UgMq4EA	Under what circumstance can two circles touch externally?
pdtloDRo8k8	FINDING AND DESCRIBING A LOCUS
l3ad8hXpzZU	Number Pattern
1-EEFwy1p_I	LINEAR NUMBER PATTERN
qxsjEYwGnz4	HOW SEQUENCE SHAPE OUR WORLD!
wIobywWePX4	QUADRATIC NUMBER PATTERN
pzTRF-_8YJ4	ARITHMETIC PROGRESSION
MEucehGtBzo	SUM OF ARITHMETIC PROGRESSION
ysTLgocnzJ0	SHORTEST WAY OF EVALUATING LOGARITHM
LM2GJtRIgSE	Math Olympiad |Q178||
sb1hhHpnNLk	Math Olympiad |Q179||
xSrIMg3Tanc	Assessment test 1|binary operation|| Q1|||
nVYgHXD0ymY	Math Olympiad Q180
VR5Lc_mib2E	Solution to Assessment test 1(Binary operation Q5)
sY5NHZx0GbA	Math Olympiad Q180
t1pTB_1NbNM	MATHS SHORTCUT 09(NSMQ)
CsAcNfRH-cI	FINDING THE NUMBER OF TERMS WHEN THE SUM IS GIVEN
104gXBnWtI0	MATHS SHORT CUT 10
mtLZMc600e0	ARE YOU AWARE THAT DIVISION IS SUCCESSIVE SUBTRACTION
HmbngETGSvE	Math Olympiad Q|182||
RYE4g02bTLI	LIVE LESSON ON TRIGONOMETRY| PART 1||
xxCQ7vwR6o0	Application of factorization
Qv0mff4lH9w	Live lesson on Trigonometry|Part 2||
OgNOMKh1mlU	LOG |08||
5YpN3VpO4cU	BINOMIAL EXPANSION|01||
iEVfTEtXglo	log 09|Simultaneous Logarithm||
jU9sb0utqZs	Maths shortcut 11
KOTDuowAuyo	Math Olympiad Q183
-yjJM1YFufY	Trig Identities
S2bQnz-SVzI	Math Olympiad| Q184
VwxLbHGIDRI	Compound angles
P55zivhs0Sg	Arithmetic challenge|01||
wgSHurFiseY	Arithmetic Challenge |02||
hxMbXTCwHks	Math Olympiad Q185
qEtiqM-m46w	Puzzle 01
s-AFKFNt6sg	MATH OLYMPIAD| Q186||
lQK2YIl82h4	Math olympiad| Q187||
1q9_JxFYwq0	TRICKY LOGARITHM
Ug1nz90M3XE	APPLICATION OF COMPOUND ANGLES
gxFH0H0qhUo	Arithmetic challenge 03
iSF260XALb0	Math Olympiad |Q188||
kXcqNDLtav0	Why Cos 90 Degrees Is Zero
4syoAGE-aF4	MULTIPLE ANGLES
r0zKeR5AXWk	MATHS SHORTCUT
oRLyOKk8m1o	Practical demonstration of inverse operation of  Addition and Subtraction
w6BdQDDs4Fw	complementary angles
f3RQVcIRrIc	Math Olympiad |Q189||
_bEFOLdt080	USING QUOTIENT RULE
ZhY1jAXsPa0	Exponential sequence nth term
wrJGOB5FflA	SIMPLE WAY OF EVALUATING TRIG EXPRESSIONS
J_8pu5G2j-o	FACTORIAL SIMPLIFIED
mkeyzcqcSqM	SIMPLIFYING INTEGERS
Yz0EygQM1z4	MATH OLYMPIAD |Q191||
M8cVlVa6Nak	Logarithm simplified
Mop_-S4m69I	FORMING CUBIC FUNCTION
UPdp_FmHCkg	Math Olympiad |Q192||
ThW7kBA1H7Q	Math olympiad |Q193||
rc2Bcsm6teo	Math Olympiad |Q194||
eKujUisEOWs	HOW TO SOLVE INDICIAL EQUATION EASILY
5qavptFHbSk	SOLVING DIFFICULT LINEAR EQUATION
GGCC55XZvPo	MANY WILL FIND IT HARD
ozgf-HIpRVM	CAN YOU EVALUATE THIS?
S8tDWJTNSUw	MANY PEOPLE WILL GET IT WRONG
-wfezkQGEDc	ARE YOU CONFUSED?
Q3po3_zm4dk	THE SECRET BEHIND FACTORIAL
KHIsuvASOak	99% WILL NOT SUCCEED
DnyylARLJb0	IT LOOKS SIMPLE?
KosV7DYXZSE	LEARN MATRIX IN 5 MINUTES
9-j9xbCuhVk	CAN YOU SOLVE THIS IN 1 MIN
CXw1zX4bn10	LEARN HOW YOU CAN SIMPLY SOLVE THIS
IT5y6Bt8C5g	SIMPLIFY THE IDENTITY IN 2 SECONDS
FZQCcFjXiDg	WHY NOT USE 1 S TO LEARN MATRICE ADDITION
uP_V28lQSPc	Learn the trick of solving combination
_9efEVW-8kw	Subtraction of matrices in 1 min
gSNODC9JwDw	Math Olympiad Q202
TSV),
    'shorts' => $parse(<<<'TSV'
ZZiOy88BDTw	Math Olympiad Q210
G0w6zKjQBUs	Why Arithmetic is a Game Changer
M7iKK1sd1Mc	Factorize Trinomials in Seconds!
X2y6G1hhAaQ	Are 2.0 and 2 the Same?
6c9RZZOPArA	Reality of Percentages in real world situations
2GNeQBob76M	IS 22/7 A RATIONAL OR IRRATIONAL?
sjA32uMYb9c	Master Linear Equations in 60 Seconds!
pA12z4JMRqo	Mastering Significant Figures in Seconds
hJyzkY0Qwww	Master the Divisibility Rule for 6 in Seconds
kV9naY7DLl0	Probability in real life situation
7VkwMrsenGE	Who wins ? Mean or Standard deviation
ZWJ2cApr4Qc	Wow! Permutations in Real Life
g5hKE236CBc	PRACTICAL DEMONSTRATION OF LIMIT OF A FUNCTION
4vZyGnCBL6M	Practical demonstration of vectors in real life
LcedB6nDoyw	Pythagoras Theorem in Real Life!
pHIk5VfS22Q	Solve Quadratic equation  in 30 Seconds!
5TL5IppP3pM	EVEN FUNCTIONS IN REAL LIFE!!!
KSoVq1nswR8	YOU WILL NEVER FORGET ODD FUNCTIONS AFTER ........
xFH5ff97jmc	SQUARING A NUMBER THAT ENDS WITH 5
BpdX_-3Bh2U	HOW WILL YOU KNOW A NUMBER IS DIVISIBLE BY 4
pv1bZni7bmU	Good news! See Class Boundaries in Real Life situation
CAxMyVJ_Wd8	Perfect Squares in Real Life!
Xy1ODQUg9Ps	Real Life  Application of Pythagoras Theorem
wS6wjgBe2gw	Why Mean Matters in Decisions!!!
2Q0wwI9zH90	Why Integers Rule the Math World!
JckfcPBW59s	THE EASIEST WAY OF ARRANGING ITEMS/OBJECT
ggB8ygrVrXo	DO YOU KNOW THAT MATRICES SHAPE OUR WORLD?
7sTtwS6_qCk	PRACTICAL DEMONSTRATION OF ADDITION OPERATION
cWqEmXNjcWw	USES OF DISTRIBUTIVE PROPERTY
flAlG3M5ygI	PRATICAL DEMONSTRATION OF INTEGERS IN REAL LIFE
LS__UGv4uaw	Why is 0! Equal to 1
S1nSHytHdxo	PRACTICAL ILLUSTRATION OF COMMON FACTOR IN ALGEBRA
w4dkEBLaoCY	MULTIPLY TWO DIGIT NUMBERS IN SECONDS!
B4e-tINFRT0	Angle of Depression  Real Life Examples!
Mh5eSP3WncQ	UNLIKE TERMS IN REAL LIFE APPLICATION
5h6g-CsRDMs	DEMONSTRATION OF RECIPROCAL OF A NUMBER
2UoKsPRC31Q	PRACTICAL DEMONSTRATION OF ADDITIVE INVERSE
O_yj4gdQe2c	PRACTICAL DEMONSTRATION OF A METRE LENGTH
4EkFVQD_Mso	THE INDIVISIBILITY OF ZERO
ns3QyMIcOwI	WHY 22/7 IS NOT RATIONAL NUMBER
e34T1uyhDyQ	WHY DOUBLE NEGATIVES BECOME POSITIVE
OKmsDpbrHCc	Practical demonstration of Angle of Elevation
OaF3MuMZSoM	Why 1 Is Not a Prime Number
i4MjYt2WR58	Mystery of Multiplicative Inverse
rDUF6y5LxnM	Variance vs Standard Deviation
r1aKptW7sTk	Why Number 1 is Truly One of a Kind!
8jak2-mDUOY	THE MYSTERY OF ZERO(0)
Tjv15DmbAeY	Mean vs Median : Which is Better
SfiRb_J4tQE	Sat 03
-ByzUDn10_E	SAT 04
fttwUdZlKCg	SAT 05
P1WKiS8TdaE	Math olympiad | Indices ||
FVy5zFowYJk	Math Olympiad | Q46 ||
b8hZE1KAIi8	Math Olympiad |Q47||
I-eKOwOBRVk	SAT 08
dunElZFUaU0	Simultaneous equation | Cramers' rule
XOnG2-eyXXs	Math Olympiad | Q48 ||
5lJxt9Hqhz4	Math Olympiad |Q49||
YXzHAsMM_h0	Math Olympiad|Q50||
AZXNI6MhA_M	SAT 09
pleryIr2ZG8	Math Olympiad | Q51 ||
eP5GjtHCsVo	Math Olympiad 52
41ilU05snZM	Math Olympiad | Q53 ||
AR2Mkj1ccY0	Math Olympiad| 55 ||
kIMNw98XL7E	Math Olympiad| Q56||
q0qxdzjMrPg	SAT 10
n_E-0oy-fR8	Math Olympiad| Q57||
LWrYvlcptoQ	Math Olympiad 58
O7vam0z8rbA	Math Olympiad| Q59 ||
_jBx2nOF7cA	Math Olympiad | Q60 ||
57VBPIDd6X4	Math Olympiad |62 ||
vGyLKDVyj0s	Math Olympiad | 63 ||
umorrS1lQkY	Math Olympiad| 64 ||
bLnvcAL1F-U	May 24, 2023
HuPs6Jw3w_0	Math Olympiad| Q66||
iQfvXhC3_lQ	Math Olympiad| Q67||
bX3s-i_5pHo	May 30, 2023
2s-ci7DOBEs	Math Olympiad |Q69 ||
CWpg86kqiQw	June 7, 2023
_aaR8Q_NgjM	Math Olympiad |Q71 ||
XRp5fpTuMc8	Math Olympiad |Q72||
9-4ofslrgIg	Math Olympiad 62
aF8QU7Dmkbg	Math Olympiad |Q74 ||
IDQ-b1qycFw	Math Olympiad |Q75 ||
YqSv-Egvu1g	Math Olympiad |Q76||
LjOk4gRz7Xw	Math Olympiad |Q77 ||
izMDIgaeT6U	Math Olympiad |Q78||
y3kBvcrPWQg	Math Olympiad |Q80 ||
hlanCb9H3-g	Math Olympiad| Q81|
ATfFDoZkbno	Math short cut 02
5nh1Bv3Fzmw	Math Olympiad |Q86 ||
ZSGhkJCxzMs	Math Olympiad |Q 87||
EJZkRWkL-kU	Math Olympiad 88
hyay2AdDYlc	Math Olympiad| Q89||
wAhktCdmU_8	Math Olympiad|Q91||
gCDa9jswrXw	Math Olympiad|Q92||
Q2y0mLi3Pbk	Math Olympiad|Q93||
mZXsz0_4KFo	Surdindicial expression
RxaQqvZFNgU	|Math Olympiad|| Q96|||
DM4ejq5_ask	Math Olympiad| 107||
CIZX4GVJ9r4	SAT 2023| 11 ||
GYoQmOCqqRE	Math Olympiad|Q111||
Cn0jliP9cCs	Math olympiad |Q123||
NdXTZ6OOS40	Math Olympiad |Q133||
qtkcYP-5oQg	Math Olympiad Q134
MVqmQ82WCT8	Math Olympiad |Q143||
imYL1zwIJso	Otisam Mathematics
RBAjuEmplgg	Controversial property of Indices
a2k7djwYU2k	MO 158
ssBmbzbgi8I	Application of distributive property
HFVCRtXxHC8	Special Indicial equation
RB_ulizYrOM	rem theorem
yvMZg4pRiN0	Maths shortcut |06||
048wINxrHMM	WHY THE CIRCUMFERENCE OF A CIRCLE IS 2πR
Ama0eyJUQ-c	WOW!| PARALLEL LINES IN REAL LIFE APPLICATIONS ||
kQ0m9tRgxfA	How Quadratic Graphs Shape Our World
a3AQ8CfrBKI	How Binomial Expansion Solves Real Life  problem
4zNgEg-6zAU	How Binary Operations Solve Real Life  Problems
p25Q3IRqlqY	Real Life Applications of Functions
0j_gwB5dR_I	How Indices Solve Real Life Problems!
gcJvs_9tohg	How Matrices Shape Our World!
FMN4R_6XcmI	How Quadratic Equation is used in Fashion world
obULRZpErE4	How Venn Diagrams Simplify Real Life!
cWLl3EcN3dw	Composite Functions in Real Life
w9RFNZELNDQ	APPLICATION OF ASSOCIATIVE PROPERTY IN REAL LIFE
LC4RdRZBTEo	Real Numbers in Real Life  Practical
tvp51NdbzR4	Understanding the Identity Element in Math
clYI976xC8M	WHY YOU CANNOT DIVIDE ANY NUMBER BY 0
IkJcdULYQAk	Did the Man Get Cheated on His Pizza
87d8ZpmKfeY	THE MYSTERY OF THE YEAR 2025
PRwO8AFXGMA	PRACTICAL ILLUSTRATION OF CLOSURE PROPERTY
EKar2CtnoVs	Circle Theorem  Real Life Applications
WIEpugd6O8I	COMMUTATIVE PROPERTY IN DATA ANALYSIS
EuftGIVAanc	HOW SET IS APPLIED IN OUR DAILY LIFE WITHOUT KNOWING
lCQg1LAuM7A	Why Any Number Divided by Itself Equals to 1
WmU2gWz9q5c	Why Any Number Raised to the Power of 0  is 1
n885ypJifZo	Why Negative x Negative = Positive
4XfMNgJjLFk	HOW TO USE LINEAR SEQUENCE IN REAL LIFE
wtqcowOYnlE	Why Dividing by Zero Blows Minds!
zKpsSfstQvc	How Linear Equations Solve Real Life
p2rbS9Z6VUg	Mastering Decision Making with Sets!
aUiQZpjfV80	PRACTICAL APPLICATION OF DIFFERENCE OF TWO SQUARES
z2dBmNsuFiY	PRACTICAL DEMONSTRATION OF CAMPASS BEARING
v3TXxGqPOSk	Improper Fractions in Real Life
C7a_NnbHC1M	Why Logarithm of a Number to Its Own Base is 1
k1ZhtlqK1SU	Mutually Exclusive Events in Real Life
QsYzO4-1-1U	Practical demonstration of why logarithm of 1 to any base is 0
ksTb1is9koo	Practical demonstration of direct Variation
3ebtcNYGlx0	UNLOCKING THE SECRET OF THE DOMAIN OF A FUNCTION
CU_y4_Y2RsU	WHY DIVIDING BY A SMALLER GETS BIGGER
R67oL_wtHlk	DO YOU KNOW THAT MULITIPLICATION IS SUCCESSIVE ADDITION
Xrs39YAMWY8	Angles in real life situations
VkPWC5P4NOo	IMPORTANCE  OF PI  IN  REAL LIFE
QOBuLDBxPS8	HOW TO OUTSMART THE BANKS WHEN BORROWING
5Hsz9BQ-CxA	HOW TO GROW YOUR MONEY EXPONENTIALLY
uml9O2ryGq0	DO YOU KNOW THAT YOU CAN USE LINEAR INEQUALITY TO BUDGET?
_RF-gYGW6O8	WHY LOGARITHM OF 1 IS ALWAYS EQUAL TO ZERO!
-J864m4vQVE	PRACTICAL EXPLAINATION OF INJECTIVE FUNCTION
Jw1K3EBJXO4	Why Zero Divided by Zero Isn't One!
grGJkmxzDXM	Practical demonstration of Perpendicular Lines
TSV),
    'live' => $parse(<<<'TSV'
hvjmDvPywSQ	REVISION SHOW
TSV),
];
