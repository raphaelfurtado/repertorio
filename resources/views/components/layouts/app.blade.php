<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Repertorio' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles

    <style>
        :root {
            --rp-primary: #5c6bc0;
            --rp-secondary: #64748b;
            --rp-accent: #fbbf24;
            --rp-bg: #0f172a;
            --rp-bg-soft: #172036;
            --rp-panel: #1b2438;
            --rp-panel-strong: #111b2f;
            --rp-border: rgba(148, 163, 184, 0.24);
            --rp-text: #e2e8f0;
            --rp-text-muted: #94a3b8;
        }

        body {
            font-family: 'Sora', sans-serif;
            color: var(--rp-text);
            background:
                radial-gradient(1000px 600px at 12% -4%, rgba(92, 107, 192, 0.20), transparent 65%),
                radial-gradient(800px 580px at 100% 0, rgba(251, 191, 36, 0.09), transparent 72%),
                linear-gradient(180deg, #0a1224 0%, #0f172a 45%, #0b1324 100%);
            min-height: 100vh;
        }

        .rp-app-shell {
            position: relative;
            isolation: isolate;
        }

        .rp-app-shell::before {
            content: "";
            position: fixed;
            inset: -240px -100px auto -160px;
            height: 720px;
            background: radial-gradient(circle, rgba(92, 107, 192, 0.22), transparent 68%);
            filter: blur(8px);
            z-index: -1;
            pointer-events: none;
        }

        .rp-glass {
            background: linear-gradient(140deg, rgba(27, 36, 56, 0.95), rgba(17, 27, 47, 0.90));
            border: 1px solid var(--rp-border);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06), 0 24px 60px rgba(2, 6, 23, 0.45);
        }

        .rp-card {
            background: linear-gradient(160deg, rgba(27, 36, 56, 0.92), rgba(15, 23, 42, 0.86));
            border: 1px solid var(--rp-border);
            box-shadow: 0 18px 45px rgba(2, 6, 23, 0.34);
        }

        .rp-btn-primary {
            background: linear-gradient(140deg, #727fcb, #5c6bc0);
            border: 1px solid rgba(196, 209, 255, 0.30);
            color: #eef2ff;
        }

        .rp-btn-primary:hover {
            background: linear-gradient(140deg, #7f8ad1, #6b79ca);
        }

        .rp-btn-muted {
            background: rgba(100, 116, 139, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.26);
            color: #dbe7ff;
        }

        .rp-btn-muted:hover {
            background: rgba(100, 116, 139, 0.2);
        }

        .rp-input {
            background: rgba(2, 6, 23, 0.48);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: #e2e8f0;
        }

        .rp-input:focus {
            outline: none;
            border-color: rgba(129, 140, 248, 0.8);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        .rp-label {
            color: var(--rp-text-muted);
            font-size: 0.71rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .rp-dot-grid {
            background-image: radial-gradient(circle at center, rgba(148, 163, 184, 0.18) 1.15px, transparent 1.15px);
            background-size: 16px 16px;
            opacity: 0.13;
        }

        .rp-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .rp-scroll::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.6);
            border-radius: 9999px;
        }

        .rp-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.35);
        }

        @keyframes rpFadeUp {
            from {
                transform: translateY(14px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .rp-animate {
            animation: rpFadeUp 0.5s ease-out both;
        }
    </style>
</head>
<body class="min-h-screen antialiased">
    <div class="rp-app-shell">
        @auth
            <div x-data="{ mobileNavOpen: false }" class="mx-auto grid min-h-screen max-w-[1440px] grid-cols-1 gap-4 p-3 sm:p-4 lg:grid-cols-[250px,1fr]">
                <aside class="rp-glass hidden flex-col rounded-3xl p-4 sm:p-5 lg:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/25 text-xl font-black text-indigo-200">R</span>
                        <div>
                            <p class="text-lg font-extrabold leading-none text-indigo-100">Repertorio Pro</p>
                            <p class="mt-1 text-[11px] uppercase tracking-[0.15em] text-slate-400">Premium Gear</p>
                        </div>
                    </a>

                    <button class="rp-btn-primary mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                        <span class="text-lg leading-none">+</span>
                        Criar Novo Repertorio
                    </button>

                    <nav class="mt-6 space-y-1.5 text-sm">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-slate-300/15 text-indigo-100' : 'text-slate-300 hover:bg-slate-500/20' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-slate-300/15 text-indigo-100' : 'text-slate-300 hover:bg-slate-500/20' }}">
                            <span>Library</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-slate-300 hover:bg-slate-500/20">
                            <span>Setlists</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-slate-300 hover:bg-slate-500/20">
                            <span>Archives</span>
                        </a>
                    </nav>

                    <div class="mt-auto space-y-3 pt-8">
                        <p class="text-xs text-slate-500">Logado como <span class="text-slate-300">{{ auth()->user()->name }}</span></p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-red-400/35 bg-red-400/10 px-3 py-2 text-sm font-medium text-red-200 hover:bg-red-400/18">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </aside>

                <div
                    x-show="mobileNavOpen"
                    x-transition.opacity
                    class="fixed inset-0 z-50 bg-black/65 lg:hidden"
                    style="display: none;"
                >
                    <div class="h-full w-[290px] bg-[#0f172a] p-4" @click.outside="mobileNavOpen = false">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/25 text-xl font-black text-indigo-200">R</span>
                            <div>
                                <p class="text-lg font-extrabold leading-none text-indigo-100">Repertorio Pro</p>
                                <p class="mt-1 text-[11px] uppercase tracking-[0.15em] text-slate-400">Premium Gear</p>
                            </div>
                        </a>

                        <button @click="mobileNavOpen = false" type="button" class="mt-4 w-full rounded-xl border border-slate-400/25 bg-slate-600/10 px-3 py-2 text-sm font-semibold text-slate-300">Fechar</button>

                        <nav class="mt-5 space-y-1.5 text-sm">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-slate-300/15 text-indigo-100' : 'text-slate-300 hover:bg-slate-500/20' }}">
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-slate-300/15 text-indigo-100' : 'text-slate-300 hover:bg-slate-500/20' }}">
                                <span>Library</span>
                            </a>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-slate-300 hover:bg-slate-500/20">
                                <span>Setlists</span>
                            </a>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-slate-300 hover:bg-slate-500/20">
                                <span>Archives</span>
                            </a>
                        </nav>

                        <div class="mt-6 space-y-3 border-t border-slate-700/60 pt-4">
                            <p class="text-xs text-slate-500">Logado como <span class="text-slate-300">{{ auth()->user()->name }}</span></p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-red-400/35 bg-red-400/10 px-3 py-2 text-sm font-medium text-red-200 hover:bg-red-400/18">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <section class="rp-glass rp-animate overflow-hidden rounded-3xl">
                    <header class="border-b border-slate-300/10 px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <button @click="mobileNavOpen = true" type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-400/25 bg-slate-600/10 text-slate-200 lg:hidden">
                                        <span class="text-base leading-none">☰</span>
                                    </button>
                                    <h1 class="text-lg font-bold text-indigo-50 sm:text-xl">{{ $title ?? 'Repertorio Pro' }}</h1>
                                </div>
                                <p class="text-xs text-slate-400">Painel de repertorios e setlists</p>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400 sm:text-sm">
                                <span class="rounded-lg border border-slate-400/25 bg-slate-500/10 px-3 py-1.5">Sincronizado</span>
                            </div>
                        </div>
                    </header>

                    <main class="rp-scroll relative max-h-[calc(100vh-120px)] overflow-auto p-4 sm:p-6 lg:p-7">
                        <div class="rp-dot-grid pointer-events-none absolute inset-0"></div>
                        <div class="relative">
                            {{ $slot ?? '' }}
                            @yield('content')
                        </div>
                    </main>
                </section>
            </div>
        @else
            @if (request()->routeIs('login'))
                <main class="relative mx-auto flex min-h-screen w-full max-w-[1300px] items-center justify-center p-4 sm:p-8">
                    <div class="absolute inset-0 rounded-[2rem] border border-indigo-400/15"></div>
                    <div class="relative w-full max-w-md">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </main>
            @else
                <main class="mx-auto w-full max-w-[1200px] p-4 sm:p-8">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            @endif
        @endauth
    </div>

    @livewireScripts
</body>
</html>
