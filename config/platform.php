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

    // Classes, programmes and subjects are edited in Admin → Curriculum (database), not here.

    'library' => [
        'disk' => 'library',
        // Keep below the host's upload_max_filesize / post_max_size (cPanel → MultiPHP INI Editor).
        'max_upload_mb' => (int) env('LIBRARY_MAX_UPLOAD_MB', 25),
        // Extension => MIME types finfo may report for it. Macro-enabled Office files, HTML, SVG and
        // executables are deliberately absent.
        'allowed' => [
            'pdf' => ['application/pdf'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
            'png' => ['image/png'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'webp' => ['image/webp'],
        ],
        // Default number of pages people without full access can see (overridable per resource
        // and in Admin → Library settings).
        'preview_pages' => (int) env('LIBRARY_PREVIEW_PAGES', 2),
        // Guests may read free documents in the browser but must sign in to download.
        'guest_downloads' => false,
    ],

    'regions' => [
        'Ahafo', 'Ashanti', 'Bono', 'Bono East', 'Central', 'Eastern', 'Greater Accra', 'North East',
        'Northern', 'Oti', 'Savannah', 'Upper East', 'Upper West', 'Volta', 'Western', 'Western North',
    ],
    // YouTube channels whose uploads become video lessons (php artisan videos:import-youtube).
    // Videos whose title mentions an elective_keywords word are filed under elective_subject.
    'video_channels' => [
        'otisam-maths' => [
            'name' => 'Otisam Mathematics',
            'channel_id' => 'UCoXMXPxUrIJyjPAQp489Byg',
            'url' => 'https://www.youtube.com/@otisammathematics9887',
            'subject' => 'core-mathematics',
            'elective_subject' => 'elective-mathematics',
            'elective_keywords' => ['differentiat', 'derivative', 'first principle', 'product rule', 'quotient rule', 'chain rule', 'integrat', 'limit', 'matri', 'determinant', 'binomial', 'permutation', 'combination', 'complex number', 'calculus'],
            'access_level' => 'free',
        ],
    ],

    // Community Q&A: a post is hidden for review once this many people have reported it.
    'qa' => [
        'hide_after_reports' => (int) env('QA_HIDE_AFTER_REPORTS', 3),
    ],
];
