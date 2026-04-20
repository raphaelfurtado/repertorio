<?php

namespace App\Http\Controllers;

use App\Models\Repertoire;

class RepertoireShareController extends Controller
{
    public function show(string $token)
    {
        $repertoire = Repertoire::query()
            ->with([
                'blocks' => fn ($query) => $query->orderBy('sort_order'),
                'blocks.songs' => fn ($query) => $query->orderBy('sort_order'),
            ])
            ->where('share_token', $token)
            ->where('is_public', true)
            ->firstOrFail();

        return view('repertoires.public', [
            'repertoire' => $repertoire,
        ]);
    }
}
