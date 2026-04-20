<section class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-lg shadow-slate-200/50">
        <h1 class="text-2xl font-bold tracking-tight">Meus repertorios</h1>
        <p class="mt-1 text-sm text-slate-600">Crie varios repertorios e organize por ordem.</p>

        <form wire:submit="createRepertoire" class="mt-4 flex flex-col gap-3 sm:flex-row">
            <input
                wire:model="name"
                type="text"
                maxlength="120"
                placeholder="Nome do repertorio"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 outline-none focus:border-teal-500"
            />
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 font-medium text-white hover:bg-slate-700">
                Criar
            </button>
        </form>
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-3">
        @forelse ($repertoires as $index => $repertoire)
            <article class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $repertoire->name }}</h2>
                        <p class="text-sm text-slate-500">
                            {{ $repertoire->is_public ? 'Publico' : 'Privado' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button wire:click="moveUp({{ $repertoire->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↑</button>
                        <button wire:click="moveDown({{ $repertoire->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-sm hover:bg-slate-100">↓</button>

                        <a href="{{ route('repertoires.edit', $repertoire) }}" class="rounded-lg bg-teal-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-teal-500">Abrir</a>

                        <button wire:click="togglePublic({{ $repertoire->id }})" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100">
                            {{ $repertoire->is_public ? 'Despublicar' : 'Publicar' }}
                        </button>

                        @if ($repertoire->is_public)
                            <a href="{{ route('repertoires.public', $repertoire->share_token) }}" target="_blank" class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-sm text-emerald-800 hover:bg-emerald-100">
                                Link publico
                            </a>
                        @endif

                        <button
                            wire:click="deleteRepertoire({{ $repertoire->id }})"
                            wire:confirm="Deseja remover este repertorio?"
                            class="rounded-lg border border-red-300 bg-red-50 px-3 py-1.5 text-sm text-red-700 hover:bg-red-100"
                        >
                            Excluir
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 p-10 text-center text-slate-500">
                Nenhum repertorio ainda.
            </div>
        @endforelse
    </div>
</section>
