<?php
/**
 * SERAPH BUILD CONSTRUCTION — Site configuration.
 * Central place for site-wide data used across partials.
 */

return [
    'name'     => 'SERAPH BUILD CONSTRUCTION',
    'tagline'  => 'Premium Luxury Architecture & Design',
    'since'    => '2005',
    'email'    => 'seraphbuildconstruction@gmail.com',
    'phone'    => '+91 90925 57722',
    'phone_tel' => '+919092557722',
    'address'  => '715-A, 7th Floor, Spencer Plaza, Anna Salai, Chennai &ndash; 600002',

    'nav' => [
        'hero'        => 'Home',
        'interior'    => 'Interiors',
        'homeplan'    => 'Home Plan',
        'materials'   => 'Materials',
        'services'    => 'Services',
        'projects'    => 'Projects',
        'about'       => 'About',
        'testimonials'=> 'Testimonials',
    ],

    'contact_url'  => 'contact.php',
    'projects_url' => 'projects.php',

    'social' => [
        ['url' => 'https://facebook.com',    'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
        ['url' => 'https://instagram.com',   'icon' => 'fa-instagram',   'label' => 'Instagram'],
        ['url' => 'https://linkedin.com',    'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'],
        ['url' => 'https://youtube.com',     'icon' => 'fa-youtube',     'label' => 'YouTube'],
    ],
];