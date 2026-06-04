<section class="rp-text mx-auto max-w-6xl space-y-3 text-[14px] leading-5">
    @once
        <style>
            .ed-panel { background: var(--rp-panel); border: 1px solid var(--rp-border); }
            .ed-soft { background: var(--rp-bg-soft); }
            .ed-titlebar {
                background: linear-gradient(140deg, var(--rp-primary-2), var(--rp-primary));
                color: #f4f6ff;
            }
            .ed-divide > :not([hidden]) ~ :not([hidden]) {
                border-top: 1px solid var(--rp-border-soft);
            }
            .ed-danger {
                border: 1px solid rgba(220, 38, 38, 0.40);
                background: rgba(220, 38, 38, 0.10);
                color: #dc2626;
            }
        </style>
    @endonce

    <div class="ed-panel overflow-hidden rounded">
        <div class="ed-titlebar flex flex-wrap items-center justify-between gap-2 px-3 py-2 text-[13px] font-bold">
            <span>repertorio editor</span>
            <span>{{ $repertoire->is_public ? 'publico' : 'privado' }}</span>
        </div>

        <div class="space-y-3 p-3">
            <div class="grid grid-cols-1 gap-2 md:grid-cols-[1fr,auto] md:items-end">
                <div>
                    <label class="rp-muted mb-1 block text-[12px] font-semibold">Nome do repertorio</label>
                    <input wire:model="repertoireName" type="text" class="rp-input w-full rounded px-2 py-1.5 text-[15px]" />
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="saveRepertoire" class="rp-btn-primary rounded px-3 py-1.5 text-[13px] font-semibold">Salvar</button>
                    <button wire:click="togglePublic" class="rp-btn-muted rounded px-3 py-1.5 text-[13px] font-semibold">
                        {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                    </button>
                </div>
            </div>

            @if ($repertoire->is_public)
                <div class="rp-muted ed-soft rounded px-3 py-2 text-[12px]">
                    Link publico: <a href="{{ route('repertoires.public', $repertoire->share_token) }}" target="_blank" class="rp-tone-major font-semibold underline">{{ route('repertoires.public', $repertoire->share_token) }}</a>
                </div>
            @endif
        </div>
    </div>

    <div x-data="{ openAddBlock: false }" @block-added.window="openAddBlock = false" class="ed-panel overflow-hidden rounded p-3">
        <div class="flex items-center justify-between gap-2">
            <label class="rp-muted block text-[12px] font-semibold">Blocos do repertorio</label>
            <button @click="openAddBlock = true" type="button" class="rp-btn-primary rounded px-3 py-1.5 text-[13px] font-semibold">
                + Adicionar bloco
            </button>
        </div>

        <div
            x-show="openAddBlock"
            x-transition.opacity
            @keydown.escape.window="openAddBlock = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;"
        >
            <div @click.outside="openAddBlock = false" class="ed-panel w-full max-w-lg rounded p-4 shadow-2xl">
                <h3 class="rp-text text-[14px] font-bold">Adicionar novo bloco</h3>
                <p class="rp-muted mt-1 text-[12px]">Crie uma nova secao para organizar as musicas.</p>

                <form wire:submit="addBlock" class="mt-3 space-y-2">
                    <input wire:model="newBlockName" type="text" placeholder="Ex: Inicio" class="rp-input w-full rounded px-2 py-1.5" />

                    @error('newBlockName')
                        <p class="text-[12px] text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button @click="openAddBlock = false" type="button" class="rp-btn-muted rounded px-3 py-1.5 text-[13px] font-semibold">Cancelar</button>
                        <button type="submit" class="rp-btn-primary rounded px-3 py-1.5 text-[13px] font-semibold">Adicionar bloco</button>
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
            <article data-block-id="{{ $block->id }}" class="ed-panel overflow-hidden rounded">
                <header class="rp-muted ed-soft flex flex-wrap items-center justify-between gap-2 px-3 py-2 text-[12px] font-bold uppercase tracking-wider">
                    <button wire:click="toggleBlock({{ $block->id }})" type="button" class="inline-flex items-center gap-2 text-left">
                        <span class="rp-text">{{ $activeBlockId === $block->id ? '▾' : '▸' }}</span>
                        <span>Bloco: {{ $block->name }}</span>
                        <span class="rp-faint rp-chip rounded px-1.5 py-0.5 text-[10px] font-semibold tracking-normal normal-case">
                            {{ $block->songs->count() }} {{ $block->songs->count() === 1 ? 'musica' : 'musicas' }}
                        </span>
                        <span class="rp-faint rp-chip rounded px-1.5 py-0.5 text-[10px] font-semibold tracking-normal normal-case">
                            ~{{ $estimatedMinutes }} min
                        </span>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <button type="button" title="Arrastar bloco" class="block-drag-handle rp-btn-muted cursor-grab rounded px-2 py-1 text-[12px] active:cursor-grabbing">↕</button>
                        <button wire:click="promptDeleteBlock({{ $block->id }})" type="button" class="ed-danger rounded px-2 py-1 text-[12px]">Excluir bloco</button>
                    </div>
                </header>

                @if ($activeBlockId === $block->id)
                <div class="ed-divide" x-init="window.initSongSortable($el, {{ $block->id }}, $wire)">
                    @forelse ($block->songs as $song)
                        @php($isEditing = (bool) ($editingSongs[$song->id] ?? false))

                        <div data-song-id="{{ $song->id }}" class="px-3 py-2">
                            @if (! $isEditing)
                                <div x-data="{ showDetails: false }" class="space-y-1">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-start">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-baseline gap-x-2">
                                                <span class="rp-chip rounded px-1.5 py-0.5 text-[11px] font-bold {{ $song->key_mode === 'minor' ? 'rp-tone-minor' : 'rp-tone-major' }}">
                                                    {{ $this->toneShort($song->key_tonic, $song->key_mode) }}
                                                </span>
                                                <p class="rp-text text-[16px] font-semibold">{{ $song->title }}</p>
                                                <p class="rp-muted text-[13px]">{{ $song->artist_name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                        <button @click="showDetails = !showDetails" type="button" class="rp-btn-muted rounded px-2 py-1 text-[12px]">
                                            Detalhes
                                        </button>
                                        <button wire:click="startEditSong({{ $song->id }})" type="button" class="rp-btn-muted rounded px-2 py-1 text-[12px]" title="Editar">✎</button>
                                        <button type="button" title="Arrastar musica" class="song-drag-handle rp-btn-muted cursor-grab rounded px-2 py-1 text-[12px] active:cursor-grabbing">↕</button>
                                        <button wire:click="promptDeleteSong({{ $song->id }})" type="button" class="ed-danger rounded px-2 py-1 text-[12px]">X</button>
                                    </div>
                                    </div>

                                    <div x-show="showDetails" x-transition.opacity class="rp-muted ed-soft rounded px-2 py-1.5 text-[12px]" style="display: none;">
                                        <p>
                                            <span class="rp-text font-semibold">Tom:</span>
                                            <span class="{{ $song->key_mode === 'minor' ? 'rp-tone-minor' : 'rp-tone-major' }}">{{ $this->toneShort($song->key_tonic, $song->key_mode) }}</span>
                                            ·
                                            <span class="rp-text font-semibold">BPM:</span>
                                            {{ $song->bpm ?: '--' }}
                                        </p>
                                    </div>

                                    <p class="rp-faint truncate text-[12px] italic" title="{{ $song->intro_excerpt ?: 'Sem anotacoes' }}">
                                        Intro: {{ $song->intro_excerpt ?: 'Sem anotacoes' }}
                                    </p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1.2fr,150px,170px,auto]">
                                        <input wire:model="songDrafts.{{ $song->id }}.title" class="rp-input rounded px-2 py-1.5 text-[13px]" placeholder="Titulo" />
                                        <input wire:model="songDrafts.{{ $song->id }}.artist_name" class="rp-input rounded px-2 py-1.5 text-[13px]" placeholder="Artista" />
                                        <input wire:model="songDrafts.{{ $song->id }}.bpm" class="rp-input rounded px-2 py-1.5 text-[13px]" placeholder="BPM" />
                                        <select
                                            wire:change="setSongDraftKey({{ $song->id }}, $event.target.value)"
                                            class="rp-input rounded px-2 py-1.5 text-[13px]"
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
                                            <button wire:click="saveEditedSong({{ $song->id }})" class="rp-btn-primary rounded px-2 py-1 text-[12px] font-semibold">Salvar</button>
                                            <button wire:click="cancelEditSong({{ $song->id }})" class="rp-btn-muted rounded px-2 py-1 text-[12px]">Cancelar</button>
                                        </div>
                                    </div>
                                    <textarea wire:model="songDrafts.{{ $song->id }}.intro_excerpt" rows="2" class="rp-input w-full rounded px-2 py-1.5 text-[13px]" placeholder="Intro / anotacao"></textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rp-muted px-3 py-3 text-[13px]">Este bloco ainda nao possui musicas.</div>
                    @endforelse
                </div>
                @endif

                <div x-data="{ openAddSong: false }" @song-added.window="if ($event.detail.blockId === {{ $block->id }}) openAddSong = false" class="rp-border-soft-c ed-soft border-t p-3">
                    <div class="flex items-center justify-between gap-2">
                        <p class="rp-muted text-[12px] font-bold uppercase tracking-wider">Musicas do bloco</p>
                        <button @click="openAddSong = true" type="button" class="rp-btn-muted rounded px-3 py-1.5 text-[13px] font-semibold">
                            + Adicionar musica
                        </button>
                    </div>

                    <div
                        x-show="openAddSong"
                        x-transition.opacity
                        @keydown.escape.window="openAddSong = false"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                        style="display: none;"
                    >
                        <div @click.outside="openAddSong = false" class="ed-panel w-full max-w-3xl rounded p-4 shadow-2xl">
                            <h3 class="rp-text text-[14px] font-bold">Adicionar musica ao bloco</h3>
                            <p class="rp-muted mt-1 text-[12px]">Bloco atual: {{ $block->name }}</p>

                            <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1fr,100px,220px,100px]">
                                <input wire:model="newSongs.{{ $block->id }}.title" class="rp-input rounded px-2 py-1.5" placeholder="Titulo" />
                                <input wire:model="newSongs.{{ $block->id }}.artist_name" class="rp-input rounded px-2 py-1.5" placeholder="Artista" />
                                <input wire:model="newSongs.{{ $block->id }}.bpm" class="rp-input rounded px-2 py-1.5" placeholder="BPM" />
                                <select
                                    wire:change="setNewSongKey({{ $block->id }}, $event.target.value)"
                                    class="rp-input rounded px-2 py-1.5"
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

                            <textarea wire:model="newSongs.{{ $block->id }}.intro_excerpt" rows="2" class="rp-input mt-2 w-full rounded px-2 py-1.5" placeholder="Intro / anotacao"></textarea>

                            <div class="mt-3 flex items-center justify-end gap-2">
                                <button @click="openAddSong = false" type="button" class="rp-btn-muted rounded px-3 py-1.5 text-[13px] font-semibold">Cancelar</button>
                                <button wire:click="addSong({{ $block->id }})" type="button" class="rp-btn-primary rounded px-3 py-1.5 text-[13px] font-semibold">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="ed-panel w-full max-w-md rounded p-4 shadow-2xl">
                <h3 class="rp-text text-[15px] font-bold">Confirmar exclusao</h3>
                <p class="rp-muted mt-2 text-[13px]">
                    @if ($deleteTargetType === 'block')
                        Tem certeza que deseja excluir o bloco <span class="rp-text font-semibold">{{ $deleteTargetLabel }}</span> e todas as musicas dele?
                    @else
                        Tem certeza que deseja excluir a musica <span class="rp-text font-semibold">{{ $deleteTargetLabel }}</span>?
                    @endif
                </p>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button wire:click="cancelDelete" type="button" class="rp-btn-muted rounded px-3 py-1.5 text-[13px] font-semibold">Cancelar</button>
                    <button wire:click="confirmDelete" type="button" class="ed-danger rounded px-3 py-1.5 text-[13px] font-semibold">Excluir</button>
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
