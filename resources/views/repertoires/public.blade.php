@php
    $totalSongs = $repertoire->blocks->sum(fn ($block) => $block->songs->count());
    $estimatedMinutes = max(0, $totalSongs * 4);
    $estimatedHours = intdiv($estimatedMinutes, 60);
    $remainingMinutes = $estimatedMinutes % 60;
    $estimatedDuration = sprintf('%02d:%02d:00', $estimatedHours, $remainingMinutes);
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $repertoire->name }} | Public View</title>

    <script>
        // Aplica tema e tamanho de leitura salvos antes da pintura (evita "flash").
        (function () {
            try {
                if (localStorage.getItem('rp-theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
                var s = parseFloat(localStorage.getItem('rp-read-scale')) || 1;
                document.documentElement.style.setProperty('--read-scale', s);
            } catch (e) {}
        })();
    </script>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(var(--c-primary) / <alpha-value>)',
                        tertiary: 'rgb(var(--c-tertiary) / <alpha-value>)',
                        surface: 'rgb(var(--c-surface) / <alpha-value>)',
                        'surface-container': 'rgb(var(--c-surface-container) / <alpha-value>)',
                        'surface-container-highest': 'rgb(var(--c-surface-container-highest) / <alpha-value>)',
                        outline: 'rgb(var(--c-outline) / <alpha-value>)',
                        'outline-variant': 'rgb(var(--c-outline-variant) / <alpha-value>)',
                        'on-surface': 'rgb(var(--c-on-surface) / <alpha-value>)',
                        'on-surface-variant': 'rgb(var(--c-on-surface-variant) / <alpha-value>)',
                        'primary-container': 'rgb(var(--c-primary-container) / <alpha-value>)',
                        'on-primary-container': 'rgb(var(--c-on-primary-container) / <alpha-value>)',
                    },
                },
            },
        }
    </script>

    <style>
        /* ===== Tema CLARO (padrão) — papel / partitura ===== */
        :root {
            --c-primary: 58 71 160;
            --c-tertiary: 176 106 0;
            --c-surface: 244 239 227;
            --c-surface-container: 250 246 238;
            --c-surface-container-highest: 233 225 208;
            --c-outline: 138 127 107;
            --c-outline-variant: 111 102 87;
            --c-on-surface: 36 38 46;
            --c-on-surface-variant: 111 102 87;
            --c-primary-container: 217 222 246;
            --c-on-primary-container: 43 52 112;
            --row-hover: rgba(40, 35, 25, 0.05);
        }

        /* ===== Tema ESCURO (toggle) — navy original ===== */
        html.dark {
            --c-primary: 186 195 255;
            --c-tertiary: 249 189 34;
            --c-surface: 11 19 38;
            --c-surface-container: 23 31 51;
            --c-surface-container-highest: 45 52 73;
            --c-outline: 143 143 158;
            --c-outline-variant: 69 70 82;
            --c-on-surface: 218 226 253;
            --c-on-surface-variant: 198 197 212;
            --c-primary-container: 60 76 159;
            --c-on-primary-container: 186 195 255;
            --row-hover: rgba(255, 255, 255, 0.03);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 450, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        h1 {
            font-family: 'Epilogue', sans-serif;
        }

        .row-hover:hover {
            background-color: var(--row-hover);
        }

        /* Tamanhos de leitura escaláveis (palco). --read-scale ajustável pelos botões de zoom. */
        .pv-title {
            font-family: 'Epilogue', sans-serif;
            font-size: calc(clamp(1.3rem, 5vw, 1.7rem) * var(--read-scale, 1));
            line-height: 1.15;
        }
        .pv-tone { font-size: calc(clamp(1.1rem, 4.5vw, 1.4rem) * var(--read-scale, 1)); line-height: 1.1; }
        .pv-sub { font-size: calc(0.95rem * var(--read-scale, 1)); }
        .pv-intro { font-size: calc(0.95rem * var(--read-scale, 1)); line-height: 1.35; }
        .pv-num { font-size: calc(0.8rem * var(--read-scale, 1)); }
    </style>
</head>
<body class="min-h-screen bg-surface pb-10 text-on-surface selection:bg-primary-container selection:text-on-primary-container">
    <header class="mx-auto w-full max-w-5xl border-b border-outline-variant/10 px-4 pb-4 pt-6 sm:px-6 sm:pt-8">
        <div class="flex flex-col justify-between gap-2 md:flex-row md:items-baseline">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h1 class="tracking-tight text-2xl font-bold text-on-surface">{{ $repertoire->name }}</h1>
                <span class="rounded bg-primary-container/30 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Public</span>
                <span class="text-xs font-medium text-on-surface-variant">{{ $estimatedDuration }}</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                <p class="hidden md:block">Main setlist compartilhado para visualizacao rapida.</p>
                <button onclick="rpToggleTheme()" title="Alternar tema claro/escuro" class="transition-colors hover:text-primary" type="button">
                    <span class="material-symbols-outlined text-lg">contrast</span>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-4 sm:px-6">
        @php $songNumber = 0; @endphp
        @forelse ($repertoire->blocks as $index => $block)
            <section class="mb-8">
                <div class="mb-2 flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-outline">Bloco {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}: {{ $block->name }}</span>
                    <div class="h-px flex-grow bg-outline-variant/20"></div>
                </div>

                <div class="divide-y divide-outline-variant/10 border-t border-outline-variant/10">
                    @forelse ($block->songs as $song)
                        @php
                            $songNumber++;
                            $isMinor = $song->key_mode === 'minor';
                            $tonic = $song->key_tonic ?: '-';
                            $toneShort = $isMinor && $song->key_tonic ? $song->key_tonic.'m' : $tonic;
                        @endphp
                        <div class="row-hover group flex items-start gap-3 py-3 sm:gap-4">
                            <div class="w-12 shrink-0 text-center sm:w-14">
                                <span class="pv-num block font-semibold text-outline">{{ $songNumber }}</span>
                                <span class="pv-tone block font-bold {{ $isMinor ? 'text-tertiary' : 'text-primary' }}">{{ $toneShort }}</span>
                            </div>

                            <div class="min-w-0 flex-grow">
                                <div class="mb-0.5 flex flex-wrap items-baseline gap-x-3">
                                    <h3 class="pv-title font-bold text-on-surface">{{ $song->title }}</h3>
                                    <span class="pv-sub text-on-surface-variant">{{ $song->artist_name }}</span>
                                    <span class="pv-sub ml-auto whitespace-nowrap font-medium text-outline">{{ $song->bpm ? $song->bpm . ' BPM' : '-- BPM' }}</span>
                                </div>

                                <p class="pv-intro text-on-surface-variant/80 italic">
                                    <span class="mr-1 text-[10px] font-bold uppercase not-italic tracking-tight text-outline">Intro:</span>
                                    {{ $song->intro_excerpt ?: 'Sem anotacoes de intro.' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-3 text-sm text-on-surface-variant">Sem musicas neste bloco.</div>
                    @endforelse
                </div>
            </section>
        @empty
            <section class="py-16 text-center">
                <p class="text-sm text-on-surface-variant">Nenhum bloco disponivel neste repertorio.</p>
            </section>
        @endforelse
    </main>

    <footer class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-4 border-t border-outline-variant/10 px-6 pb-12 pt-6 text-center md:flex-row md:text-left">
        <p class="text-[11px] font-medium tracking-wide text-outline">
            Shared via <span class="text-on-surface-variant">Repertorio Pro</span> - The Stage Essential App
        </p>
        <div class="flex gap-4 text-outline">
            <button class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">share</span></button>
            <button class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">print</span></button>
            <button class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">download</span></button>
        </div>
    </footer>

    <div class="fixed bottom-6 right-4 flex flex-col gap-2 sm:right-6">
        <button onclick="rpZoom(1)" title="Aumentar fonte" class="h-12 w-12 rounded-xl border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant shadow-lg transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_in</span>
        </button>
        <button onclick="rpZoom(-1)" title="Diminuir fonte" class="h-12 w-12 rounded-xl border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant shadow-lg transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_out</span>
        </button>
    </div>

    <script>
        window.rpToggleTheme = function () {
            var isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('rp-theme', isDark ? 'dark' : 'light'); } catch (e) {}
        };

        (function () {
            var scale = 1;
            try { scale = parseFloat(localStorage.getItem('rp-read-scale')) || 1; } catch (e) {}

            window.rpZoom = function (dir) {
                scale = Math.min(2, Math.max(0.8, Math.round((scale + dir * 0.1) * 10) / 10));
                document.documentElement.style.setProperty('--read-scale', scale);
                try { localStorage.setItem('rp-read-scale', scale); } catch (e) {}
            };
        })();
    </script>
</body>
</html>
