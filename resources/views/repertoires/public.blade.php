@php($title = 'Repertorio publico')
<x-layouts.app :title="$title">
    <section class="space-y-4">
        <header class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight">{{ $repertoire->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">Repertorio publico compartilhado</p>
        </header>

        @foreach ($repertoire->blocks as $block)
            <article class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
                <h2 class="text-lg font-semibold">{{ $block->name }}</h2>

                <div class="mt-3 space-y-2">
                    @forelse ($block->songs as $song)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="font-semibold">{{ $song->artist_name }} - {{ $song->title }}</p>
                            <p class="text-sm text-slate-600">
                                @if ($song->key_tonic && $song->key_mode)
                                    Tom: {{ $song->key_tonic }} {{ $song->key_mode === 'major' ? 'Maior' : 'Menor' }}
                                @endif
                                @if ($song->bpm)
                                    | BPM: {{ $song->bpm }}
                                @endif
                            </p>
                            @if ($song->intro_excerpt)
                                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $song->intro_excerpt }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sem musicas neste bloco.</p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
</x-layouts.app>
