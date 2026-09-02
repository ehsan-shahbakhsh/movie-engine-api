<?php

namespace App\Actions\Favorite;

use App\Exceptions\Favorite\FavoriteAlreadyExistsException;
use App\Models\Favorite;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use InvalidArgumentException;

final class CreateFavoriteAction
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
    ];

    /**
     * @throws FavoriteAlreadyExistsException
     */
    public function execute(User $user, int $modelId, string $modelType): Favorite
    {
        if (!array_key_exists($modelType, self::MORPH_MAP)) {
            throw new InvalidArgumentException("Invalid favoritable type: {$modelType}");
        }

        $modelClass = self::MORPH_MAP[$modelType];

        $modelClass::findOrFail($modelId);

        $exists = $user->favorites()
            ->where('favoritable_id', $modelId)
            ->where('favoritable_type', $modelClass)
            ->exists();

        if ($exists) {
            throw new FavoriteAlreadyExistsException;
        }

        return $user->favorites()->create([
            'favoritable_id' => $modelId,
            'favoritable_type' => $modelClass,
        ]);
    }
}
