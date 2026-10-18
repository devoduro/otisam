{{-- Shared <head> assets for the Tailwind layouts: Inter + Plus Jakarta Sans (as in castvote), local Font Awesome, compiled CSS/JS. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/admin/vendor/fontawesome/css/all.min.css') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
