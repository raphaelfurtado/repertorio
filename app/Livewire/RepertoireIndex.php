<?php

namespace App\Livewire;

use App\Models\Repertoire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class RepertoireIndex extends Component
{
    public string $name = '';

    public function createRepertoire(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $maxSort = (int) Repertoire::query()
            ->where('user_id', Auth::id())
            ->max('sort_order');

        Repertoire::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'share_token' => (string) Str::uuid(),
            'is_public' => false,
            'sort_order' => $maxSort + 1,
        ]);

        $this->name = '';
    }

    public function deleteRepertoire(int $repertoireId): void
    {
        $this->ownedRepertoire($repertoireId)->delete();
    }

    public function togglePublic(int $repertoireId): void
    {
        $repertoire = $this->ownedRepertoire($repertoireId);
        $repertoire->is_public = ! $repertoire->is_public;
        $repertoire->save();
    }

    public function moveUp(int $repertoireId): void
    {
        $this->moveRepertoire($repertoireId, -1);
    }

    public function moveDown(int $repertoireId): void
    {
        $this->moveRepertoire($repertoireId, 1);
    }

    public function render()
    {
        return view('livewire.repertoire-index', [
            'repertoires' => Repertoire::query()
                ->where('user_id', Auth::id())
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    private function moveRepertoire(int $repertoireId, int $direction): void
    {
        $items = Repertoire::query()
            ->where('user_id', Auth::id())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $items->search(fn (Repertoire $item) => $item->id === $repertoireId);

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

    private function ownedRepertoire(int $repertoireId): Repertoire
    {
        return Repertoire::query()
            ->where('user_id', Auth::id())
            ->findOrFail($repertoireId);
    }
}
