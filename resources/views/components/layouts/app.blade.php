<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Repertorio' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles

    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: radial-gradient(circle at top left, #e2f7ef 0%, #f5f8ff 40%, #ffffff 100%);
        }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <header class="border-b border-slate-200/70 bg-white/70 backdrop-blur">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold tracking-tight">Repertorio</a>

            @auth
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100" type="submit">
                            Sair
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
