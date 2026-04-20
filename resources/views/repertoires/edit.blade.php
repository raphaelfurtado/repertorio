@php($title = 'Editar repertorio')
<x-layouts.app :title="$title">
    <div class="mx-auto max-w-5xl">
        <div class="mb-3 rounded-2xl bg-white/75 px-4 py-3 shadow-sm ring-1 ring-slate-200">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-600 underline">← Voltar para meus repertorios</a>
        </div>

        <livewire:repertoire-editor :repertoire="$repertoire" />
    </div>
</x-layouts.app>
