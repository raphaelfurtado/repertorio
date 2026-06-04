<section class="rp-text mx-auto max-w-6xl space-y-3 pb-24 text-[14px] leading-5 lg:pb-3">
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
            /* Alvo de toque confortável no mobile */
            .ed-tap { min-height: 44px; }
            .ed-num {
                min-width: 26px;
                color: var(--rp-text-faint);
                font-variant-numeric: tabular-nums;
            }
            /* Barra fixa de salvar (mobile) */
            .ed-savebar {
                background: var(--rp-panel);
                border-top: 1px solid var(--rp-border);
                box-shadow: 0 -8px 24px rgba(40, 35, 25, 0.10);
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
                    <input wire:model="repertoireName" type="text" class="rp-input w-full rounded px-3 py-2 text-[15px]" />
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="saveRepertoire" class="rp-btn-primary ed-tap flex-1 rounded px-4 py-2 text-[13px] font-semibold md:flex-none">Salvar</button>
                    <button wire:click="togglePublic" class="rp-btn-muted ed-tap flex-1 rounded px-4 py-2 text-[13px] font-semibold md:flex-none">
                        {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                    </button>
                </div>
            </div>

            @if ($repertoire->is_public)
                <div class="rp-muted ed-soft rounded px-3 py-2 text-[12px]">
                    Link publico: <a href="{{ route('repertoires.public', $repertoire->share_token) }}" target="_blank" class="rp-tone-major break-all font-semibold underline">{{ route('repertoires.public', $repertoire->share_token) }}</a>
                </div>
            @endif
        </div>
    </div>

    <div x-data="{ openAddBlock: false }" @block-added.window="openAddBlock = false" class="ed-panel overflow-hidden rounded p-3">
        <div class="flex items-center justify-between gap-2">
            <label class="rp-muted block text-[12px] font-semibold">Blocos do repertorio</label>
            <button @click="openAddBlock = true" type="button" class="rp-btn-primary ed-tap rounded px-4 py-2 text-[13px] font-semibold">
                + Bloco
            </button>
        </div>

        <div
            x-show="openAddBlock"
            x-transition.opacity
            @keydown.escape.window="openAddBlock = false"
            class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4"
            style="display: none;"
        >
            <div @click.outside="openAddBlock = false" class="ed-panel w-full max-w-lg rounded-t-2xl p-4 shadow-2xl sm:rounded">
                <h3 class="rp-text text-[15px] font-bold">Adicionar novo bloco</h3>
                <p class="rp-muted mt-1 text-[12px]">Crie uma nova secao para organizar as musicas.</p>

                <form wire:submit="addBlock" class="mt-3 space-y-2">
                    <input wire:model="newBlockName" type="text" placeholder="Ex: Inicio" class="rp-input ed-tap w-full rounded px-3 py-2" />

                    @error('newBlockName')
                        <p class="text-[12px] text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button @click="openAddBlock = false" type="button" class="rp-btn-muted ed-tap rounded px-4 py-2 text-[13px] font-semibold">Cancelar</button>
                        <button type="submit" class="rp-btn-primary ed-tap rounded px-4 py-2 text-[13px] font-semibold">Adicionar bloco</button>
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
                <header class="ed-soft flex items-center gap-2 px-2 py-1.5">
                    <button wire:click="toggleBlock({{ $block->id }})" type="button" class="ed-tap flex min-w-0 flex-1 items-center gap-2 rounded px-1 text-left">
                        <span class="rp-text text-[15px]">{{ $activeBlockId === $block->id ? '▾' : '▸' }}</span>
                        <span class="rp-text truncate text-[13px] font-bold">{{ $block->name }}</span>
                        <span class="rp-faint rp-chip shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold">
                            {{ $songsCount }} {{ $songsCount === 1 ? 'musica' : 'musicas' }}
                        </span>
                        <span class="rp-faint rp-chip hidden shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold sm:inline">
                            ~{{ $estimatedMinutes }} min
                        </span>
                    </button>

                    <button type="button" title="Arrastar bloco" class="block-drag-handle rp-btn-muted ed-tap inline-flex w-11 cursor-grab items-center justify-center rounded text-[14px] active:cursor-grabbing">↕</button>

                    <div x-data="{ menu: false }" class="relative">
                        <button @click="menu = !menu" type="button" title="Mais" class="rp-btn-muted ed-tap inline-flex w-11 items-center justify-center rounded text-[16px] font-bold">⋯</button>
                        <div
                            x-show="menu"
                            x-transition.opacity
                            @click.outside="menu = false"
                            class="ed-panel absolute right-0 top-12 z-20 w-44 overflow-hidden rounded shadow-2xl"
                            style="display: none;"
                        >
                            <button wire:click="promptDeleteBlock({{ $block->id }})" @click="menu = false" type="button" class="ed-danger flex w-full items-center gap-2 px-3 py-2.5 text-left text-[13px] font-semibold">
                                <span>Excluir bloco</span>
                            </button>
                        </div>
                    </div>
                </header>

                @if ($activeBlockId === $block->id)
                <div class="ed-divide" x-init="window.initSongSortable($el, {{ $block->id }}, $wire)">
                    @forelse ($block->songs as $song)
                        @php($isEditing = (bool) ($editingSongs[$song->id] ?? false))

                        <div data-song-id="{{ $song->id }}" class="px-2 py-1.5 sm:px-3">
                            @if (! $isEditing)
                                <div x-data="{ open: false }">
                                    <div class="flex items-center gap-2">
                                        <span class="ed-num text-center text-[12px] font-bold">{{ $loop->iteration }}</span>

                                        <button @click="open = !open" type="button" class="ed-tap flex min-w-0 flex-1 items-center gap-2 rounded py-1 text-left">
                                            <span class="rp-chip shrink-0 rounded px-1.5 py-0.5 text-[11px] font-bold {{ $song->key_mode === 'minor' ? 'rp-tone-minor' : 'rp-tone-major' }}">
                                                {{ $this->toneShort($song->key_tonic, $song->key_mode) }}
                                            </span>
                                            <span class="min-w-0">
                                                <span class="rp-text block truncate text-[16px] font-semibold leading-tight">{{ $song->title }}</span>
                                                <span class="rp-muted block truncate text-[12px]">{{ $song->artist_name }}{{ $song->bpm ? ' · '.$song->bpm.' BPM' : '' }}</span>
                                            </span>
                                            <span class="rp-faint ml-auto shrink-0 text-[14px]" x-text="open ? '▾' : '▸'"></span>
                                        </button>

                                        <button type="button" title="Arrastar musica" class="song-drag-handle rp-btn-muted ed-tap inline-flex w-11 shrink-0 cursor-grab items-center justify-center rounded text-[14px] active:cursor-grabbing">↕</button>
                                    </div>

                                    <p class="rp-faint truncate pl-[34px] text-[12px] italic" title="{{ $song->intro_excerpt ?: 'Sem anotacoes' }}">
                                        Intro: {{ $song->intro_excerpt ?: 'Sem anotacoes' }}
                                    </p>

                                    <div x-show="open" x-transition.opacity class="mt-2 space-y-2 pl-[34px]" style="display: none;">
                                        <div class="rp-muted ed-soft rounded px-3 py-2 text-[12px]">
                                            <span class="rp-text font-semibold">Tom:</span>
                                            <span class="{{ $song->key_mode === 'minor' ? 'rp-tone-minor' : 'rp-tone-major' }}">{{ $this->toneShort($song->key_tonic, $song->key_mode) }}</span>
                                            ·
                                            <span class="rp-text font-semibold">BPM:</span>
                                            {{ $song->bpm ?: '--' }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button wire:click="startEditSong({{ $song->id }})" type="button" class="rp-btn-muted ed-tap flex-1 rounded px-3 py-2 text-[13px] font-semibold">Editar</button>
                                            <button wire:click="promptDeleteSong({{ $song->id }})" type="button" class="ed-danger ed-tap rounded px-4 py-2 text-[13px] font-semibold">Excluir</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1.2fr,150px,170px,auto]">
                                        <input wire:model="songDrafts.{{ $song->id }}.title" class="rp-input ed-tap rounded px-3 py-2 text-[13px]" placeholder="Titulo" />
                                        <input wire:model="songDrafts.{{ $song->id }}.artist_name" class="rp-input ed-tap rounded px-3 py-2 text-[13px]" placeholder="Artista" />
                                        <input wire:model="songDrafts.{{ $song->id }}.bpm" class="rp-input ed-tap rounded px-3 py-2 text-[13px]" placeholder="BPM" />
                                        <select
                                            wire:change="setSongDraftKey({{ $song->id }}, $event.target.value)"
                                            class="rp-input ed-tap rounded px-3 py-2 text-[13px]"
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
                                        <div class="flex items-center gap-2">
                                            <button wire:click="saveEditedSong({{ $song->id }})" class="rp-btn-primary ed-tap flex-1 rounded px-3 py-2 text-[12px] font-semibold md:flex-none">Salvar</button>
                                            <button wire:click="cancelEditSong({{ $song->id }})" class="rp-btn-muted ed-tap flex-1 rounded px-3 py-2 text-[12px] md:flex-none">Cancelar</button>
                                        </div>
                                    </div>
                                    <textarea wire:model="songDrafts.{{ $song->id }}.intro_excerpt" rows="2" class="rp-input w-full rounded px-3 py-2 text-[13px]" placeholder="Intro / anotacao"></textarea>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rp-muted px-3 py-3 text-[13px]">Este bloco ainda nao possui musicas.</div>
                    @endforelse

                    <div x-data="{ openAddSong: false }" @song-added.window="if ($event.detail.blockId === {{ $block->id }}) openAddSong = false" class="px-2 py-2 sm:px-3">
                        <button @click="openAddSong = true" type="button" class="rp-btn-muted ed-tap flex w-full items-center justify-center gap-1 rounded px-3 py-2 text-[13px] font-semibold">
                            + Adicionar musica
                        </button>

                        <div
                            x-show="openAddSong"
                            x-transition.opacity
                            @keydown.escape.window="openAddSong = false"
                            class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4"
                            style="display: none;"
                        >
                            <div @click.outside="openAddSong = false" class="ed-panel max-h-[90vh] w-full max-w-3xl overflow-auto rounded-t-2xl p-4 shadow-2xl sm:rounded">
                                <h3 class="rp-text text-[15px] font-bold">Adicionar musica ao bloco</h3>
                                <p class="rp-muted mt-1 text-[12px]">Bloco atual: {{ $block->name }}</p>

                                <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-[1.5fr,1fr,100px,220px,100px]">
                                    <input wire:model="newSongs.{{ $block->id }}.title" class="rp-input ed-tap rounded px-3 py-2" placeholder="Titulo" />
                                    <input wire:model="newSongs.{{ $block->id }}.artist_name" class="rp-input ed-tap rounded px-3 py-2" placeholder="Artista" />
                                    <input wire:model="newSongs.{{ $block->id }}.bpm" class="rp-input ed-tap rounded px-3 py-2" placeholder="BPM" />
                                    <select
                                        wire:change="setNewSongKey({{ $block->id }}, $event.target.value)"
                                        class="rp-input ed-tap rounded px-3 py-2"
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

                                <textarea wire:model="newSongs.{{ $block->id }}.intro_excerpt" rows="2" class="rp-input mt-2 w-full rounded px-3 py-2" placeholder="Intro / anotacao"></textarea>

                                <div class="mt-3 flex items-center justify-end gap-2">
                                    <button @click="openAddSong = false" type="button" class="rp-btn-muted ed-tap rounded px-4 py-2 text-[13px] font-semibold">Cancelar</button>
                                    <button wire:click="addSong({{ $block->id }})" type="button" class="rp-btn-primary ed-tap rounded px-4 py-2 text-[13px] font-semibold">
                                        Adicionar musica
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </article>
        @endforeach
    </div>

    {{-- Barra fixa de salvar (somente mobile) --}}
    <div class="ed-savebar fixed inset-x-0 bottom-0 z-40 flex items-center gap-2 px-3 py-2.5 lg:hidden">
        <button wire:click="saveRepertoire" class="rp-btn-primary ed-tap flex-1 rounded-xl px-4 py-2 text-[14px] font-semibold">Salvar repertorio</button>
        <button wire:click="togglePublic" class="rp-btn-muted ed-tap rounded-xl px-4 py-2 text-[13px] font-semibold">
            {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
        </button>
    </div>

    @if ($deleteTargetType)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4">
            <div class="ed-panel w-full max-w-md rounded-t-2xl p-4 shadow-2xl sm:rounded">
                <h3 class="rp-text text-[15px] font-bold">Confirmar exclusao</h3>
                <p class="rp-muted mt-2 text-[13px]">
                    @if ($deleteTargetType === 'block')
                        Tem certeza que deseja excluir o bloco <span class="rp-text font-semibold">{{ $deleteTargetLabel }}</span> e todas as musicas dele?
                    @else
                        Tem certeza que deseja excluir a musica <span class="rp-text font-semibold">{{ $deleteTargetLabel }}</span>?
                    @endif
                </p>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button wire:click="cancelDelete" type="button" class="rp-btn-muted ed-tap rounded px-4 py-2 text-[13px] font-semibold">Cancelar</button>
                    <button wire:click="confirmDelete" type="button" class="ed-danger ed-tap rounded px-4 py-2 text-[13px] font-semibold">Excluir</button>
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
