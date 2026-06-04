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

    <script>
        // Aplica o tema salvo antes da pintura para evitar "flash" de tela.
        (function () {
            try {
                var saved = localStorage.getItem('rp-theme');
                if (saved === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        /* ===== Tema CLARO (padrão) — papel / partitura ===== */
        :root {
            --rp-primary: #4654b0;
            --rp-primary-2: #5663c6;
            --rp-secondary: #6f6657;
            --rp-accent: #c0820a;
            --rp-bg: #f4efe3;
            --rp-bg-soft: #efe8d9;
            --rp-panel: #fffdf8;
            --rp-panel-strong: #ffffff;
            --rp-border: rgba(64, 52, 30, 0.16);
            --rp-border-soft: rgba(64, 52, 30, 0.10);
            --rp-text: #24262e;
            --rp-heading: #1f2b54;
            --rp-text-muted: #6f6657;
            --rp-text-faint: #978b76;
            --rp-tone-major: #3a47a0;
            --rp-tone-minor: #b06a00;
            --rp-hover: rgba(64, 52, 30, 0.06);
            --rp-active: rgba(70, 84, 176, 0.12);
            --rp-chip-bg: rgba(70, 84, 176, 0.10);
            --rp-badge-bg: rgba(70, 84, 176, 0.14);
            --rp-badge-text: #3a47a0;
            --rp-shadow: 0 16px 38px rgba(70, 60, 35, 0.12);
            --rp-shadow-strong: 0 22px 55px rgba(70, 60, 35, 0.16);
            --rp-scroll-thumb: rgba(120, 105, 70, 0.45);
            --rp-scroll-track: rgba(120, 105, 70, 0.12);
            --rp-dot: rgba(80, 66, 40, 0.20);
            --rp-glow: rgba(70, 84, 176, 0.10);
            --rp-body-bg:
                radial-gradient(1000px 600px at 12% -4%, rgba(70, 84, 176, 0.08), transparent 65%),
                radial-gradient(800px 580px at 100% 0, rgba(192, 130, 10, 0.07), transparent 72%),
                linear-gradient(180deg, #f7f2e7 0%, #f4efe3 45%, #f1eadb 100%);
        }

        /* ===== Tema ESCURO (toggle) — navy original ===== */
        html.dark {
            --rp-primary: #5c6bc0;
            --rp-primary-2: #727fcb;
            --rp-secondary: #64748b;
            --rp-accent: #fbbf24;
            --rp-bg: #0f172a;
            --rp-bg-soft: #172036;
            --rp-panel: #1b2438;
            --rp-panel-strong: #111b2f;
            --rp-border: rgba(148, 163, 184, 0.24);
            --rp-border-soft: rgba(148, 163, 184, 0.12);
            --rp-text: #e2e8f0;
            --rp-heading: #e8ecff;
            --rp-text-muted: #94a3b8;
            --rp-text-faint: #64748b;
            --rp-tone-major: #bac3ff;
            --rp-tone-minor: #f9bd22;
            --rp-hover: rgba(148, 163, 184, 0.14);
            --rp-active: rgba(203, 213, 225, 0.15);
            --rp-chip-bg: rgba(100, 116, 139, 0.18);
            --rp-badge-bg: rgba(99, 102, 241, 0.25);
            --rp-badge-text: #c7d2fe;
            --rp-shadow: 0 18px 45px rgba(2, 6, 23, 0.34);
            --rp-shadow-strong: 0 24px 60px rgba(2, 6, 23, 0.45);
            --rp-scroll-thumb: rgba(100, 116, 139, 0.6);
            --rp-scroll-track: rgba(15, 23, 42, 0.35);
            --rp-dot: rgba(148, 163, 184, 0.18);
            --rp-glow: rgba(92, 107, 192, 0.22);
            --rp-body-bg:
                radial-gradient(1000px 600px at 12% -4%, rgba(92, 107, 192, 0.20), transparent 65%),
                radial-gradient(800px 580px at 100% 0, rgba(251, 191, 36, 0.09), transparent 72%),
                linear-gradient(180deg, #0a1224 0%, #0f172a 45%, #0b1324 100%);
        }

        body {
            font-family: 'Sora', sans-serif;
            color: var(--rp-text);
            background: var(--rp-body-bg);
            background-attachment: fixed;
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
            background: radial-gradient(circle, var(--rp-glow), transparent 68%);
            filter: blur(8px);
            z-index: -1;
            pointer-events: none;
        }

        .rp-glass {
            background: var(--rp-panel);
            border: 1px solid var(--rp-border);
            box-shadow: var(--rp-shadow-strong);
        }

        .rp-card {
            background: var(--rp-panel);
            border: 1px solid var(--rp-border);
            box-shadow: var(--rp-shadow);
        }

        /* Helpers de texto cientes do tema */
        .rp-text { color: var(--rp-text); }
        .rp-muted { color: var(--rp-text-muted); }
        .rp-faint { color: var(--rp-text-faint); }
        .rp-heading { color: var(--rp-heading); }
        .rp-accent { color: var(--rp-accent); }
        .rp-border-c { border-color: var(--rp-border); }
        .rp-border-soft-c { border-color: var(--rp-border-soft); }

        .rp-link { color: var(--rp-text-muted); transition: color .15s ease; }
        .rp-link:hover { color: var(--rp-heading); }

        .rp-chip {
            background: var(--rp-chip-bg);
            border: 1px solid var(--rp-border);
            color: var(--rp-text-muted);
        }

        .rp-badge {
            background: var(--rp-badge-bg);
            color: var(--rp-badge-text);
        }

        .rp-nav-link {
            color: var(--rp-text-muted);
            transition: background-color .15s ease, color .15s ease;
        }
        .rp-nav-link:hover { background: var(--rp-hover); }
        .rp-nav-link.is-active { background: var(--rp-active); color: var(--rp-heading); }

        /* Cores de tom — definidas após .rp-chip para vencer quando combinadas no mesmo elemento */
        .rp-tone-major { color: var(--rp-tone-major); }
        .rp-tone-minor { color: var(--rp-tone-minor); }

        .rp-btn-primary {
            background: linear-gradient(140deg, var(--rp-primary-2), var(--rp-primary));
            border: 1px solid rgba(120, 134, 220, 0.32);
            color: #f4f6ff;
        }

        .rp-btn-primary:hover { filter: brightness(1.06); }

        .rp-btn-muted {
            background: var(--rp-hover);
            border: 1px solid var(--rp-border);
            color: var(--rp-text);
        }

        .rp-btn-muted:hover { background: var(--rp-active); }

        .rp-input {
            background: var(--rp-panel-strong);
            border: 1px solid var(--rp-border);
            color: var(--rp-text);
        }

        .rp-input::placeholder { color: var(--rp-text-faint); }

        .rp-input:focus {
            outline: none;
            border-color: var(--rp-primary);
            box-shadow: 0 0 0 3px var(--rp-active);
        }

        .rp-label {
            color: var(--rp-text-muted);
            font-size: 0.71rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .rp-dot-grid {
            background-image: radial-gradient(circle at center, var(--rp-dot) 1.15px, transparent 1.15px);
            background-size: 16px 16px;
            opacity: 0.13;
        }

        .rp-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .rp-scroll::-webkit-scrollbar-thumb {
            background: var(--rp-scroll-thumb);
            border-radius: 9999px;
        }

        .rp-scroll::-webkit-scrollbar-track {
            background: var(--rp-scroll-track);
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
                        <span class="rp-badge inline-flex h-10 w-10 items-center justify-center rounded-xl text-xl font-black">R</span>
                        <div>
                            <p class="rp-heading text-lg font-extrabold leading-none">Repertorio Pro</p>
                            <p class="rp-faint mt-1 text-[11px] uppercase tracking-[0.15em]">Repertórios & palco</p>
                        </div>
                    </a>

                    <a href="{{ route('dashboard') }}#novo-repertorio" class="rp-btn-primary mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                        <span class="text-lg leading-none">+</span>
                        Criar novo repertório
                    </a>

                    <nav class="mt-6 space-y-1.5 text-sm">
                        <a href="{{ route('dashboard') }}" class="rp-nav-link flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                            <span>Meus repertórios</span>
                        </a>
                    </nav>

                    <div class="mt-auto space-y-3 pt-8">
                        <button type="button" onclick="rpToggleTheme()" class="rp-btn-muted flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-medium transition">
                            <span data-rp-theme-icon>🌙</span>
                            <span data-rp-theme-label>Modo escuro</span>
                        </button>
                        <p class="rp-faint text-xs">Logado como <span class="rp-muted">{{ auth()->user()->name }}</span></p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-red-400/35 bg-red-400/10 px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-400/18">
                                Sair
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
                    <div class="rp-glass h-full w-[290px] rounded-r-3xl p-4" @click.outside="mobileNavOpen = false">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                            <span class="rp-badge inline-flex h-10 w-10 items-center justify-center rounded-xl text-xl font-black">R</span>
                            <div>
                                <p class="rp-heading text-lg font-extrabold leading-none">Repertorio Pro</p>
                                <p class="rp-faint mt-1 text-[11px] uppercase tracking-[0.15em]">Premium Gear</p>
                            </div>
                        </a>

                        <button @click="mobileNavOpen = false" type="button" class="rp-btn-muted mt-4 w-full rounded-xl px-3 py-2 text-sm font-semibold">Fechar</button>

                        <a href="{{ route('dashboard') }}#novo-repertorio" @click="mobileNavOpen = false" class="rp-btn-primary mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold">
                            <span class="text-lg leading-none">+</span>
                            Criar novo repertório
                        </a>

                        <nav class="mt-5 space-y-1.5 text-sm">
                            <a href="{{ route('dashboard') }}" class="rp-nav-link flex items-center gap-2 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                                <span>Meus repertórios</span>
                            </a>
                        </nav>

                        <div class="rp-border-soft-c mt-6 space-y-3 border-t pt-4">
                            <button type="button" onclick="rpToggleTheme()" class="rp-btn-muted flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-medium">
                                <span data-rp-theme-icon>🌙</span>
                                <span data-rp-theme-label>Modo escuro</span>
                            </button>
                            <p class="rp-faint text-xs">Logado como <span class="rp-muted">{{ auth()->user()->name }}</span></p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-red-400/35 bg-red-400/10 px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-400/18">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <section class="rp-glass rp-animate overflow-hidden rounded-3xl">
                    <header class="rp-border-soft-c border-b px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <button @click="mobileNavOpen = true" type="button" class="rp-btn-muted inline-flex h-8 w-8 items-center justify-center rounded-lg lg:hidden">
                                        <span class="text-base leading-none">☰</span>
                                    </button>
                                    <h1 class="rp-heading text-lg font-bold sm:text-xl">{{ $title ?? 'Repertorio Pro' }}</h1>
                                </div>
                                <p class="rp-muted text-xs">Painel de repertórios e setlists</p>
                            </div>
                            <div class="flex items-center gap-2 text-xs sm:text-sm">
                                <button type="button" onclick="rpToggleTheme()" title="Alternar tema claro/escuro" class="rp-chip inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5">
                                    <span data-rp-theme-icon>🌙</span>
                                    <span class="hidden sm:inline" data-rp-theme-label>Modo escuro</span>
                                </button>
                                <span class="rp-chip rounded-lg px-3 py-1.5">Sincronizado</span>
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
                    <div class="rp-border-soft-c absolute inset-0 rounded-[2rem] border"></div>
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

    @auth
        {{-- Toast de feedback (ouve o evento 'rp-toast' disparado pelos componentes Livewire) --}}
        <div
            x-data="{ show: false, msg: '' }"
            x-on:rp-toast.window="msg = ($event.detail && ($event.detail.message ?? $event.detail[0])) || 'Pronto'; show = true; clearTimeout(window._rpToast); window._rpToast = setTimeout(() => show = false, 2600)"
            x-show="show"
            x-transition.opacity.duration.200ms
            class="rp-card fixed bottom-5 left-1/2 z-[80] -translate-x-1/2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-lg"
            style="display: none;"
        >
            <span class="rp-heading" x-text="msg"></span>
        </div>
    @endauth

    @livewireScripts

    <script>
        window.rpToggleTheme = function () {
            var isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('rp-theme', isDark ? 'dark' : 'light'); } catch (e) {}
            window.rpSyncThemeButtons();
        };

        window.rpSyncThemeButtons = function () {
            var isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('[data-rp-theme-icon]').forEach(function (el) {
                el.textContent = isDark ? '☀️' : '🌙';
            });
            document.querySelectorAll('[data-rp-theme-label]').forEach(function (el) {
                el.textContent = isDark ? 'Modo claro' : 'Modo escuro';
            });
        };

        document.addEventListener('DOMContentLoaded', window.rpSyncThemeButtons);
        // Livewire pode re-renderizar; ressincroniza os botões após navegação/morphs.
        document.addEventListener('livewire:navigated', window.rpSyncThemeButtons);
    </script>
</body>
</html>
