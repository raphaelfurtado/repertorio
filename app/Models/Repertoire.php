<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repertoire extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'share_token',
        'is_public',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(RepertoireBlock::class)->orderBy('sort_order');
    }
}
