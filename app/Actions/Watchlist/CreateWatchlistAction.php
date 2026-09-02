<?php

namespace App\Actions\Watchlist;

use App\Exceptions\Watchlist\WatchlistAlreadyExistsException;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\Watchlist;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class CreateWatchlistAction
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
    ];

    /**
     * @throws WatchlistAlreadyExistsException
     */
    public function execute(User $user, int $modelId, string $modelType): Watchlist
    {
        if (!array_key_exists($modelType, self::MORPH_MAP)) {
            throw new InvalidArgumentException("Invalid watchable type: {$modelType}");
        }

        $modelClass = self::MORPH_MAP[$modelType];

        $modelClass::findOrFail($modelId);

        $exists = $user->watchlists()
            ->where('watchable_id', $modelId)
            ->where('watchable_type', $modelClass)
            ->exists();

        if ($exists) {
            throw new WatchlistAlreadyExistsException;
        }

        return $user->watchlists()->create([
            'watchable_id' => $modelId,
            'watchable_type' => $modelClass,
        ]);
    }
}
