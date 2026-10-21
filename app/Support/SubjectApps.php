<?php

namespace App\Support;

/**
 * Links to the free AI lesson-planning apps published by Subject Specific App
 * (https://subjectspecificapp.com), one per level, year and subject. The apps run on Playlab and
 * belong to their developer; Otisam only links to them. Collected 2026-10-20 from their site's app
 * picker; see docs/LESSON_PLANS_REFERENCE.md. Re-check the links when updating.
 */
class SubjectApps
{
    public const SOURCE = 'https://subjectspecificapp.com';

    public const BASE = 'https://www.playlab.ai/project/';

    /** Their Year 1 placeholder: one app for three technical subjects until they split it. */
    public const SHARED = 'cmd9384ox05zfji0u5hsjvka9';

    /** One app per JHS subject, covering JHS 1-3 (B7-B9). */
    public const JHS = [
        'English Language' => 'cmsnya1ww0kgakh0wyiltlnum',
        'Mathematics' => 'cmrrxlnio5sisl40wnp8u4e1e',
        'Science' => 'cmso0przz02lsk60wq7kl1ena',
        'Computing' => 'cmrpbcuct0ed2oc0w5iepig1w',
        'Social Studies' => 'cmsp9efhe0exdlc0wzoc9qqfb',
    ];

    /** SHS: year => subject => app id. */
    public const SHS = [
        1 => [
            'Accounting' => 'cmekekfba0ab1kr0u69l9m7jf',
            'Additional Mathematics' => 'cmd92w06l04rzh10uj4rc3pwv',
            'Agricultural Science' => 'cmd932l2s055fh10uudu9v30i',
            'Agriculture' => 'cmd93523905myji0ultzdspqb',
            'Arabic' => 'cmd93bnef05j7h10uo7d72ea6',
            'Art and Design Foundation' => 'cmd93hdgx04v9k60urun1snzm',
            'Art and Design Studio' => 'cmd93pozj058tk60u1vi7j5n8',
            'Automotive and Metal Technology' => self::SHARED,
            'Aviation & Aerospace Engineering' => 'cmdw89v1e0jc6mg0u8em0clf1',
            'Biology' => 'cmd93v07f06h5ji0uae4zk9lj',
            'Biomedical Science' => 'cmd93x7ru054oju0uilff0262',
            'Building Construction and Woodwork Technology' => self::SHARED,
            'Business Management' => 'cmekep06f0besnb0uuqf606q5',
            'Chemistry' => 'cmd940t7h06ujji0ubvswrck1',
            'Christian Religious Studies' => 'cmdw9ntmi2h2and0ug3r5stay',
            'Clothing and Textiles' => 'cmdw978jx0k83mg0uij7zjef3',
            'Computing' => 'cmd9462xe05urjq0u38blwlcq',
            'Design and Communication Technology' => 'cmd94caqa07a5ji0uyg6tvivz',
            'Economics' => 'cmd94ee7j05skgn0uk2rvfvyg',
            'Electrical and Electronics Technology' => self::SHARED,
            'Engineering' => 'cmd94j45c06apjq0u9x0s5vq4',
            'English Language' => 'cmdxx258702r9os0ukf1zoqke',
            'Food and Nutrition' => 'cmdw9c1rr2i6jm60utaylrnnc',
            'French' => 'cmdw8k4gv0jowmg0u81kxyyub',
            'General Science' => 'cmd94ugn006q9jq0uu4ntg6y1',
            'Geography' => 'cmdw907a32iheo50ujv3ecgl9',
            'Ghanaian Languages (Asante Twi)' => 'cmdxpx04a2zr2nn0uzjoju8b6',
            'Government' => 'cmd94ypa5065ogy0uidiugvn2',
            'History' => 'cmd950t2e05e4ix0u7vezzc72',
            'Information Communication Technology' => 'cmd952nfd05sdju0uns2559uz',
            'Islamic Religious Studies' => 'cmdw9yhyd2jldno0viwed8qjo',
            'Literature in English' => 'cmd954psk065tju0uwu65jc6i',
            'Management in Living' => 'cmekev1070atdll0udetpyfqp',
            'Manufacturing Engineering' => 'cmd956fi407z6ji0uhfnsy5mg',
            'Mathematics' => 'cmd959ml706q6k60u7e66i89u',
            'Performing Arts' => 'cmd95ij56065nes0uv3shu976',
            'Physical Education and Health (Core)' => 'cmd95elao07kmjq0u7tgx043y',
            'Physical Education and Health (Elective)' => 'cmd95gm2j06kqju0u8bipvxs5',
            'Physics' => 'cmd95kequ08evji0udoma8669',
            'Religious and Moral Education' => 'cmd95mhu107zojq0uuzuj21xi',
            'Robotics' => 'cmd95q0a305ytix0uc5fa4dmb',
            'Social Studies' => 'cmd95sy3u06qzgn0uww395d6c',
            'Spanish' => 'cmd95y5j80666i30uzwqno84z',
        ],
        2 => [
            'Accounting' => 'cmeojcma2036fgk0u4pscv17f',
            'Additional Mathematics' => 'cme6fmsbj0fbtnk0uz002inv9',
            'Agricultural Science' => 'cme6ekrbj0fjxm30uhacwpg5z',
            'Agriculture' => 'cme6eo5we0fwvm30upgptn669',
            'Arabic' => 'cme6fxyhl08zdnl0u4l321e4a',
            'Art and Design Foundation' => 'cme6fztla0984no0uus6zmjmb',
            'Art and Design Studio' => 'cme6g1wav0fsenk0u51x1fd4i',
            'Automotive and Metal Technology' => 'cme6fpwwh09wik90u10hknruu',
            'Aviation & Aerospace Engineering' => 'cme6fh5m80cz0m90u6x57eaov',
            'Biology' => 'cme6fjssk0bbom50u3bk6xwpt',
            'Biomedical Science' => 'cme6g4b0o09l0no0ukyjuka73',
            'Building Construction and Woodwork Technology' => 'cme6eri0r09gbk90uguxc6b9e',
            'Business Management' => 'cmd97ngc20acujq0usfl5z5v1',
            'Chemistry' => 'cme6g5xey0aakk90u6r8olibz',
            'Christian Religious Studies' => 'cmesm1nji032sgs0vkcasizmi',
            'Clothing and Textiles' => 'cme6h94e50apwjd0ucc7wjk10',
            'Computing' => 'cme6g8b040bs7m50u2fe3el2q',
            'Design and Communication Technology' => 'cme6gaio409uyjd0uh3we9cz3',
            'Economics' => 'cme6gikat0cg8m50ubv2or1xz',
            'Electrical and Electronics Technology' => 'cme6fv4cg0dcpm90u0p9fk8kr',
            'Engineering' => 'cme6gk7au0gjtm30ulo63hvmp',
            'English Language' => 'cme6glvhl0gvgm30uccc5uu7j',
            'Food and Nutrition' => 'cme6havtb09ypnl0u463nznvq',
            'French' => 'cme6goppi09e6o60ut0ai02zb',
            'General Science' => 'cme6graz50dwzm90ukyuuno5x',
            'Geography' => 'cmdw6fr442g3fnd0u9fffjfso',
            'Ghanaian Languages (Asante Twi)' => 'cme6gv49209kvnl0uyv3ie7rx',
            'Government' => 'cme6h714p0ge7nk0uw48xnont',
            'History' => 'cmdxjohhq2uwtno0vln690jg1',
            'Information Communication Technology' => 'cmekgp6rq0bjkp20u76nao8s4',
            'Islamic Religious Studies' => 'cmeslwo8n033gki0uztwpzktw',
            'Literature in English' => 'cmekgtiug0b55l50uflwfsy0c',
            'Management in Living' => 'cme6hcu7o0i1rm30udu4jxxlt',
            'Manufacturing Engineering' => 'cmekgvwwq0bnvkr0u15qr23fo',
            'Mathematics' => 'cmekh06gb0c0ekr0u8dflebla',
            'Performing Arts' => 'cmekh7n4u0cmxmv0u1djjqig9',
            'Physical Education and Health (Core)' => 'cme6gexy30c49m50uxxvx0iu8',
            'Physical Education and Health (Elective)' => 'cmekh5a6a0blzl50uyxvqdz8j',
            'Physics' => 'cmekh9xfg0bspmd0ujw1h9gqb',
            'Religious and Moral Education' => 'cmekhch900chmkr0u9rlcv2yl',
            'Robotics' => 'cmekhfdwl0c68ll0uhtf0d6pn',
            'Social Studies' => 'cmekhi7l30cbsp20ulf2a5p5w',
            'Spanish' => 'cmekhn11r0bvxm80us6jdjhlr',
        ],
        3 => [
            'Accounting' => 'cmmx45e990879jy0wtyebamqv',
            'Additional Mathematics' => 'cmluhc0jr0pxxla0uir19mqzs',
            'Agricultural Science' => 'cmluid6zh0qyamb0usp9ht12n',
            'Agriculture' => 'cmluigx4y0sl1la0u7epd4jad',
            'Arabic' => 'cmlv3eqr834r8j20uaotzujhn',
            'Art and Design Foundation' => 'cmlv3hdam356jj20u03hw660o',
            'Art and Design Studio' => 'cmlv3qxsw3my8la0uwfcasut8',
            'Automotive and Metal Technology' => 'cmluim4n60rhnmb0uidu9v3m4',
            'Aviation & Aerospace Engineering' => 'cmlv3syjl3n6nla0uihbswg56',
            'Biology' => 'cmn7hf07p18bgkb0vje8oovrs',
            'Biomedical Science' => 'cmmx48tzc08k8ko0w73oqifc0',
            'Building Construction and Woodwork Technology' => 'cmluijfvv0r9mmb0u9f2sf3rc',
            'Business Management' => 'cmlv3uyj637i6j20u4andmksc',
            'Chemistry' => 'cmlv3xgvx37yij20u8a0e3w30',
            'Christian Religious Studies' => 'cmn7hkdmq17sykg0wfleydo5u',
            'Clothing and Textiles' => 'cmlv3zkcr24ephg0utpggftt9',
            'Computing' => 'cmlv42qwc3p28la0uvreo92pd',
            'Design and Communication Technology' => 'cmlv44nih396wj20u3fis2i63',
            'Economics' => 'cmnbk2cgl10jhko0wn7fea08j',
            'Electrical and Electronics Technology' => 'cmluioocl0t0sla0um6pzh3rn',
            'Engineering' => 'cmlv48fru25y7hg0urgcyyk1g',
            'English Language' => 'cmlv4aeps3aj7j20u2dxr0slc',
            'Food and Nutrition' => 'cmlv4ci5e26o5hg0umpfk0ot5',
            'French' => 'cmlv4eb5i3rb7la0up25elgl0',
            'General Science' => 'cmlv4gpmx3bepj20us8wnl5q7',
            'Geography' => 'cmngh2zks1f57fa0w7ol2oled',
            'Ghanaian Languages (Asante Twi)' => 'cmst2xtbx2np3md0wpcd90lp3',
            'Government' => 'cmlv4lkdy2843hg0u2w6lvv9m',
            'History' => 'cmlv4oti428h6hg0uqurrwtob',
            'Information Communication Technology' => 'cmnd6o2tk1du1jb0wtdeg3324',
            'Islamic Religious Studies' => 'cmnbko4nl11agko0wpqkgp41z',
            'Literature in English' => 'cmlv4r40x3cw3j20u22yh93mz',
            'Management in Living' => 'cmlv4tgwy3d99j20u6a42dyn8',
            'Manufacturing Engineering' => 'cmlv4vv1h3tyrla0u5y4co7th',
            'Mathematics' => 'cmk7omrgz2vhtgn0uv7dg4q0f',
            'Music' => 'cmlv50e6d3efgj20uxs0oju0z',
            'Performing Arts' => 'cmlv54pkx3f1sj20uchmz8zha',
            'Physical Education and Health (Core)' => 'cmlv52c9n3v6lla0us4gf9vjc',
            'Physical Education and Health (Elective)' => 'cmnbkbyhb10wyko0w2fz0f3i2',
            'Physics' => 'cmnbk5pwp10pjko0wpzkicnoo',
            'Religious and Moral Education' => 'cmlv57hp03w2wla0uhc0eqtz9',
            'Robotics' => 'cmnbki6pg38agnl0wn7unsoq9',
            'Social Studies' => 'cmlv58w5x2bgphg0umrutrfdd',
            'Spanish' => 'cmlv5f8q23x9ola0uba93n9uv',
        ],
    ];

    public static function url(string $id): string
    {
        return self::BASE.$id;
    }

    /**
     * Every app as a flat list for the page: level, year (null = all JHS years), subject, url.
     *
     * @return array<int, array{level: string, year: ?int, subject: string, url: string, shared: bool}>
     */
    public static function all(): array
    {
        $apps = [];
        foreach (self::JHS as $subject => $id) {
            $apps[] = ['level' => 'JHS', 'year' => null, 'subject' => $subject, 'url' => self::url($id), 'shared' => false];
        }
        foreach (self::SHS as $year => $subjects) {
            foreach ($subjects as $subject => $id) {
                $apps[] = ['level' => 'SHS', 'year' => $year, 'subject' => $subject, 'url' => self::url($id), 'shared' => $id === self::SHARED];
            }
        }

        return $apps;
    }
}
