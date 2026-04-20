<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">Dashboard</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-indigo-50 sm:text-4xl">Meus Repertorios</h2>
            <p class="mt-2 text-sm text-slate-400">Organize setlists por turne, evento ou ensaio.</p>
        </div>

        <form wire:submit="createRepertoire" class="rp-card w-full rounded-2xl p-3 sm:w-auto">
            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    wire:model="name"
                    type="text"
                    maxlength="120"
                    placeholder="Nome do novo repertorio"
                    class="rp-input w-full rounded-xl px-4 py-2.5 text-sm sm:w-72"
                />
                <button type="submit" class="rp-btn-primary rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                    Criar
                </button>
            </div>
            @error('name')
                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
            @enderror
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        @forelse ($repertoires as $index => $repertoire)
            <article class="rp-card group relative overflow-hidden rounded-2xl p-4 sm:p-5 {{ $index === 0 ? 'xl:col-span-2' : '' }}">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-indigo-400/10 via-transparent to-transparent"></div>
                <div class="relative">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex rounded-full border px-2 py-1 text-[10px] font-bold uppercase tracking-wider {{ $repertoire->is_public ? 'border-emerald-300/40 bg-emerald-400/10 text-emerald-200' : 'border-slate-400/30 bg-slate-400/10 text-slate-300' }}">
                                {{ $repertoire->is_public ? 'Em Turne' : 'Rascunho' }}
                            </span>
                            <h3 class="mt-3 text-2xl font-bold text-indigo-50">{{ $repertoire->name }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ $repertoire->is_public ? 'Setlist pronto para compartilhar com a banda.' : 'Estrutura em preparacao para proxima apresentacao.' }}</p>
                        </div>
                        <div class="flex gap-1.5 text-slate-400">
                            <button wire:click="moveUp({{ $repertoire->id }})" class="rounded-lg border border-slate-300/20 bg-slate-500/10 px-2 py-1 text-sm hover:bg-slate-500/20">↑</button>
                            <button wire:click="moveDown({{ $repertoire->id }})" class="rounded-lg border border-slate-300/20 bg-slate-500/10 px-2 py-1 text-sm hover:bg-slate-500/20">↓</button>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="{{ route('repertoires.edit', $repertoire) }}" class="rp-btn-primary rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wide">Abrir Editor</a>

                        <button wire:click="togglePublic({{ $repertoire->id }})" class="rp-btn-muted rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wide">
                            {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                        </button>

                        @if ($repertoire->is_public)
                            <a href="{{ route('repertoires.public', $repertoire->share_token) }}" target="_blank" class="rounded-xl border border-emerald-300/40 bg-emerald-400/10 px-3.5 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-200">
                                Preview Publico
                            </a>
                        @endif

                        <button
                            wire:click="deleteRepertoire({{ $repertoire->id }})"
                            wire:confirm="Deseja remover este repertorio?"
                            class="rounded-xl border border-red-300/40 bg-red-500/10 px-3.5 py-2 text-xs font-semibold uppercase tracking-wide text-red-200"
                        >
                            Excluir
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="rp-card col-span-full rounded-2xl border-dashed p-12 text-center text-slate-400">
                <p class="text-lg font-semibold text-indigo-100">Nenhum repertorio ainda</p>
                <p class="mt-2 text-sm">Comece criando o primeiro projeto para montar seu set.</p>
            </div>
        @endforelse
    </div>
</section>
