@php
    $totalSongs = $repertoire->blocks->sum(fn ($block) => $block->songs->count());
    $estimatedMinutes = max(0, $totalSongs * 4);
    $estimatedHours = intdiv($estimatedMinutes, 60);
    $remainingMinutes = $estimatedMinutes % 60;
    $estimatedDuration = sprintf('%02d:%02d:00', $estimatedHours, $remainingMinutes);
@endphp
<!DOCTYPE html>
<html class="dark" lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $repertoire->name }} | Public View</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#bac3ff',
                        tertiary: '#f9bd22',
                        surface: '#0b1326',
                        'surface-container': '#171f33',
                        'surface-container-highest': '#2d3449',
                        outline: '#8f8f9e',
                        'outline-variant': '#454652',
                        'on-surface': '#dae2fd',
                        'on-surface-variant': '#c6c5d4',
                        'primary-container': '#3c4c9f',
                        'on-primary-container': '#bac3ff',
                    },
                },
            },
        }
    </script>

    <style>
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
            background-color: rgba(255, 255, 255, 0.03);
        }

        .dense-text {
            font-size: 0.8125rem;
            line-height: 1.25rem;
        }
    </style>
</head>
<body class="min-h-screen bg-surface pb-10 text-on-surface selection:bg-primary-container selection:text-on-primary-container">
    <header class="mx-auto w-full max-w-5xl border-b border-outline-variant/10 px-6 pb-4 pt-8">
        <div class="flex flex-col justify-between gap-2 md:flex-row md:items-baseline">
            <div class="flex items-center gap-3">
                <h1 class="tracking-tight text-xl font-bold text-on-surface">{{ $repertoire->name }}</h1>
                <span class="rounded bg-primary-container/30 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Public</span>
                <span class="text-xs font-medium text-on-surface-variant">{{ $estimatedDuration }}</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                <p class="hidden md:block">Main setlist compartilhado para visualizacao rapida.</p>
                <button class="transition-colors hover:text-primary" type="button">
                    <span class="material-symbols-outlined text-lg">contrast</span>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-4">
        @forelse ($repertoire->blocks as $index => $block)
            <section class="mb-8">
                <div class="mb-2 flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-outline">Block {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}: {{ $block->name }}</span>
                    <div class="h-px flex-grow bg-outline-variant/20"></div>
                </div>

                <div class="divide-y divide-outline-variant/10 border-t border-outline-variant/10">
                    @forelse ($block->songs as $song)
                        @php
                            $isMinor = $song->key_mode === 'minor';
                            $tonic = $song->key_tonic ?: '-';
                            $toneShort = $isMinor && $song->key_tonic ? $song->key_tonic.'m' : $tonic;
                        @endphp
                        <div class="row-hover group flex items-start gap-4 py-2">
                            <div class="min-w-[40px] text-center">
                                <span class="block text-sm font-bold {{ $isMinor ? 'text-tertiary' : 'text-primary' }}">{{ $toneShort }}</span>
                            </div>

                            <div class="min-w-0 flex-grow">
                                <div class="mb-0.5 flex flex-wrap items-baseline gap-x-3">
                                    <h3 class="truncate text-xl font-bold text-on-surface md:text-2xl" style="font-family: 'Epilogue', sans-serif;">{{ $song->title }}</h3>
                                    <span class="text-xs text-on-surface-variant">{{ $song->artist_name }}</span>
                                    <span class="ml-auto text-xs text-outline">{{ $song->bpm ? $song->bpm . ' BPM' : '-- BPM' }}</span>
                                </div>

                                <p class="dense-text text-on-surface-variant/80 italic">
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

    <div class="fixed bottom-6 right-6 flex flex-col gap-2">
        <button class="h-10 w-10 rounded border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_in</span>
        </button>
        <button class="h-10 w-10 rounded border border-outline-variant/20 bg-surface-container-highest text-on-surface-variant transition-all hover:text-primary active:scale-95" type="button">
            <span class="material-symbols-outlined text-xl">zoom_out</span>
        </button>
    </div>
</body>
</html>
