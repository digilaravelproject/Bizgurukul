<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BizGurukul Pro') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-900 antialiased relative min-h-screen flex flex-col items-center justify-center pt-6 sm:pt-0">

    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-3xl opacity-50">
        </div>
    </div>

    <div class="mb-6 transition transform hover:scale-105 duration-300">
        <a href="/" class="flex items-center gap-2">
            <div
                class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                B
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-slate-800">
                BIZ<span class="text-indigo-600">GURUKUL</span>
            </span>
        </a>
    </div>

    <div class="w-full sm:max-w-lg px-6 py-8 glass-panel rounded-2xl shadow-xl">
        {{ $slot }}
    </div>

    <div class="mt-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} BizGurukul Pro. Secure & Professional.
    </div>
</body>

</html>
