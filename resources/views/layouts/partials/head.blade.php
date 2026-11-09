<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ isset($title) && trim($title) !== '' ? trim($title).' · ' : '' }}{{ config('app.name') }}</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='6' fill='%235046e5'/%3E%3Crect x='4.5' y='5' width='4' height='14' rx='1.4' fill='%23fff' opacity='.6'/%3E%3Crect x='10' y='5' width='4' height='10' rx='1.4' fill='%23fff' opacity='.85'/%3E%3Crect x='15.5' y='5' width='4' height='6' rx='1.4' fill='%23fbbf24'/%3E%3C/svg%3E">

{{-- Aplica o tema salvo antes da renderização, evitando o "flash" de tema claro. --}}
<script>
    (function () {
        try {
            var saved = localStorage.getItem('theme');
            var dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) document.documentElement.classList.add('dark');
        } catch (e) {}
    })();
</script>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
