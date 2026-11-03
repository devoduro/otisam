<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Student login slips · {{ config('app.name') }}</title>
    {{-- A plain page so it prints the same on any printer: cut along the dashed lines. --}}
    <style>
        * { box-sizing: border-box; }
        body { margin: 16px; font-family: Arial, Helvetica, sans-serif; color: #111; background: #fff; }
        .bar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
        .bar button { padding: 10px 18px; font-size: 15px; font-weight: bold; border: 0; border-radius: 10px; background: #28409e; color: #fff; cursor: pointer; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0; }
        .slip { border: 1px dashed #888; padding: 12px 14px; break-inside: avoid; }
        .slip h2 { margin: 0 0 2px; font-size: 15px; }
        .slip .class { margin: 0 0 8px; font-size: 12px; color: #555; }
        .slip dl { margin: 0; display: grid; grid-template-columns: auto 1fr; gap: 2px 8px; font-size: 13px; }
        .slip dt { color: #555; }
        .slip dd { margin: 0; font-family: "Courier New", monospace; font-weight: bold; }
        .slip .pin { letter-spacing: 3px; font-size: 16px; }
        .slip .where { margin: 8px 0 0; font-size: 11px; color: #555; word-break: break-all; }
        @media print { .bar { display: none; } body { margin: 8mm; } }
    </style>
</head>
<body>
    <div class="bar">
        <p><strong>{{ count($rows) }} login slip(s)</strong> from {{ $teacher->name }}. Cut along the dashed lines and give each student their own slip.</p>
        <button type="button" onclick="window.print()">Print</button>
    </div>
    <div class="grid">
        @foreach ($rows as $row)
            <section class="slip">
                <h2>{{ $row['name'] }}</h2>
                <p class="class">{{ $row['class'] }}</p>
                <dl>
                    @if (! empty($row['email']))<dt>Email</dt><dd>{{ $row['email'] }}</dd>@endif
                    <dt>Username</dt><dd>{{ $row['username'] }}</dd>
                    <dt>PIN</dt><dd class="pin">{{ $row['pin'] }}</dd>
                </dl>
                <p class="where">Sign in at {{ route('login') }}@if (! empty($row['email'])) with your email or username. You will choose your own password the first time.@else with your username. Keep your PIN secret.@endif</p>
            </section>
        @endforeach
    </div>
</body>
</html>
