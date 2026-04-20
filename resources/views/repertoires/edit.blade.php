@php($title = 'Editar repertorio')
<x-layouts.app :title="$title">
    <div class="mx-auto max-w-6xl space-y-3">
        <div class="rp-card rounded-2xl px-4 py-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-300 hover:text-indigo-200">← Voltar para meus repertorios</a>
        </div>

        <livewire:repertoire-editor :repertoire="$repertoire" />
    </div>
</x-layouts.app>
