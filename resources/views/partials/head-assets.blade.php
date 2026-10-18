{{-- Shared <head> assets for the Tailwind layouts: Poppins (as in jhs_portal), local Font Awesome, compiled CSS/JS. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/admin/vendor/fontawesome/css/all.min.css') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
