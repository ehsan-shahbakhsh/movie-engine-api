<?php

namespace App\Models;

use App\Http\Resources\Api\V1\WatchlistCollection;
use App\Http\Resources\Api\V1\WatchlistResource;
use Database\Factories\WatchlistFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Attributes\UseResourceCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseResource(WatchlistResource::class)]
#[UseResourceCollection(WatchlistCollection::class)]
#[Fillable(['user_id', 'watchable_id', 'watchable_type'])]
class Watchlist extends Model
{
    /** @use HasFactory<WatchlistFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }
}
