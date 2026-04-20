<section class="mx-auto max-w-6xl space-y-3 text-[14px] leading-5 text-[#dae2fd]">
    <div class="overflow-hidden rounded border border-[#454652] bg-[#131b2e]">
        <div class="flex flex-wrap items-center justify-between gap-2 bg-[#3c4c9f] px-3 py-2 text-[13px] font-bold text-[#dee0ff]">
            <span>repertorio editor</span>
            <span>{{ $repertoire->is_public ? 'publico' : 'privado' }}</span>
        </div>

        <div class="space-y-3 p-3">
            <div class="grid grid-cols-1 gap-2 md:grid-cols-[1fr,auto] md:items-end">
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#a9bad3]">Nome do repertorio</label>
                    <input wire:model="repertoireName" type="text" class="w-full rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[15px] text-[#dae2fd]" />
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="saveRepertoire" class="rounded border border-[#5262b2] bg-[#3c4c9f] px-3 py-1.5 text-[13px] font-semibold text-[#dee0ff] hover:bg-[#4858ab]">Salvar</button>
                    <button wire:click="togglePublic" class="rounded border border-[#454652] bg-[#0b1326] px-3 py-1.5 text-[13px] font-semibold text-[#c6c5d4] hover:bg-[#171f33]">
                        {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                    </button>
                </div>
            </div>

            @if ($repertoire->is_public)
                <div class="rounded border border-[#454652] bg-[#0b1326] px-3 py-2 text-[12px] text-[#a9bad3]">
                    Link publico: <a href="{{ route('repertoires.public', $repertoire->share_token) }}" target="_blank" class="font-semibold text-[#bac3ff] underline">{{ route('repertoires.public', $repertoire->share_token) }}</a>
                </div>
            @endif
        </div>
    </div>

    <div x-data="{ openAddBlock: false }" @block-added.window="openAddBlock = false" class="overflow-hidden rounded border border-[#454652] bg-[#131b2e] p-3">
        <div class="flex items-center justify-between gap-2">
            <label class="block text-[12px] font-semibold text-[#a9bad3]">Blocos do repertorio</label>
            <button @click="openAddBlock = true" type="button" class="rounded border border-[#5262b2] bg-[#3c4c9f] px-3 py-1.5 text-[13px] font-semibold text-[#dee0ff] hover:bg-[#4858ab]">
                + Adicionar bloco
            </button>
        </div>

        <div
            x-show="openAddBlock"
            x-transition.opacity
            @keydown.escape.window="openAddBlock = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            style="display: none;"
        >
            <div @click.outside="openAddBlock = false" class="w-full max-w-lg rounded border border-[#454652] bg-[#131b2e] p-4 shadow-2xl">
                <h3 class="text-[14px] font-bold text-[#dae2fd]">Adicionar novo bloco</h3>
                <p class="mt-1 text-[12px] text-[#a9bad3]">Crie uma nova secao para organizar as musicas.</p>

                <form wire:submit="addBlock" class="mt-3 space-y-2">
                    <input wire:model="newBlockName" type="text" placeholder="Ex: Inicio" class="w-full rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]" />

                    @error('newBlockName')
                        <p class="text-[12px] text-[#ffb4ab]">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button @click="openAddBlock = false" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-3 py-1.5 text-[13px] font-semibold text-[#c6c5d4]">Cancelar</button>
                        <button type="submit" class="rounded border border-[#5262b2] bg-[#3c4c9f] px-3 py-1.5 text-[13px] font-semibold text-[#dee0ff] hover:bg-[#4858ab]">Adicionar bloco</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-3" x-init="window.initBlockSortable($el, $wire)">
        @foreach ($repertoire->blocks as $block)
            @php
                $songsCount = $block->songs->count();
                $estimatedMinutes = $songsCount * 4;
            @endphp
            <article data-block-id="{{ $block->id }}" class="overflow-hidden rounded border border-[#454652] bg-[#131b2e]">
                <header class="flex flex-wrap items-center justify-between gap-2 bg-[#171f33] px-3 py-2 text-[12px] font-bold uppercase tracking-wider text-[#a9bad3]">
                    <button wire:click="toggleBlock({{ $block->id }})" type="button" class="inline-flex items-center gap-2 text-left">
                        <span class="text-[#c6c5d4]">{{ $activeBlockId === $block->id ? '▾' : '▸' }}</span>
                        <span>Bloco: {{ $block->name }}</span>
                        <span class="rounded border border-[#454652] bg-[#0b1326] px-1.5 py-0.5 text-[10px] font-semibold tracking-normal text-[#8f8f9e] normal-case">
                            {{ $block->songs->count() }} {{ $block->songs->count() === 1 ? 'musica' : 'musicas' }}
                        </span>
                        <span class="rounded border border-[#454652] bg-[#0b1326] px-1.5 py-0.5 text-[10px] font-semibold tracking-normal text-[#8f8f9e] normal-case">
                            ~{{ $estimatedMinutes }} min
                        </span>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <button type="button" title="Arrastar bloco" class="block-drag-handle cursor-grab rounded border border-[#454652] bg-[#0b1326] px-2 py-1 text-[12px] text-[#c6c5d4] active:cursor-grabbing">↕</button>
                        <button wire:click="promptDeleteBlock({{ $block->id }})" type="button" class="rounded border border-[#7a3d4a] bg-[#331922] px-2 py-1 text-[12px] text-[#ffb4ab]">Excluir bloco</button>
                    </div>
                </header>

                @if ($activeBlockId === $block->id)
                <div class="divide-y divide-[#2d3449]" x-init="window.initSongSortable($el, {{ $block->id }}, $wire)">
                    @forelse ($block->songs as $song)
                        @php($isEditing = (bool) ($editingSongs[$song->id] ?? false))

                        <div data-song-id="{{ $song->id }}" class="px-3 py-2">
                            @if (! $isEditing)
                                <div x-data="{ showDetails: false }" class="space-y-1">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-start">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-baseline gap-x-2">
                                                <span class="rounded border border-[#2d3449] bg-[#0f172a] px-1.5 py-0.5 text-[11px] font-bold {{ $song->key_mode === 'minor' ? 'text-[#f9bd22]' : 'text-[#bac3ff]' }}">
                                                    {{ $this->toneShort($song->key_tonic, $song->key_mode) }}
                                                </span>
                                                <p class="text-[16px] font-semibold text-[#dae2fd]">{{ $song->title }}</p>
                                                <p class="text-[13px] text-[#c6c5d4]">{{ $song->artist_name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <button @click="showDetails = !showDetails" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1 text-[12px] text-[#c6c5d4]">
                                                Detalhes
                                            </button>
                                            <button wire:click="startEditSong({{ $song->id }})" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1 text-[12px] text-[#c6c5d4]" title="Editar">✎</button>
                                            <button type="button" title="Arrastar musica" class="song-drag-handle cursor-grab rounded border border-[#454652] bg-[#0b1326] px-2 py-1 text-[12px] text-[#c6c5d4] active:cursor-grabbing">↕</button>
                                            <button wire:click="promptDeleteSong({{ $song->id }})" type="button" class="rounded border border-[#7a3d4a] bg-[#331922] px-2 py-1 text-[12px] text-[#ffb4ab]">X</button>
                                        </div>
                                    </div>

                                    <div x-show="showDetails" x-transition.opacity class="rounded border border-[#2d3449] bg-[#0f172a] px-2 py-1.5 text-[12px] text-[#a9bad3]" style="display: none;">
                                        <p>
                                            <span class="font-semibold text-[#c6c5d4]">Tom:</span>
                                            <span class="{{ $song->key_mode === 'minor' ? 'text-[#f9bd22]' : 'text-[#bac3ff]' }}">{{ $this->toneShort($song->key_tonic, $song->key_mode) }}</span>
                                            ·
                                            <span class="font-semibold text-[#c6c5d4]">BPM:</span>
                                            {{ $song->bpm ?: '--' }}
                                        </p>
                                    </div>

                                    <p class="truncate text-[12px] italic text-[#8f8f9e]" title="{{ $song->intro_excerpt ?: 'Sem anotacoes' }}">
                                        Intro: {{ $song->intro_excerpt ?: 'Sem anotacoes' }}
                                    </p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1.2fr,150px,170px,auto]">
                                        <input wire:model="songDrafts.{{ $song->id }}.title" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[13px] text-[#dae2fd]" placeholder="Titulo" />
                                        <input wire:model="songDrafts.{{ $song->id }}.artist_name" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[13px] text-[#dae2fd]" placeholder="Artista" />
                                        <input wire:model="songDrafts.{{ $song->id }}.bpm" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[13px] text-[#dae2fd]" placeholder="BPM" />
                                        <select
                                            wire:change="setSongDraftKey({{ $song->id }}, $event.target.value)"
                                            class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[13px] text-[#dae2fd]"
                                        >
                                            <option value="">Selecionar tom</option>
                                            <optgroup label="Tons Maiores">
                                                @foreach ($majorTones as $tone)
                                                    <option value="{{ $tone }}|major" @selected(($songDrafts[$song->id]['key_tonic'] ?? '') === $tone && ($songDrafts[$song->id]['key_mode'] ?? '') === 'major')>
                                                        {{ $tone }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Tons Menores">
                                                @foreach ($minorTones as $tone)
                                                    <option value="{{ $tone }}|minor" @selected(($songDrafts[$song->id]['key_tonic'] ?? '') === $tone && ($songDrafts[$song->id]['key_mode'] ?? '') === 'minor')>
                                                        {{ $tone }}m
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="saveEditedSong({{ $song->id }})" class="rounded border border-[#5262b2] bg-[#3c4c9f] px-2 py-1 text-[12px] font-semibold text-[#dee0ff]">Salvar</button>
                                            <button wire:click="cancelEditSong({{ $song->id }})" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1 text-[12px] text-[#c6c5d4]">Cancelar</button>
                                        </div>
                                    </div>
                                    <textarea wire:model="songDrafts.{{ $song->id }}.intro_excerpt" rows="2" class="w-full rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[13px] text-[#dae2fd]" placeholder="Intro / anotacao"></textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-3 py-3 text-[13px] text-[#a9bad3]">Este bloco ainda nao possui musicas.</div>
                    @endforelse
                </div>
                @endif

                <div x-data="{ openAddSong: false }" @song-added.window="if ($event.detail.blockId === {{ $block->id }}) openAddSong = false" class="border-t border-[#2d3449] bg-[#171f33] p-3">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[12px] font-bold uppercase tracking-wider text-[#a9bad3]">Musicas do bloco</p>
                        <button @click="openAddSong = true" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-3 py-1.5 text-[13px] font-semibold text-[#c6c5d4] hover:bg-[#131b2e]">
                            + Adicionar musica
                        </button>
                    </div>

                    <div
                        x-show="openAddSong"
                        x-transition.opacity
                        @keydown.escape.window="openAddSong = false"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                        style="display: none;"
                    >
                        <div @click.outside="openAddSong = false" class="w-full max-w-3xl rounded border border-[#454652] bg-[#131b2e] p-4 shadow-2xl">
                            <h3 class="text-[14px] font-bold text-[#dae2fd]">Adicionar musica ao bloco</h3>
                            <p class="mt-1 text-[12px] text-[#a9bad3]">Bloco atual: {{ $block->name }}</p>

                            <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1fr,100px,220px,100px]">
                                <input wire:model="newSongs.{{ $block->id }}.title" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]" placeholder="Titulo" />
                                <input wire:model="newSongs.{{ $block->id }}.artist_name" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]" placeholder="Artista" />
                                <input wire:model="newSongs.{{ $block->id }}.bpm" class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]" placeholder="BPM" />
                                <select
                                    wire:change="setNewSongKey({{ $block->id }}, $event.target.value)"
                                    class="rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]"
                                >
                                    <option value="">Selecionar tom</option>
                                    <optgroup label="Tons Maiores">
                                        @foreach ($majorTones as $tone)
                                            <option value="{{ $tone }}|major" @selected(($newSongs[$block->id]['key_tonic'] ?? '') === $tone && ($newSongs[$block->id]['key_mode'] ?? '') === 'major')>
                                                {{ $tone }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Tons Menores">
                                        @foreach ($minorTones as $tone)
                                            <option value="{{ $tone }}|minor" @selected(($newSongs[$block->id]['key_tonic'] ?? '') === $tone && ($newSongs[$block->id]['key_mode'] ?? '') === 'minor')>
                                                {{ $tone }}m
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <textarea wire:model="newSongs.{{ $block->id }}.intro_excerpt" rows="2" class="mt-2 w-full rounded border border-[#454652] bg-[#0b1326] px-2 py-1.5 text-[#dae2fd]" placeholder="Intro / anotacao"></textarea>

                            <div class="mt-3 flex items-center justify-end gap-2">
                                <button @click="openAddSong = false" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-3 py-1.5 text-[13px] font-semibold text-[#c6c5d4]">Cancelar</button>
                                <button wire:click="addSong({{ $block->id }})" type="button" class="rounded border border-[#5262b2] bg-[#3c4c9f] px-3 py-1.5 text-[13px] font-semibold text-[#dee0ff] hover:bg-[#4858ab]">
                                    Adicionar musica
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    @if ($deleteTargetType)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="w-full max-w-md rounded border border-[#454652] bg-[#131b2e] p-4 shadow-2xl">
                <h3 class="text-[15px] font-bold text-[#dae2fd]">Confirmar exclusao</h3>
                <p class="mt-2 text-[13px] text-[#a9bad3]">
                    @if ($deleteTargetType === 'block')
                        Tem certeza que deseja excluir o bloco <span class="font-semibold text-[#dee0ff]">{{ $deleteTargetLabel }}</span> e todas as musicas dele?
                    @else
                        Tem certeza que deseja excluir a musica <span class="font-semibold text-[#dee0ff]">{{ $deleteTargetLabel }}</span>?
                    @endif
                </p>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button wire:click="cancelDelete" type="button" class="rounded border border-[#454652] bg-[#0b1326] px-3 py-1.5 text-[13px] font-semibold text-[#c6c5d4]">Cancelar</button>
                    <button wire:click="confirmDelete" type="button" class="rounded border border-[#7a3d4a] bg-[#331922] px-3 py-1.5 text-[13px] font-semibold text-[#ffb4ab]">Excluir</button>
                </div>
            </div>
        </div>
    @endif

    @once
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            window.initBlockSortable = function (el, wire) {
                if (!window.Sortable || el.dataset.sortableInit === '1') {
                    return;
                }

                el.dataset.sortableInit = '1';

                Sortable.create(el, {
                    animation: 150,
                    handle: '.block-drag-handle',
                    draggable: '[data-block-id]',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-block-id]')).map((item) => Number(item.dataset.blockId));
                        wire.reorderBlocks(ids);
                    },
                });
            };

            window.initSongSortable = function (el, blockId, wire) {
                if (!window.Sortable || el.dataset.sortableInit === '1') {
                    return;
                }

                el.dataset.sortableInit = '1';

                Sortable.create(el, {
                    animation: 150,
                    handle: '.song-drag-handle',
                    draggable: '[data-song-id]',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-song-id]')).map((item) => Number(item.dataset.songId));
                        wire.reorderSongs(blockId, ids);
                    },
                });
            };
        </script>
    @endonce
</section>
