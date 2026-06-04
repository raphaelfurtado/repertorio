@php
    $totalSongs = $repertoire->blocks->sum(fn ($block) => $block->songs->count());
    $estimatedMinutes = max(0, $totalSongs * 4);
    $estimatedHours = intdiv($estimatedMinutes, 60);
    $remainingMinutes = $estimatedMinutes % 60;
    $estimatedDuration = sprintf('%02d:%02d:00', $estimatedHours, $remainingMinutes);

    // Lista plana das músicas para o Modo palco (navegação música a música).
    $stageSongs = [];
    $stageNumber = 0;
    foreach ($repertoire->blocks as $block) {
        foreach ($block->songs as $song) {
            $stageNumber++;
            $isMinorStage = $song->key_mode === 'minor';
            $stageSongs[] = [
                'n' => $stageNumber,
                'block' => $block->name,
                'title' => $song->title,
                'artist' => $song->artist_name,
                'tone' => $song->key_tonic ? ($isMinorStage ? $song->key_tonic.'m' : $song->key_tonic) : '-',
                'minor' => $isMinorStage,
                'bpm' => $song->bpm,
                'intro' => $song->intro_excerpt,
            ];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $repertoire->name }} · Repertório</title>

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

        /* Modo palco — leitura música a música em tela cheia */
        #stage-overlay { display: none; }
        #stage-overlay.is-open { display: flex; }
        .stage-tone-major { color: rgb(var(--c-primary)); }
        .stage-tone-minor { color: rgb(var(--c-tertiary)); }

        /* Impressão limpa (botão Imprimir / salvar PDF) */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; color: #000; }
            .row-hover { break-inside: avoid; }
        }
    </style>
</head>
<body class="min-h-screen bg-surface pb-10 text-on-surface selection:bg-primary-container selection:text-on-primary-container">
    <header class="no-print mx-auto w-full max-w-5xl border-b border-outline-variant/10 px-4 pb-4 pt-6 sm:px-6 sm:pt-8">
        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-baseline">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h1 class="tracking-tight text-2xl font-bold text-on-surface">{{ $repertoire->name }}</h1>
                <span class="rounded bg-primary-container/30 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Público</span>
                <span class="text-xs font-medium text-on-surface-variant">{{ $totalSongs }} {{ $totalSongs === 1 ? 'música' : 'músicas' }} · {{ $estimatedDuration }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                @if ($totalSongs > 0)
                    <button onclick="stageOpen(0)" class="inline-flex items-center gap-1.5 rounded-lg border border-primary/40 bg-primary-container/30 px-3 py-1.5 font-semibold text-primary transition-colors hover:bg-primary-container/50" type="button">
                        <span class="material-symbols-outlined text-base">slideshow</span>
                        Modo palco
                    </button>
                @endif
                <button onclick="rpToggleTheme()" title="Alternar tema claro/escuro" class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition-colors hover:text-primary" type="button">
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
                        <div onclick="stageOpen({{ $songNumber - 1 }})" title="Abrir no Modo palco" class="row-hover group flex cursor-pointer items-start gap-3 py-3 sm:gap-4">
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
                                    {{ $song->intro_excerpt ?: 'Sem anotações de intro.' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-3 text-sm text-on-surface-variant">Sem músicas neste bloco.</div>
                    @endforelse
                </div>
            </section>
        @empty
            <section class="py-16 text-center">
                <p class="text-sm text-on-surface-variant">Nenhum bloco disponível neste repertório.</p>
            </section>
        @endforelse
    </main>

    <footer class="no-print mx-auto flex max-w-5xl flex-col items-center justify-between gap-4 border-t border-outline-variant/10 px-6 pb-12 pt-6 text-center md:flex-row md:text-left">
        <p class="text-[11px] font-medium tracking-wide text-outline">
            Compartilhado via <span class="text-on-surface-variant">Repertorio Pro</span> — o essencial pro palco
        </p>
        <div class="flex gap-4 text-outline">
            <button onclick="stageShare()" title="Compartilhar link" class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">share</span></button>
            <button onclick="window.print()" title="Imprimir" class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">print</span></button>
            <button onclick="window.print()" title="Salvar como PDF" class="hover:text-primary" type="button"><span class="material-symbols-outlined text-base">download</span></button>
        </div>
    </footer>

    <div class="no-print fixed bottom-6 right-4 flex flex-col gap-2 sm:right-6">
        <button onclick="rpZoom(1)" title="Aumentar fonte" class="h-12 w-12 rounded-xl border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant shadow-lg transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_in</span>
        </button>
        <button onclick="rpZoom(-1)" title="Diminuir fonte" class="h-12 w-12 rounded-xl border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant shadow-lg transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_out</span>
        </button>
    </div>

    {{-- ===== Modo palco (tela cheia, música a música) ===== --}}
    <div id="stage-overlay" class="no-print fixed inset-0 z-[60] flex-col bg-surface text-on-surface">
        {{-- Topo: bloco + posição + sair --}}
        <div class="flex items-center justify-between gap-3 px-4 pt-4 sm:px-6">
            <span id="stage-block" class="truncate text-xs font-bold uppercase tracking-widest text-outline"></span>
            <div class="flex items-center gap-3">
                <span id="stage-counter" class="text-xs font-semibold text-on-surface-variant"></span>
                <button onclick="stageClose()" title="Sair (Esc)" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant hover:text-primary" type="button">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        {{-- Centro: música atual. Toque na metade esquerda/direita para navegar. --}}
        <div class="relative flex flex-1 items-center justify-center overflow-hidden px-6">
            <button onclick="stageGo(-1)" aria-label="Anterior" class="absolute inset-y-0 left-0 w-1/3 cursor-w-resize" type="button"></button>
            <button onclick="stageGo(1)" aria-label="Próxima" class="absolute inset-y-0 right-0 w-1/3 cursor-e-resize" type="button"></button>

            <div class="pointer-events-none max-w-3xl text-center">
                <div id="stage-tone" class="font-black leading-none" style="font-size: clamp(4rem, 22vw, 9rem);"></div>
                <h2 id="stage-title" class="mt-4 font-bold" style="font-family: 'Epilogue', sans-serif; font-size: clamp(2rem, 8vw, 4rem); line-height: 1.1;"></h2>
                <p id="stage-artist" class="mt-3 text-on-surface-variant" style="font-size: clamp(1rem, 4vw, 1.6rem);"></p>
                <p id="stage-bpm" class="mt-2 font-medium text-outline" style="font-size: clamp(0.9rem, 3.5vw, 1.3rem);"></p>
                <p id="stage-intro" class="mx-auto mt-6 max-w-2xl italic text-on-surface-variant/80" style="font-size: clamp(1rem, 4vw, 1.5rem); line-height: 1.4;"></p>
            </div>
        </div>

        {{-- Base: navegação explícita --}}
        <div class="flex items-center justify-between gap-3 px-4 pb-5 sm:px-6">
            <button onclick="stageGo(-1)" class="inline-flex items-center gap-1 rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 font-semibold text-on-surface-variant active:scale-95" type="button">
                <span class="material-symbols-outlined">chevron_left</span> Anterior
            </button>
            <button onclick="stageToggleWake()" id="stage-wake" title="Manter tela acesa" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-surface-container px-3 py-3 text-xs font-semibold text-on-surface-variant active:scale-95" type="button">
                <span class="material-symbols-outlined text-base">lightbulb</span>
                <span class="hidden sm:inline">Tela acesa</span>
            </button>
            <button onclick="stageGo(1)" class="inline-flex items-center gap-1 rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 font-semibold text-on-surface-variant active:scale-95" type="button">
                Próxima <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>
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

        // Compartilhar link (Web Share API com fallback de copiar)
        window.stageShare = function () {
            var url = window.location.href;
            if (navigator.share) {
                navigator.share({ title: document.title, url: url }).catch(function () {});
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function () {
                    alert('Link copiado!');
                }).catch(function () {});
            }
        };

        // ===== Modo palco =====
        (function () {
            var SONGS = @json($stageSongs);
            var overlay = document.getElementById('stage-overlay');
            var idx = 0;
            var wakeLock = null;
            var wakeWanted = false;

            function el(id) { return document.getElementById(id); }

            function render() {
                var s = SONGS[idx];
                if (!s) { return; }
                el('stage-block').textContent = s.block || '';
                el('stage-counter').textContent = (idx + 1) + ' / ' + SONGS.length;
                var tone = el('stage-tone');
                tone.textContent = s.tone || '-';
                tone.className = 'font-black leading-none ' + (s.minor ? 'stage-tone-minor' : 'stage-tone-major');
                el('stage-title').textContent = s.title || '';
                el('stage-artist').textContent = s.artist || '';
                el('stage-bpm').textContent = s.bpm ? (s.bpm + ' BPM') : '';
                el('stage-intro').textContent = s.intro ? ('Intro: ' + s.intro) : '';
            }

            async function requestWake() {
                if (!wakeWanted || !('wakeLock' in navigator)) { return; }
                try { wakeLock = await navigator.wakeLock.request('screen'); } catch (e) {}
            }

            function releaseWake() {
                wakeWanted = false;
                if (wakeLock) { wakeLock.release().catch(function () {}); wakeLock = null; }
                syncWakeButton();
            }

            function syncWakeButton() {
                var btn = el('stage-wake');
                if (!btn) { return; }
                btn.style.opacity = wakeWanted ? '1' : '0.55';
            }

            window.stageToggleWake = function () {
                wakeWanted = !wakeWanted;
                if (wakeWanted) { requestWake(); } else { releaseWake(); }
                syncWakeButton();
            };

            window.stageOpen = function (i) {
                if (!SONGS.length) { return; }
                idx = Math.min(SONGS.length - 1, Math.max(0, i || 0));
                overlay.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                render();
                // tenta manter a tela acesa por padrão no palco
                wakeWanted = true; requestWake(); syncWakeButton();
            };

            window.stageClose = function () {
                overlay.classList.remove('is-open');
                document.body.style.overflow = '';
                releaseWake();
            };

            window.stageGo = function (delta) {
                var next = idx + delta;
                if (next < 0 || next >= SONGS.length) { return; }
                idx = next;
                render();
            };

            // Reativa o wake lock ao voltar o foco (alguns navegadores soltam)
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible' && wakeWanted) { requestWake(); }
            });

            // Teclado
            document.addEventListener('keydown', function (e) {
                if (!overlay.classList.contains('is-open')) { return; }
                if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown') { e.preventDefault(); stageGo(1); }
                else if (e.key === 'ArrowLeft' || e.key === 'PageUp') { e.preventDefault(); stageGo(-1); }
                else if (e.key === 'Escape') { stageClose(); }
            });

            // Swipe (toque)
            var touchX = null;
            overlay.addEventListener('touchstart', function (e) { touchX = e.changedTouches[0].clientX; }, { passive: true });
            overlay.addEventListener('touchend', function (e) {
                if (touchX === null) { return; }
                var dx = e.changedTouches[0].clientX - touchX;
                if (Math.abs(dx) > 50) { stageGo(dx < 0 ? 1 : -1); }
                touchX = null;
            }, { passive: true });
        })();
    </script>
</body>
</html>
