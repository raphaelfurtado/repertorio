<section class="space-y-5">
    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-lg shadow-slate-200/50">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full">
                <label class="text-sm font-medium text-slate-700">Nome do repertorio</label>
                <input
                    wire:model="repertoireName"
                    type="text"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2 outline-none focus:border-teal-500"
                />
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="saveRepertoire" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    Salvar nome
                </button>
                <button wire:click="togglePublic" class="rounded-xl border border-slate-300 px-4 py-2 text-sm hover:bg-slate-100">
                    {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                </button>
            </div>
        </div>

        @if ($repertoire->is_public)
            <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                Link publico: <a class="underline" target="_blank" href="{{ route('repertoires.public', $repertoire->share_token) }}">{{ route('repertoires.public', $repertoire->share_token) }}</a>
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5">
        <form wire:submit="addBlock" class="flex flex-col gap-3 sm:flex-row">
            <input wire:model="newBlockName" type="text" placeholder="Novo bloco (ex: Abertura, Louvor, Encerramento)" class="w-full rounded-xl border border-slate-300 px-4 py-2 outline-none focus:border-teal-500" />
            <button type="submit" class="rounded-xl bg-teal-600 px-4 py-2 font-medium text-white hover:bg-teal-500">Adicionar bloco</button>
        </form>
        @error('newBlockName')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-4">
        @foreach ($repertoire->blocks as $block)
            <article class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold">{{ $block->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <button wire:click="moveBlockUp({{ $block->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↑</button>
                        <button wire:click="moveBlockDown({{ $block->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↓</button>
                        <button wire:click="deleteBlock({{ $block->id }})" wire:confirm="Excluir bloco e musicas?" class="rounded-lg border border-red-300 bg-red-50 px-3 py-1 text-sm text-red-700 hover:bg-red-100">Excluir bloco</button>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($block->songs as $song)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-6">
                                <input value="{{ $song->artist_name }}" wire:change="updateSongField({{ $song->id }}, 'artist_name', $event.target.value)" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Artista" />
                                <input value="{{ $song->title }}" wire:change="updateSongField({{ $song->id }}, 'title', $event.target.value)" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Musica" />
                                <input value="{{ $song->key_tonic }}" wire:change="updateSongField({{ $song->id }}, 'key_tonic', $event.target.value)" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Tom (C, D#, Bb)" />
                                <select wire:change="updateSongField({{ $song->id }}, 'key_mode', $event.target.value)" class="rounded-lg border border-slate-300 px-3 py-2">
                                    <option value="" @selected(!$song->key_mode)>Modo</option>
                                    <option value="major" @selected($song->key_mode === 'major')>Maior</option>
                                    <option value="minor" @selected($song->key_mode === 'minor')>Menor</option>
                                </select>
                                <input value="{{ $song->bpm }}" wire:change="updateSongField({{ $song->id }}, 'bpm', $event.target.value)" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="BPM" />
                                <div class="flex items-center gap-2">
                                    <button wire:click="moveSongUp({{ $song->id }})" class="rounded border border-slate-300 px-2 py-1 text-sm">↑</button>
                                    <button wire:click="moveSongDown({{ $song->id }})" class="rounded border border-slate-300 px-2 py-1 text-sm">↓</button>
                                    <button wire:click="deleteSong({{ $song->id }})" wire:confirm="Excluir musica?" class="rounded border border-red-300 bg-red-50 px-2 py-1 text-sm text-red-700">X</button>
                                </div>
                            </div>
                            <textarea wire:change="updateSongField({{ $song->id }}, 'intro_excerpt', $event.target.value)" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" rows="2" placeholder="Trecho de inicio para lembrar como comeca">{{ $song->intro_excerpt }}</textarea>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 rounded-xl border border-dashed border-slate-300 p-3">
                    <h3 class="mb-2 text-sm font-semibold text-slate-700">Adicionar musica no bloco</h3>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-5">
                        <input wire:model="newSongs.{{ $block->id }}.artist_name" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Artista" />
                        <input wire:model="newSongs.{{ $block->id }}.title" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Musica" />
                        <input wire:model="newSongs.{{ $block->id }}.key_tonic" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="Tom" />
                        <select wire:model="newSongs.{{ $block->id }}.key_mode" class="rounded-lg border border-slate-300 px-3 py-2">
                            <option value="">Modo</option>
                            <option value="major">Maior</option>
                            <option value="minor">Menor</option>
                        </select>
                        <input wire:model="newSongs.{{ $block->id }}.bpm" class="rounded-lg border border-slate-300 px-3 py-2" placeholder="BPM (opcional)" />
                    </div>
                    <textarea wire:model="newSongs.{{ $block->id }}.intro_excerpt" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" rows="2" placeholder="Trecho para lembrar como comeca"></textarea>
                    <button wire:click="addSong({{ $block->id }})" class="mt-2 rounded-lg bg-teal-600 px-3 py-2 text-sm font-medium text-white hover:bg-teal-500">Adicionar musica</button>
                </div>
            </article>
        @endforeach
    </div>
</section>
