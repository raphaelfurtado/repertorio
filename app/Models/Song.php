<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Song extends Model
{
    protected $fillable = [
        'repertoire_block_id',
        'artist_name',
        'title',
        'key_tonic',
        'key_mode',
        'bpm',
        'intro_excerpt',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'bpm' => 'integer',
        ];
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(RepertoireBlock::class, 'repertoire_block_id');
    }
}
