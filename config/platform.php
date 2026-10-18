<?php

/*
| Platform behaviour that is configuration, not code. Values an administrator should change
| at runtime will move to the settings table as their modules are built.
*/

return [

    'currency' => env('PLATFORM_CURRENCY', 'GHS'),

    'otp' => [
        'ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),
        'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),
        'max_per_hour' => (int) env('OTP_MAX_PER_HOUR', 5),
        'max_per_ip_hour' => (int) env('OTP_MAX_PER_IP_HOUR', 20),
    ],

    // Staff are signed out after this many idle minutes (jhs_portal's SessionTimeout).
    'staff_idle_minutes' => (int) env('STAFF_IDLE_MINUTES', 30),

    // Launch scope is SHS (BLUEPRINT A2). Replaced by the curriculum tables in P2.
    'class_levels' => ['SHS 1', 'SHS 2', 'SHS 3'],

    'programmes' => [
        'General Science', 'General Arts', 'Business', 'Visual Arts', 'Home Economics',
        'Agricultural Science', 'Technical', 'STEM',
    ],

    'regions' => [
        'Ahafo', 'Ashanti', 'Bono', 'Bono East', 'Central', 'Eastern', 'Greater Accra', 'North East',
        'Northern', 'Oti', 'Savannah', 'Upper East', 'Upper West', 'Volta', 'Western', 'Western North',
    ],
];
