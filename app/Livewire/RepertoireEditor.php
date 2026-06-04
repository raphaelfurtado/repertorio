<?php

namespace App\Livewire;

use App\Models\Repertoire;
use App\Models\RepertoireBlock;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RepertoireEditor extends Component
{
    public Repertoire $repertoire;

    public string $repertoireName = '';

    public string $newBlockName = '';

    public array $newSongs = [];

    public array $editingSongs = [];

    public array $songDrafts = [];

    public ?int $activeBlockId = null;

    public ?string $deleteTargetType = null;

    public ?int $deleteTargetId = null;

    public string $deleteTargetLabel = '';

    public array $majorTones = ['C', 'C#', 'Db', 'D', 'Eb', 'E', 'F', 'F#', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];

    public array $minorTones = ['A', 'A#', 'Bb', 'B', 'C', 'C#', 'Db', 'D', 'D#', 'Eb', 'E', 'F', 'F#', 'Gb', 'G', 'G#', 'Ab'];

    public function mount(Repertoire $repertoire): void
    {
        abort_unless($repertoire->user_id === Auth::id(), 403);

        $this->repertoire = $repertoire;
        $this->repertoireName = $repertoire->name;
    }

    public function saveRepertoire(): void
    {
        $validated = $this->validate([
            'repertoireName' => ['required', 'string', 'max:120'],
        ]);

        $this->repertoire->name = $validated['repertoireName'];
        $this->repertoire->save();
        $this->dispatch('rp-toast', message: 'Repertório salvo.');
    }

    public function togglePublic(): void
    {
        $this->repertoire->is_public = ! $this->repertoire->is_public;
        $this->repertoire->save();
        $this->dispatch('rp-toast', message: $this->repertoire->is_public ? 'Repertório publicado.' : 'Repertório despublicado.');
    }

    public function addBlock(): void
    {
        $validated = $this->validate([
            'newBlockName' => ['required', 'string', 'max:120'],
        ]);

        $maxSort = (int) RepertoireBlock::query()
            ->where('repertoire_id', $this->repertoire->id)
            ->max('sort_order');

        $block = RepertoireBlock::create([
            'repertoire_id' => $this->repertoire->id,
            'name' => $validated['newBlockName'],
            'sort_order' => $maxSort + 1,
        ]);

        $this->activeBlockId = $block->id;
        $this->newBlockName = '';
        $this->dispatch('block-added');
        $this->dispatch('rp-toast', message: 'Bloco adicionado.');
    }

    public function deleteBlock(int $blockId): void
    {
        if ($this->activeBlockId === $blockId) {
            $this->activeBlockId = null;
        }

        $this->ownedBlock($blockId)->delete();
        $this->dispatch('rp-toast', message: 'Bloco excluído.');
    }

    public function promptDeleteBlock(int $blockId): void
    {
        $block = $this->ownedBlock($blockId);

        $this->deleteTargetType = 'block';
        $this->deleteTargetId = $block->id;
        $this->deleteTargetLabel = $block->name;
    }

    public function toggleBlock(int $blockId): void
    {
        $this->activeBlockId = $this->activeBlockId === $blockId ? null : $blockId;
    }

    public function moveBlockUp(int $blockId): void
    {
        $this->moveBlock($blockId, -1);
    }

    public function moveBlockDown(int $blockId): void
    {
        $this->moveBlock($blockId, 1);
    }

    public function reorderBlocks(array $orderedBlockIds): void
    {
        $ids = array_values(array_unique(array_map('intval', $orderedBlockIds)));

        if ($ids === []) {
            return;
        }

        $blocks = RepertoireBlock::query()
            ->where('repertoire_id', $this->repertoire->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $position = 1;

        foreach ($ids as $id) {
            if (! isset($blocks[$id])) {
                continue;
            }

            $block = $blocks[$id];
            $block->sort_order = $position;
            $block->save();
            $position++;
        }
    }

    public function addSong(int $blockId): void
    {
        $block = $this->ownedBlock($blockId);
        $input = $this->newSongs[$blockId] ?? [];

        $validator = validator($input, [
            'artist_name' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:120'],
            'key_tonic' => ['nullable', 'regex:/^[A-G](#|b)?$/'],
            'key_mode' => ['nullable', 'in:major,minor'],
            'bpm' => ['nullable', 'integer', 'min:30', 'max:300'],
            'intro_excerpt' => ['nullable', 'string', 'max:1200'],
        ]);

        $validated = $validator->validate();

        $maxSort = (int) Song::query()
            ->where('repertoire_block_id', $blockId)
            ->max('sort_order');

        Song::create([
            'repertoire_block_id' => $blockId,
            'artist_name' => $validated['artist_name'],
            'title' => $validated['title'],
            'key_tonic' => $validated['key_tonic'] ?: null,
            'key_mode' => $validated['key_mode'] ?: null,
            'bpm' => $validated['bpm'] ?: null,
            'intro_excerpt' => $validated['intro_excerpt'] ?: null,
            'sort_order' => $maxSort + 1,
        ]);

        $this->newSongs[$blockId] = [
            'artist_name' => '',
            'title' => '',
            'key_tonic' => '',
            'key_mode' => '',
            'bpm' => '',
            'intro_excerpt' => '',
        ];

        $this->activeBlockId = $blockId;
        $this->dispatch('song-added', blockId: $blockId);
        $this->dispatch('rp-toast', message: 'Música adicionada.');
    }

    public function setSongDraftKey(int $songId, string $signature): void
    {
        if (! isset($this->songDrafts[$songId])) {
            return;
        }

        [$tonic, $mode] = $this->parseToneSignature($signature);

        $this->songDrafts[$songId]['key_tonic'] = $tonic;
        $this->songDrafts[$songId]['key_mode'] = $mode;
    }

    public function setNewSongKey(int $blockId, string $signature): void
    {
        if (! isset($this->newSongs[$blockId])) {
            $this->newSongs[$blockId] = [
                'artist_name' => '',
                'title' => '',
                'key_tonic' => '',
                'key_mode' => '',
                'bpm' => '',
                'intro_excerpt' => '',
            ];
        }

        [$tonic, $mode] = $this->parseToneSignature($signature);

        $this->newSongs[$blockId]['key_tonic'] = $tonic;
        $this->newSongs[$blockId]['key_mode'] = $mode;
    }

    public function startEditSong(int $songId): void
    {
        $song = $this->ownedSong($songId);

        $this->activeBlockId = $song->repertoire_block_id;
        $this->editingSongs[$songId] = true;
        $this->songDrafts[$songId] = [
            'artist_name' => $song->artist_name,
            'title' => $song->title,
            'key_tonic' => $song->key_tonic ?? '',
            'key_mode' => $song->key_mode ?? '',
            'bpm' => $song->bpm ? (string) $song->bpm : '',
            'intro_excerpt' => $song->intro_excerpt ?? '',
        ];
    }

    public function cancelEditSong(int $songId): void
    {
        unset($this->editingSongs[$songId], $this->songDrafts[$songId]);
    }

    public function saveEditedSong(int $songId): void
    {
        $song = $this->ownedSong($songId);
        $draft = $this->songDrafts[$songId] ?? [];

        $validated = validator($draft, $this->songRules())->validate();

        $song->artist_name = $validated['artist_name'];
        $song->title = $validated['title'];
        $song->key_tonic = ($validated['key_tonic'] ?? '') ?: null;
        $song->key_mode = ($validated['key_mode'] ?? '') ?: null;
        $song->bpm = ($validated['bpm'] ?? '') ?: null;
        $song->intro_excerpt = ($validated['intro_excerpt'] ?? '') ?: null;
        $song->save();

        unset($this->editingSongs[$songId], $this->songDrafts[$songId]);
        $this->dispatch('rp-toast', message: 'Música salva.');
    }

    public function updateSongField(int $songId, string $field, string $value): void
    {
        $allowed = ['artist_name', 'title', 'key_tonic', 'key_mode', 'bpm', 'intro_excerpt'];

        if (! in_array($field, $allowed, true)) {
            return;
        }

        $song = $this->ownedSong($songId);

        $rules = $this->songRules();

        $validated = validator([$field => $value], [$field => $rules[$field]])->validate();
        $song->{$field} = $validated[$field] === '' ? null : $validated[$field];
        $song->save();
    }

    public function deleteSong(int $songId): void
    {
        $this->ownedSong($songId)->delete();
        unset($this->editingSongs[$songId], $this->songDrafts[$songId]);
        $this->dispatch('rp-toast', message: 'Música excluída.');
    }

    public function promptDeleteSong(int $songId): void
    {
        $song = $this->ownedSong($songId);

        $this->deleteTargetType = 'song';
        $this->deleteTargetId = $song->id;
        $this->deleteTargetLabel = $song->title;
    }

    public function cancelDelete(): void
    {
        $this->deleteTargetType = null;
        $this->deleteTargetId = null;
        $this->deleteTargetLabel = '';
    }

    public function confirmDelete(): void
    {
        if (! $this->deleteTargetType || ! $this->deleteTargetId) {
            return;
        }

        $type = $this->deleteTargetType;
        $id = $this->deleteTargetId;

        $this->cancelDelete();

        if ($type === 'block') {
            $this->deleteBlock($id);

            return;
        }

        if ($type === 'song') {
            $this->deleteSong($id);
        }
    }

    public function moveSongUp(int $songId): void
    {
        $this->moveSong($songId, -1);
    }

    public function moveSongDown(int $songId): void
    {
        $this->moveSong($songId, 1);
    }

    public function reorderSongs(int $blockId, array $orderedSongIds): void
    {
        $block = $this->ownedBlock($blockId);
        $ids = array_values(array_unique(array_map('intval', $orderedSongIds)));

        if ($ids === []) {
            return;
        }

        $songs = Song::query()
            ->where('repertoire_block_id', $block->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $position = 1;

        foreach ($ids as $id) {
            if (! isset($songs[$id])) {
                continue;
            }

            $song = $songs[$id];
            $song->sort_order = $position;
            $song->save();
            $position++;
        }
    }

    public function render()
    {
        $this->repertoire->refresh();
        $this->repertoire->load([
            'blocks' => fn ($query) => $query->orderBy('sort_order'),
            'blocks.songs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        foreach ($this->repertoire->blocks as $block) {
            if (! isset($this->newSongs[$block->id])) {
                $this->newSongs[$block->id] = [
                    'artist_name' => '',
                    'title' => '',
                    'key_tonic' => '',
                    'key_mode' => '',
                    'bpm' => '',
                    'intro_excerpt' => '',
                ];
            }

            foreach ($block->songs as $song) {
                if (($this->editingSongs[$song->id] ?? false) && ! isset($this->songDrafts[$song->id])) {
                    $this->songDrafts[$song->id] = [
                        'artist_name' => $song->artist_name,
                        'title' => $song->title,
                        'key_tonic' => $song->key_tonic ?? '',
                        'key_mode' => $song->key_mode ?? '',
                        'bpm' => $song->bpm ? (string) $song->bpm : '',
                        'intro_excerpt' => $song->intro_excerpt ?? '',
                    ];
                }
            }
        }

        if ($this->activeBlockId === null && $this->repertoire->blocks->isNotEmpty()) {
            $this->activeBlockId = $this->repertoire->blocks->first()->id;
        }

        return view('livewire.repertoire-editor');
    }

    private function songRules(): array
    {
        return [
            'artist_name' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:120'],
            'key_tonic' => ['nullable', 'regex:/^[A-G](#|b)?$/'],
            'key_mode' => ['nullable', 'in:major,minor'],
            'bpm' => ['nullable', 'integer', 'min:30', 'max:300'],
            'intro_excerpt' => ['nullable', 'string', 'max:1200'],
        ];
    }

    private function ownedBlock(int $blockId): RepertoireBlock
    {
        return RepertoireBlock::query()
            ->where('repertoire_id', $this->repertoire->id)
            ->findOrFail($blockId);
    }

    private function ownedSong(int $songId): Song
    {
        return Song::query()
            ->whereKey($songId)
            ->whereHas('block', function ($query): void {
                $query->where('repertoire_id', $this->repertoire->id);
            })
            ->firstOrFail();
    }

    private function moveBlock(int $blockId, int $direction): void
    {
        $items = RepertoireBlock::query()
            ->where('repertoire_id', $this->repertoire->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $items->search(fn (RepertoireBlock $item) => $item->id === $blockId);

        if ($index === false) {
            return;
        }

        $swapIndex = $index + $direction;

        if (! isset($items[$swapIndex])) {
            return;
        }

        $current = $items[$index];
        $target = $items[$swapIndex];

        [$current->sort_order, $target->sort_order] = [$target->sort_order, $current->sort_order];

        $current->save();
        $target->save();
    }

    private function moveSong(int $songId, int $direction): void
    {
        $song = $this->ownedSong($songId);

        $items = Song::query()
            ->where('repertoire_block_id', $song->repertoire_block_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $items->search(fn (Song $item) => $item->id === $songId);

        if ($index === false) {
            return;
        }

        $swapIndex = $index + $direction;

        if (! isset($items[$swapIndex])) {
            return;
        }

        $current = $items[$index];
        $target = $items[$swapIndex];

        [$current->sort_order, $target->sort_order] = [$target->sort_order, $current->sort_order];

        $current->save();
        $target->save();
    }

    private function parseToneSignature(string $signature): array
    {
        if ($signature === '' || ! str_contains($signature, '|')) {
            return ['', ''];
        }

        [$tonic, $mode] = explode('|', $signature, 2);

        if (! in_array($mode, ['major', 'minor'], true)) {
            return ['', ''];
        }

        $allowedTones = array_merge($this->majorTones, $this->minorTones);

        if (! in_array($tonic, $allowedTones, true)) {
            return ['', ''];
        }

        return [$tonic, $mode];
    }

    public function toneShort(?string $tonic, ?string $mode): string
    {
        if (! $tonic) {
            return '-';
        }

        if ($mode === 'minor') {
            return $tonic.'m';
        }

        return $tonic;
    }
}
