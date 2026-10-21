# Lesson plans: feature reference survey (subjectspecificapp.com)

Surveyed 2026-10-20 as a **feature reference only**. Nothing below is plan content; no text from
that site, its apps or MoE/NaCCA documents was copied into Otisam. All Otisam lesson plans are
written from scratch.

## What the site is

A WordPress/WooCommerce hub run by an independent developer. It has four kinds of page:

| Page | URL | What it offers |
|---|---|---|
| SHS app picker | https://subjectspecificapp.com/ | Two dropdowns (Year 1–3, then subject) and a "Go to App" button that opens a Playlab AI app for that year and subject. WhatsApp link for custom plan requests. |
| JHS apps | https://subjectspecificapp.com/jhs | Five cards (English, Maths, Science, Computing, Social Studies), each opening one Playlab app covering B7–B9. Marked as a testing version. |
| Lesson planner shop | https://subjectspecificapp.com/lesson-planners (+ `/page/2/`) | 87 paid WooCommerce products, one per SHS year and subject (GH₵76–120 after discount), sold as editable Word/PDF files with a First/Second Semester variant. |
| Product page | e.g. https://subjectspecificapp.com/year-1-mathematics-lesson-planners-for-shs/ | Marketing description: weeks 1–24 split into two 12-week semesters, list of sections covered, "editable", reviews tab. The plans themselves are behind payment. |

### How a "subject-specific app" is structured (Playlab form)

Each app is a Playlab chat app with a short intake form, then the AI drafts the plan:

- Region (16 regions) and school name
- **Week being planned**: a dropdown of 24 weeks, each labelled "Week N (Section S): topic"
  — so the year is organised as sections (≈ strands) spread over 24 teaching weeks
- Task wanted: learning activities, assessments, differentiation strategies, or reviewing an
  existing plan
- Lessons per week (1–5), lesson length, number of learners

### How their lesson plans are organised (from product descriptions and the app form)

- Per **level → year → subject**, then by **semester** (weeks 1–12, 13–24)
- Each **week** is tied to a **section/strand** and a focus area
- The plan for a week covers: learning activities, assessment, differentiation; NaCCA plans
  conventionally also carry content standard, learning indicators, core competencies,
  a starter/main/reflection lesson flow, resources and teacher reflection

## Otisam design decisions taken from this survey

- Browse **level → year → subject → week**, filterable by strand/topic (our `curriculum_nodes`)
- A plan holds: week, term, lessons per week, duration, class size, strand/sub-strand (linked
  node or free text), content standard, indicators, core competencies, key vocabulary,
  resources, three lesson phases (starter / main / reflection), assessment, differentiation,
  homework and teacher notes
- Teachers write their own plans (no AI generation, no paid downloads); print view doubles
  as "download as PDF" through the browser
- Free to read, like the rest of the public site

## Collected links

### JHS apps (B7–B9, one app per subject)

| Subject | App |
|---|---|
| English Language | https://www.playlab.ai/project/cmsnya1ww0kgakh0wyiltlnum |
| Mathematics | https://www.playlab.ai/project/cmrrxlnio5sisl40wnp8u4e1e |
| Science | https://www.playlab.ai/project/cmso0przz02lsk60wq7kl1ena |
| Computing | https://www.playlab.ai/project/cmrpbcuct0ed2oc0w5iepig1w |
| Social Studies | https://www.playlab.ai/project/cmsp9efhe0exdlc0wzoc9qqfb |

### SHS apps (from the picker's data; prefix every id with `https://www.playlab.ai/project/`)

`SHARED` = the site's own placeholder, one app shared by three Year 1 technical subjects
(`cmd9384ox05zfji0u5hsjvka9`). Music has a Year 3 app only.

| Subject | Year 1 | Year 2 | Year 3 |
|---|---|---|---|
| Accounting | cmekekfba0ab1kr0u69l9m7jf | cmeojcma2036fgk0u4pscv17f | cmmx45e990879jy0wtyebamqv |
| Additional Mathematics | cmd92w06l04rzh10uj4rc3pwv | cme6fmsbj0fbtnk0uz002inv9 | cmluhc0jr0pxxla0uir19mqzs |
| Agricultural Science | cmd932l2s055fh10uudu9v30i | cme6ekrbj0fjxm30uhacwpg5z | cmluid6zh0qyamb0usp9ht12n |
| Agriculture | cmd93523905myji0ultzdspqb | cme6eo5we0fwvm30upgptn669 | cmluigx4y0sl1la0u7epd4jad |
| Arabic | cmd93bnef05j7h10uo7d72ea6 | cme6fxyhl08zdnl0u4l321e4a | cmlv3eqr834r8j20uaotzujhn |
| Art and Design Foundation | cmd93hdgx04v9k60urun1snzm | cme6fztla0984no0uus6zmjmb | cmlv3hdam356jj20u03hw660o |
| Art and Design Studio | cmd93pozj058tk60u1vi7j5n8 | cme6g1wav0fsenk0u51x1fd4i | cmlv3qxsw3my8la0uwfcasut8 |
| Automotive and Metal Technology | SHARED | cme6fpwwh09wik90u10hknruu | cmluim4n60rhnmb0uidu9v3m4 |
| Aviation & Aerospace Engineering | cmdw89v1e0jc6mg0u8em0clf1 | cme6fh5m80cz0m90u6x57eaov | cmlv3syjl3n6nla0uihbswg56 |
| Biology | cmd93v07f06h5ji0uae4zk9lj | cme6fjssk0bbom50u3bk6xwpt | cmn7hf07p18bgkb0vje8oovrs |
| Biomedical Science | cmd93x7ru054oju0uilff0262 | cme6g4b0o09l0no0ukyjuka73 | cmmx48tzc08k8ko0w73oqifc0 |
| Building Construction and Woodwork Technology | SHARED | cme6eri0r09gbk90uguxc6b9e | cmluijfvv0r9mmb0u9f2sf3rc |
| Business Management | cmekep06f0besnb0uuqf606q5 | cmd97ngc20acujq0usfl5z5v1 | cmlv3uyj637i6j20u4andmksc |
| Chemistry | cmd940t7h06ujji0ubvswrck1 | cme6g5xey0aakk90u6r8olibz | cmlv3xgvx37yij20u8a0e3w30 |
| Christian Religious Studies | cmdw9ntmi2h2and0ug3r5stay | cmesm1nji032sgs0vkcasizmi | cmn7hkdmq17sykg0wfleydo5u |
| Clothing and Textiles | cmdw978jx0k83mg0uij7zjef3 | cme6h94e50apwjd0ucc7wjk10 | cmlv3zkcr24ephg0utpggftt9 |
| Computing | cmd9462xe05urjq0u38blwlcq | cme6g8b040bs7m50u2fe3el2q | cmlv42qwc3p28la0uvreo92pd |
| Design and Communication Technology | cmd94caqa07a5ji0uyg6tvivz | cme6gaio409uyjd0uh3we9cz3 | cmlv44nih396wj20u3fis2i63 |
| Economics | cmd94ee7j05skgn0uk2rvfvyg | cme6gikat0cg8m50ubv2or1xz | cmnbk2cgl10jhko0wn7fea08j |
| Electrical and Electronics Technology | SHARED | cme6fv4cg0dcpm90u0p9fk8kr | cmluioocl0t0sla0um6pzh3rn |
| Engineering | cmd94j45c06apjq0u9x0s5vq4 | cme6gk7au0gjtm30ulo63hvmp | cmlv48fru25y7hg0urgcyyk1g |
| English Language | cmdxx258702r9os0ukf1zoqke | cme6glvhl0gvgm30uccc5uu7j | cmlv4aeps3aj7j20u2dxr0slc |
| Food and Nutrition | cmdw9c1rr2i6jm60utaylrnnc | cme6havtb09ypnl0u463nznvq | cmlv4ci5e26o5hg0umpfk0ot5 |
| French | cmdw8k4gv0jowmg0u81kxyyub | cme6goppi09e6o60ut0ai02zb | cmlv4eb5i3rb7la0up25elgl0 |
| General Science | cmd94ugn006q9jq0uu4ntg6y1 | cme6graz50dwzm90ukyuuno5x | cmlv4gpmx3bepj20us8wnl5q7 |
| Geography | cmdw907a32iheo50ujv3ecgl9 | cmdw6fr442g3fnd0u9fffjfso | cmngh2zks1f57fa0w7ol2oled |
| Ghanaian Languages (Asante Twi) | cmdxpx04a2zr2nn0uzjoju8b6 | cme6gv49209kvnl0uyv3ie7rx | cmst2xtbx2np3md0wpcd90lp3 |
| Government | cmd94ypa5065ogy0uidiugvn2 | cme6h714p0ge7nk0uw48xnont | cmlv4lkdy2843hg0u2w6lvv9m |
| History | cmd950t2e05e4ix0u7vezzc72 | cmdxjohhq2uwtno0vln690jg1 | cmlv4oti428h6hg0uqurrwtob |
| Information Communication Technology | cmd952nfd05sdju0uns2559uz | cmekgp6rq0bjkp20u76nao8s4 | cmnd6o2tk1du1jb0wtdeg3324 |
| Islamic Religious Studies | cmdw9yhyd2jldno0viwed8qjo | cmeslwo8n033gki0uztwpzktw | cmnbko4nl11agko0wpqkgp41z |
| Literature in English | cmd954psk065tju0uwu65jc6i | cmekgtiug0b55l50uflwfsy0c | cmlv4r40x3cw3j20u22yh93mz |
| Management in Living | cmekev1070atdll0udetpyfqp | cme6hcu7o0i1rm30udu4jxxlt | cmlv4tgwy3d99j20u6a42dyn8 |
| Manufacturing Engineering | cmd956fi407z6ji0uhfnsy5mg | cmekgvwwq0bnvkr0u15qr23fo | cmlv4vv1h3tyrla0u5y4co7th |
| Mathematics | cmd959ml706q6k60u7e66i89u | cmekh06gb0c0ekr0u8dflebla | cmk7omrgz2vhtgn0uv7dg4q0f |
| Music | — | — | cmlv50e6d3efgj20uxs0oju0z |
| Performing Arts | cmd95ij56065nes0uv3shu976 | cmekh7n4u0cmxmv0u1djjqig9 | cmlv54pkx3f1sj20uchmz8zha |
| Physical Education and Health (Core) | cmd95elao07kmjq0u7tgx043y | cme6gexy30c49m50uxxvx0iu8 | cmlv52c9n3v6lla0us4gf9vjc |
| Physical Education and Health (Elective) | cmd95gm2j06kqju0u8bipvxs5 | cmekh5a6a0blzl50uyxvqdz8j | cmnbkbyhb10wyko0w2fz0f3i2 |
| Physics | cmd95kequ08evji0udoma8669 | cmekh9xfg0bspmd0ujw1h9gqb | cmnbk5pwp10pjko0wpzkicnoo |
| Religious and Moral Education | cmd95mhu107zojq0uuzuj21xi | cmekhch900chmkr0u9rlcv2yl | cmlv57hp03w2wla0uhc0eqtz9 |
| Robotics | cmd95q0a305ytix0uc5fa4dmb | cmekhfdwl0c68ll0uhtf0d6pn | cmnbki6pg38agnl0wn7unsoq9 |
| Social Studies | cmd95sy3u06qzgn0uww395d6c | cmekhi7l30cbsp20ulf2a5p5w | cmlv58w5x2bgphg0umrutrfdd |
| Spanish | cmd95y5j80666i30uzwqno84z | cmekhn11r0bvxm80us6jdjhlr | cmlv5f8q23x9ola0uba93n9uv |

### Paid lesson-planner products (87, all SHS)

Every product URL follows `https://subjectspecificapp.com/year-{N}-{subject-slug}-lesson-planners-for-shs/`
(a few vary: `year-1-physics-lesson-planners-for-shs-nacca-aligned`, `year-2-history-lessons-planners-for-shs`,
`year-2-physics-lessons-planners-for-shs`, `year-2-arts-and-design-*`, `year-1-ict-*`, `year-2-ict-*`,
`year-*-asante-twi-*`, `year-2-building-construction-and-woodwork-*`).

- **Year 1 (28)**: Accounting, Additional Mathematics, Agriculture, Art and Design Foundation,
  Art and Design Studio, Asante Twi, Biology, Business Management, Chemistry, Christian Religious
  Studies, Clothing and Textiles, Computing, Design and Communication Technology, Economics,
  English Language, Food and Nutrition, French, General Science, Geography, Government, History,
  ICT, Literature in English, Management in Living, Mathematics, PE and Health (Elective),
  Physics, Social Studies
- **Year 2 (32)**: Accounting, Additional Mathematics, Agriculture, Arts and Design Foundation,
  Arts and Design Studio, Asante Twi, Biology, Building Construction and Woodwork, Business
  Management, Chemistry, Christian Religious Studies, Clothing and Textiles, Computing, Design
  and Communication Technology, Economics, Electrical and Electronics Technology, Engineering,
  English Language, Food and Nutrition, French, General Science, Geography, Government, History,
  ICT, Islamic Religious Studies, Literature in English, Management in Living, Mathematics,
  PE and Health (Elective), Physics, Social Studies
- **Year 3 (27)**: Accounting, Additional Mathematics, Art and Design Foundation, Art and Design
  Studio, Asante Twi, Automotive and Metal Technology, Biology, Chemistry, Christian Religious
  Studies, Clothing and Textiles, Computing, Economics, Electrical and Electronics Technology,
  English Language, Food and Nutrition, French, General Science, Geography, Government, History,
  ICT, Literature in English, Management in Living, Mathematics, PE and Health (Elective),
  Physics, Social Studies
