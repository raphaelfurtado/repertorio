<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepertoireBlock extends Model
{
    protected $fillable = [
        'repertoire_id',
        'name',
        'sort_order',
    ];

    public function repertoire(): BelongsTo
    {
        return $this->belongsTo(Repertoire::class);
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class)->orderBy('sort_order');
    }
}
