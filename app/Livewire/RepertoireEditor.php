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
    }

    public function togglePublic(): void
    {
        $this->repertoire->is_public = ! $this->repertoire->is_public;
        $this->repertoire->save();
    }

    public function addBlock(): void
    {
        $validated = $this->validate([
            'newBlockName' => ['required', 'string', 'max:120'],
        ]);

        $maxSort = (int) RepertoireBlock::query()
            ->where('repertoire_id', $this->repertoire->id)
            ->max('sort_order');

        RepertoireBlock::create([
            'repertoire_id' => $this->repertoire->id,
            'name' => $validated['newBlockName'],
            'sort_order' => $maxSort + 1,
        ]);

        $this->newBlockName = '';
    }

    public function deleteBlock(int $blockId): void
    {
        $this->ownedBlock($blockId)->delete();
    }

    public function moveBlockUp(int $blockId): void
    {
        $this->moveBlock($blockId, -1);
    }

    public function moveBlockDown(int $blockId): void
    {
        $this->moveBlock($blockId, 1);
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
    }

    public function updateSongField(int $songId, string $field, string $value): void
    {
        $allowed = ['artist_name', 'title', 'key_tonic', 'key_mode', 'bpm', 'intro_excerpt'];

        if (! in_array($field, $allowed, true)) {
            return;
        }

        $song = $this->ownedSong($songId);

        $rules = [
            'artist_name' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:120'],
            'key_tonic' => ['nullable', 'regex:/^[A-G](#|b)?$/'],
            'key_mode' => ['nullable', 'in:major,minor'],
            'bpm' => ['nullable', 'integer', 'min:30', 'max:300'],
            'intro_excerpt' => ['nullable', 'string', 'max:1200'],
        ];

        $validated = validator([$field => $value], [$field => $rules[$field]])->validate();
        $song->{$field} = $validated[$field] === '' ? null : $validated[$field];
        $song->save();
    }

    public function deleteSong(int $songId): void
    {
        $this->ownedSong($songId)->delete();
    }

    public function moveSongUp(int $songId): void
    {
        $this->moveSong($songId, -1);
    }

    public function moveSongDown(int $songId): void
    {
        $this->moveSong($songId, 1);
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
        }

        return view('livewire.repertoire-editor');
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
}
