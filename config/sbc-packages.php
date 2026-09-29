<?php
/**
 * SERAPH BUILD CONSTRUCTION — SBC Packages.
 *
 * Canonical source of truth for the technical specification of the two
 * turnkey packages. Transcribed from "images/Sbc Package Details.xlsx"
 * and normalised for the web:
 *   - "n" used as "and"            -> "&"
 *   - feet / inch marks            -> &prime; / &Prime;
 *   - Rs. / Rs,                    -> ₹
 *   - straight quotes               -> typographic quotes where quoted
 *   - whitespace + casing tidied
 *
 * Values are written display-ready (HTML entities for measurement marks),
 * matching the existing convention in config/site.php, so they echo raw.
 *
 * 15 sections / 59 specification rows / 2 tiers.
 */

declare(strict_types=1);

return [
    /* ---------------------------------------------------------------
       Package tiers
       --------------------------------------------------------------- */
    'tiers' => [
        'premium' => [
            'key'         => 'premium',
            'name'        => 'SBC Premium Package',
            'short'       => 'Premium',
            'rate'        => '2,300',
            'rate_unit'   => 'per sq.ft',
            'tagline'     => 'Engineered certainty, beautifully finished.',
            'summary'     => 'A complete RCC-framed turnkey home specified end to end — '
                           . 'structural integrity you can measure, and finishes selected for a '
                           . 'twenty-year service life rather than a photo shoot.',
            'highlights'  => [
                'RCC framed structure, foundation designed against your soil test report.',
                '10&prime; floor-to-roof on GF, FF and SF with an 8&prime; clear headroom.',
                '9&Prime; external and 4.5&Prime; internal walls with 3/4&Prime; rough-finish external plaster.',
                '2&prime;&times;2&prime; vitrified flooring throughout at ₹50/sq.ft.',
                'Teak main door with skin shutters and SS handrail at ₹800/Rft.',
                'Parryware bath fittings, Anchor Roma switchgear, L&T three-phase distribution board.',
                '2 coats of Asian Apex exterior emulsion over primer.',
            ],
            'badge'       => 'Most chosen',
            'icon'        => 'fa-shield-halved',
        ],
        'elite' => [
            'key'         => 'elite',
            'name'        => 'SBC Elite Package',
            'short'       => 'Elite',
            'rate'        => '2,900',
            'rate_unit'   => 'per sq.ft',
            'tagline'     => 'Every specification upgraded. Nothing left to chance.',
            'summary'     => 'The Premium build with a decisive specification uplift — wider-format '
                           . 'double-charged tiles, German-profile windows, branded sanitaryware and '
                           . 'a fully tanked, waterproofed envelope.',
            'highlights'  => [
                'Everything in Premium, with a harder specification across every trade.',
                '4&prime;&times;2&prime; double-charged vitrified flooring at ₹120/sq.ft.',
                'Wire-cut bricks, Dalmia cement and Tata Steel / JSW reinforcement.',
                '1st quality teak frames with full teak shutters; Kommerling German-profile UPVC.',
                'Jaquar bath fittings, 3-in-1 wall mixers, Legrand switchgear.',
                '2 coats of Asian Apex Ultima exterior and Premier interior emulsion.',
                'Waterproofed sump and overhead tank; cool-roof white terrace tiles.',
            ],
            'badge'       => 'Highest specification',
            'icon'        => 'fa-gem',
        ],
    ],

    /* ---------------------------------------------------------------
       Document metadata
       --------------------------------------------------------------- */
    'meta' => [
        'title'      => 'SBC Packages',
        'intro'      => 'Two turnkey specifications. Fifty-nine line items. No ambiguity about what '
                      . 'you are paying for. Every brand, grade and thickness below is what our '
                      . 'site teams are contractually required to install.',
        'disclaimer' => 'Indicative specification published for planning purposes. Final scope, brand '
                      . 'substitutions and pricing are confirmed against your approved architectural '
                      . 'drawings, soil test report and site conditions before the contract is signed.',
        'contact_url' => 'contact.php',
        'whatsapp'   => 'https://wa.me/919092557722',
    ],

    /* ---------------------------------------------------------------
       The 15 specification sections
       --------------------------------------------------------------- */
    'sections' => [

        [
            'id'     => 'structure',
            'num'    => 'I',
            'title'  => 'Structure',
            'icon'   => 'fa-helmet-safety',
            'blurb'  => 'The load path of the building — fixed heights, framed construction and a foundation settled by test, not by guesswork.',
            'rows'   => [
                ['Structure type',                                            'RCC framed structure',                                     'RCC framed structure'],
                ['GF height from floor finish to roof top',                    '10&prime; 0&Prime;',                                        '10&prime; 0&Prime;'],
                ['FF height from floor finish to roof top',                    '10&prime; 0&Prime;',                                        '10&prime; 0&Prime;'],
                ['SF height from floor finish to roof top',                    '10&prime; 0&Prime;',                                        '10&prime; 0&Prime;'],
                ['Head room height from floor finish to roof top',             '8&prime; 0&Prime;',                                         '8&prime; 0&Prime;'],
                ['Size of pile / footing, plinth beam, roof slab, column etc.', 'As per approved structural plan',                          'As per approved structural plan'],
                ['Foundation type',                                            'As per soil test report',                                   'As per soil test report'],
            ],
        ],

        [
            'id'     => 'super-structure',
            'num'    => 'II',
            'title'  => 'Super Structure',
            'icon'   => 'fa-trowel-bricks',
            'blurb'  => 'Materials that carry the load and shed the weather — brand-grade cement, certified steel and correct wall thicknesses.',
            'rows'   => [
                ['Bricks type',                                'First quality chamber bricks',                          'Wire cut bricks'],
                ['Cement for concrete works',                  'Coromandel 53 grade / equivalent',                      'Dalmia / equivalent'],
                ['Cement for brickwork &amp; plastering',      'Zuari 53 grade / equivalent',                            'Zuari PPC 53 grade / equivalent'],
                ['Sand for concrete &amp; brick works',        'M-sand, water washed',                                   'River sand, water washed'],
                ['Sand for plastering works',                  'P-sand, fine',                                           'P-sand, fine'],
                ['Steel bars',                                 'Arun TMT 550D / equivalent',                             'Tata Steel / JSW'],
                ['Outer brick wall thickness',                 '9&Prime;',                                                '9&Prime;'],
                ['Inner brick walls thickness',                '4.5&Prime;',                                              '4.5&Prime;'],
                ['Plastering thickness for inner walls',       '1/2&Prime; thick with smooth finish',                    '1/2&Prime; thick with smooth finish'],
                ['Plastering thickness for outer walls',       '3/4&Prime; thick with rough finish',                     '3/4&Prime; thick with rough finish'],
                ['Plastering thickness for ceiling',           '1/2&Prime; thick with smooth finish',                    '1/2&Prime; thick with smooth finish'],
                ['Roof slab thickness',                        '4.5&Prime; / 5&Prime; / 6&Prime; RCC as per approved structural plan', '4.5&Prime; / 5&Prime; / 6&Prime; RCC as per approved structural plan'],
            ],
        ],

        [
            'id'     => 'flooring',
            'num'    => 'III',
            'title'  => 'Flooring &amp; Tiles',
            'icon'   => 'fa-border-all',
            'blurb'  => 'Every horizontal surface, priced line by line — no vague allowance, no &ldquo;standard quality&rdquo; footnotes.',
            'rows'   => [
                ['All bedrooms, kitchen, living room &amp; lobby floor', "2&prime;&times;2&prime; vitrified floor tiles @ ₹50/sq.ft",      "4&prime;&times;2&prime; double charged vitrified floor tiles @ ₹120/sq.ft"],
                ['Kitchen wall dadoing up to 2&prime; height',           '15&Prime;&times;10&Prime; digital tiles @ ₹40/sq.ft',         '24&Prime;&times;12&Prime; digital tiles @ ₹60/sq.ft'],
                ['All bathroom floors',                                   "1&prime;&times;1&prime; antiskid tiles @ ₹40/sq.ft",          "1&prime;&times;1&prime; antiskid tiles @ ₹60/sq.ft"],
                ['Bathroom wall tiles up to 7&prime; height',             '15&Prime;&times;10&Prime; digital tiles @ ₹40/sq.ft',         '24&Prime;&times;12&Prime; digital tiles @ ₹60/sq.ft'],
                ['Car parking',                                           "1&prime;&times;1&prime; car parking tiles @ ₹45/sq.ft",       "1&prime;&times;1&prime; car parking tiles @ ₹75/sq.ft"],
                ['Staircase steps',                                       'Raw granite @ ₹120/sq.ft for tread only, or antiskid tiles @ ₹40/sq.ft for tread &amp; riser', 'Polished granite @ ₹120/sq.ft for tread &amp; riser'],
                ['Kitchen platform',                                      "2&prime; 0&Prime; wide G20 black granite @ ₹140/sq.ft",       "2&prime; 0&Prime; wide black granite @ ₹180/sq.ft"],
            ],
        ],

        [
            'id'     => 'carpentry',
            'num'    => 'IV',
            'title'  => 'Carpentry',
            'icon'   => 'fa-door-open',
            'blurb'  => 'Doors, windows, grills and hardware — the joinery you touch every single day of your ownership.',
            'rows'   => [
                ['Main door',                                 "3&prime;6&Prime;&times;7&prime;0&Prime; teak wood frame with skin shutters", "3&prime;6&Prime;&times;7&prime;0&Prime; 1st quality teak wood frame with teak wood shutters"],
                ['Bedroom doors',                             "3&prime;0&Prime;&times;7&prime;0&Prime; teak wood frame with flush type shutters (oval design)", "3&prime;0&Prime;&times;7&prime;0&Prime; 1st quality teak wood frame with flush type designed shutters"],
                ['Balcony, service, headroom &amp; bathroom doors', "2&prime;6&Prime;&times;7&prime;0&Prime; teak wood frame with film coated flush type shutters (plain design)", "2&prime;6&Prime;&times;7&prime;0&Prime; WPC frame with waterproof FRP doors"],
                ['Windows &amp; ventilators',                 'UPVC with glazed shutters — 1 no. window for each room &amp; 1 no. ventilator for each bathroom, as per finalised plan', 'UPVC with glazed shutters, Kommerling (German profile), as per the quantity given in the architect working plan'],
                ['Window grills',                             '10 mm square MS grills, standard design @ 1.5 kg/sq.ft',    '12 mm square MS grills, standard design @ 2 kg/sq.ft avg.'],
                ['Staircase hand rail',                       'SS handrail @ ₹800/Rft',                                   'SS handrail @ ₹900/Rft'],
                ['Main door fittings',                         'Godrej (basic) lock with brass fittings, minimum 3 sets of keys, standard door stopper, magic eye, chain link &amp; 2 no. brass tower bolts', 'Godrej lock with brass fittings and minimum 3 sets of keys, brass coated magic eye, chain link, 12&Prime; handle on both sides, brass tower bolts (2 no.), brass AL trap &amp; door magnet'],
                ['Bedroom door fittings',                      'Trishul / equivalent locks with anodized aluminium fittings, standard door stopper &amp; 2 no. SS tower bolts', 'Godrej / equivalent locks with anodized aluminium fittings, standard door stopper, tower bolt &amp; AL trap'],
                ['Bathroom door fittings',                    'Trishul / equivalent handle with anodized aluminium fittings, 2 no. SS tower bolts', 'Godrej / equivalent keyless locks with anodized aluminium fittings, standard door stopper, tower bolt &amp; AL trap'],
                ['Other door fittings',                        'SS AL trap with standard door stopper &amp; 2 no. SS tower bolts', 'Godrej / equivalent keyless locks with anodized aluminium fittings, standard door stopper, tower bolt &amp; AL trap'],
            ],
        ],

        [
            'id'     => 'electrical',
            'num'    => 'V',
            'title'  => 'Electrical',
            'icon'   => 'fa-bolt',
            'blurb'  => 'Concealed copper throughout, inverter-ready, and pre-provisioned for the way you actually live.',
            'rows'   => [
                ['Wiring',                  'Concealed wiring with copper wires of Polycab / equivalent brand, conforming to ISI specifications', 'Concealed wiring with copper wires, Finolex / equivalent brand, conforming to ISI specifications'],
                ['PVC pipes',               'Finolex / equivalent',                                       'Finolex / equivalent'],
                ['Switches &amp; other fittings', 'Anchor Roma / equivalent',                                'Legrand / equivalent'],
                ['3 phase DB box',          'L&amp;T / equivalent',                                       'L&amp;T / equivalent'],
                ['Inverter wiring',         'Provided at appropriate locations',                         'Provided at appropriate locations'],
                ['Other provisions',        'Home theatre provision, spotlight provision, TV cable point in living room, AC points in bedroom only, washing machine, water purifier &amp; refrigerator provision', 'Home theatre provision and spotlight provision in living room, AC points in living and all bedrooms, TV cable point at living hall and all bedrooms, washing machine, water purifier, computer &amp; refrigerator provision'],
            ],
        ],

        [
            'id'     => 'plumbing',
            'num'    => 'VI',
            'title'  => 'Plumbing',
            'icon'   => 'fa-faucet',
            'blurb'  => 'Branded CPVC lines and full bathroom fit-out, delivered wet-tested before handover.',
            'rows'   => [
                ['CPVC pipes',                 'Ashirwad / equivalent brand CPVC pipes for concealed lines and Finolex / equivalent brand for outer lines', 'Ashirwad / equivalent brand CPVC pipes for concealed lines and outer lines'],
                ['Fittings per bathroom',      'Parryware make wash basin, pillar cock, 2-in-1 wall mixer, angle cock, closet EWC / IWC with health faucet, geyser provision', 'Jaquar make wash basin, pillar cock, 3-in-1 wall mixer, angle cock, closet EWC / IWC with health faucet, geyser provision'],
                ['Kitchen sink',               'SS sink with Parryware make sink cock',                 'Double bowl Parryware SS sink with 2 no. Jaquar make sink cocks'],
                ['Other provisions',           'Washing machine inlet &amp; outlet line, provision for water purifier in kitchen, tap provisions at terrace, car parking &amp; service area', 'Washing machine inlet &amp; outlet line, provision for water purifier in kitchen, tap provisions at terrace, car parking &amp; service area'],
            ],
        ],

        [
            'id'     => 'lofts-wardrobes',
            'num'    => 'VII',
            'title'  => 'Lofts &amp; Wardrobes',
            'icon'   => 'fa-couch',
            'blurb'  => 'Storage built into the structure in every bedroom and kitchen — no shutters, no added cost later.',
            'rows'   => [
                ['Loft',     'Each bedroom and kitchen will be provided with one loft at one side of the room (shorter span), without any shutters for covering', 'Each bedroom and kitchen will be provided with one loft at one side of the room (shorter span), without any shutters for covering'],
                ['Wardrobe', 'Each bedroom will be provided with Cudappah stone (cupboards) without shutters', 'Each bedroom will be provided with granite stone (cupboards) without shutters'],
            ],
        ],

        [
            'id'     => 'painting',
            'num'    => 'VIII',
            'title'  => 'Painting',
            'icon'   => 'fa-paint-roller',
            'blurb'  => 'A full coating system, not a single pass of colour — wash, putty, primer, emulsion, in that order.',
            'rows'   => [
                ['Wooden joineries &amp; MS grills', '2 coats of enamel paint, Asian make', '2 coats of matt finish enamel paint, Asian make'],
                ['Main door &amp; wooden handrail',   '3 coats of varnish, Asian make',   '3 coats of varnish, Asian make'],
                ['Inner walls',                       '1 coat whitewash, 2 coats of Asian cement based putty, 1 coat primer &amp; 2 coats of Asian Tractor emulsion paint', '1 coat whitewash, 2 coats of Asian cement based putty, 1 coat primer &amp; 2 coats of Asian Premier emulsion paint'],
                ['Outer walls',                       '1 coat whitewash, 1 coat primer &amp; 2 coats Asian Ace exterior emulsion', '1 coat whitewash, 1 coat primer &amp; 2 coats Asian Apex Ultima exterior emulsion'],
            ],
        ],

        [
            'id'     => 'terrace',
            'num'    => 'IX',
            'title'  => 'Terrace',
            'icon'   => 'fa-umbrella-beach',
            'blurb'  => 'The roof is a living space — protected, finished and usable.',
            'rows'   => [
                ['Terrace finish', 'Weathering course of brick jelly and lime with cement mortar 1:6, with laying of roof top red tile (or) cool roof white tile, as per finalised quote', '2 coats of waterproof application &amp; cool roof tiles (white)'],
            ],
        ],

        [
            'id'     => 'underground-sump',
            'num'    => 'X',
            'title'  => 'Underground Sump',
            'icon'   => 'fa-water',
            'blurb'  => 'Water storage built as a structural element, not a lined pit.',
            'rows'   => [
                ['Underground sump', 'RCC double mat structure as per structural drawing', 'RCC double mat structure as per structural drawing with 1 coat waterproof application'],
            ],
        ],

        [
            'id'     => 'septic-tank',
            'num'    => 'XI',
            'title'  => 'Septic Tank',
            'icon'   => 'fa-vial',
            'blurb'  => 'Sized and detailed on the structural drawing you sign off.',
            'rows'   => [
                ['Septic tank', 'RCC / brickwork structure as per structural drawing', 'RCC / brick wall as per structural drawing'],
            ],
        ],

        [
            'id'     => 'overhead-tank',
            'num'    => 'XII',
            'title'  => 'Overhead Tank',
            'icon'   => 'fa-tower-broadcast',
            'blurb'  => 'Restrained, watertight and safe.',
            'rows'   => [
                ['Overhead tank', 'Brick wall with 2 coats of waterproof / Sintex PVC', 'RCC bottom &amp; top slab with brick wall &amp; 2 coats of waterproof'],
            ],
        ],

        [
            'id'     => 'basement-height',
            'num'    => 'XIII',
            'title'  => 'Basement Height',
            'icon'   => 'fa-arrows-down-to-line',
            'blurb'  => 'Levels are set against the road so the site never fights back.',
            'rows'   => [
                ['Basement height', 'Car park level 1.5&prime; high from road level &amp; building basement height 3&prime; from road level', 'Car park level 1.5&prime; high from road level &amp; building basement height 3&prime; from road level'],
            ],
        ],

        [
            'id'     => 'compound-wall',
            'num'    => 'XIV',
            'title'  => 'Compound Wall',
            'icon'   => 'fa-road-barrier',
            'blurb'  => 'Piled, beamed, plastered and painted on both faces — a boundary that holds.',
            'rows'   => [
                ['Compound wall', '9&Prime; dia &times; 6 feet depth pile for every 8 feet inter distance, interconnected by 9&Prime;&times;12&Prime; plinth beam, 4.5&Prime; red brick wall with plastering &amp; painting on both sides for 5 feet height from ground / road level', '9&Prime; dia &times; 6 feet depth pile for every 8 feet inter distance, interconnected by 9&Prime;&times;12&Prime; plinth beam, 4.5&Prime; red brick wall with plastering &amp; painting on both sides for 5 feet height from ground / road level'],
            ],
        ],

        [
            'id'     => 'car-ramp',
            'num'    => 'XV',
            'title'  => 'Car Ramp',
            'icon'   => 'fa-road',
            'blurb'  => 'A finished approach that takes the weight of the car and the rain.',
            'rows'   => [
                ['Car ramp', 'PCC finish ramp work with side brick wall, filling &amp; consolidation', 'PCC finish ramp work with side brick wall, filling &amp; consolidation'],
            ],
        ],

    ],
];
