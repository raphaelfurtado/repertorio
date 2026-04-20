@php($title = 'Editar repertorio')
<x-layouts.app :title="$title">
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 underline">← Voltar para meus repertorios</a>
    </div>

    <livewire:repertoire-editor :repertoire="$repertoire" />
</x-layouts.app>
