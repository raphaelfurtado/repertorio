<section class="mx-auto max-w-5xl space-y-4 sm:space-y-5">
    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-lg shadow-slate-200/60 sm:p-5">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h1 class="text-lg font-bold tracking-tight sm:text-xl">Editor de repertorio</h1>
            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $repertoire->is_public ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                {{ $repertoire->is_public ? 'Publico' : 'Privado' }}
            </span>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="w-full md:flex-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nome do repertorio</label>
                <input
                    wire:model="repertoireName"
                    type="text"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none focus:border-teal-500"
                />
            </div>

            <div class="grid grid-cols-2 gap-2 md:flex md:items-center">
                <button wire:click="saveRepertoire" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">
                    Salvar
                </button>
                <button wire:click="togglePublic" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold hover:bg-slate-100">
                    {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                </button>
            </div>
        </div>

        @if ($repertoire->is_public)
            <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                <p class="font-semibold">Link publico</p>
                <a class="break-all underline" target="_blank" href="{{ route('repertoires.public', $repertoire->share_token) }}">{{ route('repertoires.public', $repertoire->share_token) }}</a>
            </div>
        @endif
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 sm:p-5">
        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Novo bloco</label>
        <form wire:submit="addBlock" class="flex flex-col gap-2 sm:flex-row">
            <input wire:model="newBlockName" type="text" placeholder="Ex: Inicio, Louvor, Encerramento" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:border-teal-500" />
            <button type="submit" class="rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-500">Adicionar bloco</button>
        </form>
        @error('newBlockName')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-4">
        @foreach ($repertoire->blocks as $block)
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-100 bg-slate-50/80 px-4 py-3 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-base font-bold text-slate-800 sm:text-lg">{{ $block->name }}</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <button wire:click="moveBlockUp({{ $block->id }})" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-sm hover:bg-slate-100">↑</button>
                            <button wire:click="moveBlockDown({{ $block->id }})" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-sm hover:bg-slate-100">↓</button>
                            <button wire:click="deleteBlock({{ $block->id }})" wire:confirm="Excluir bloco e musicas?" class="rounded-lg border border-red-300 bg-red-50 px-3 py-1 text-sm font-medium text-red-700 hover:bg-red-100">Excluir bloco</button>
                        </div>
                    </div>
                </header>

                <div class="space-y-3 p-4 sm:p-5">
                    @forelse ($block->songs as $song)
                        <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Musica cadastrada</p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="moveSongUp({{ $song->id }})" class="rounded-md border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↑</button>
                                    <button wire:click="moveSongDown({{ $song->id }})" class="rounded-md border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↓</button>
                                    <button wire:click="deleteSong({{ $song->id }})" wire:confirm="Excluir musica?" class="rounded-md border border-red-300 bg-red-50 px-2 py-1 text-sm text-red-700 hover:bg-red-100">X</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-5">
                                <div class="xl:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Artista</label>
                                    <input value="{{ $song->artist_name }}" wire:change="updateSongField({{ $song->id }}, 'artist_name', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Artista" />
                                </div>
                                <div class="xl:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Musica</label>
                                    <input value="{{ $song->title }}" wire:change="updateSongField({{ $song->id }}, 'title', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Musica" />
                                </div>
                                <div class="xl:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Tom</label>
                                    <input value="{{ $song->key_tonic }}" wire:change="updateSongField({{ $song->id }}, 'key_tonic', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="C, D#, Bb" />
                                </div>
                                <div class="xl:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">Modo</label>
                                    <select wire:change="updateSongField({{ $song->id }}, 'key_mode', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                        <option value="" @selected(!$song->key_mode)>Selecionar</option>
                                        <option value="major" @selected($song->key_mode === 'major')>Maior</option>
                                        <option value="minor" @selected($song->key_mode === 'minor')>Menor</option>
                                    </select>
                                </div>
                                <div class="xl:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-slate-500">BPM</label>
                                    <input value="{{ $song->bpm }}" wire:change="updateSongField({{ $song->id }}, 'bpm', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Opcional" />
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Trecho para lembrar como comeca</label>
                                <textarea wire:change="updateSongField({{ $song->id }}, 'intro_excerpt', $event.target.value)" class="w-full rounded-lg border border-slate-300 px-3 py-2" rows="2" placeholder="Ex: primeira frase ou referencia de entrada">{{ $song->intro_excerpt }}</textarea>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                            Este bloco ainda nao possui musicas.
                        </div>
                    @endforelse

                    <div class="rounded-2xl border border-dashed border-teal-300 bg-teal-50/40 p-3 sm:p-4">
                        <h3 class="mb-3 text-sm font-bold text-slate-700">Adicionar musica no bloco</h3>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-5">
                            <div class="xl:col-span-1">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Artista</label>
                                <input wire:model="newSongs.{{ $block->id }}.artist_name" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Artista" />
                            </div>
                            <div class="xl:col-span-1">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Musica</label>
                                <input wire:model="newSongs.{{ $block->id }}.title" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Nome da musica" />
                            </div>
                            <div class="xl:col-span-1">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Tom</label>
                                <input wire:model="newSongs.{{ $block->id }}.key_tonic" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="C, D#, Bb" />
                            </div>
                            <div class="xl:col-span-1">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">Modo</label>
                                <select wire:model="newSongs.{{ $block->id }}.key_mode" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                    <option value="">Selecionar</option>
                                    <option value="major">Maior</option>
                                    <option value="minor">Menor</option>
                                </select>
                            </div>
                            <div class="xl:col-span-1">
                                <label class="mb-1 block text-xs font-semibold text-slate-500">BPM</label>
                                <input wire:model="newSongs.{{ $block->id }}.bpm" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Opcional" />
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="mb-1 block text-xs font-semibold text-slate-500">Trecho para lembrar como comeca</label>
                            <textarea wire:model="newSongs.{{ $block->id }}.intro_excerpt" class="w-full rounded-lg border border-slate-300 px-3 py-2" rows="2" placeholder="Ex: primeira frase da musica"></textarea>
                        </div>

                        <button wire:click="addSong({{ $block->id }})" class="mt-3 w-full rounded-xl bg-teal-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-teal-500 sm:w-auto">
                            Adicionar musica
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
