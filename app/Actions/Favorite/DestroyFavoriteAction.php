<?php

namespace App\Actions\Favorite;

use App\Models\Favorite;

final class DestroyFavoriteAction
{
    public function execute(Favorite $favorite): void
    {
        $favorite->delete();
    }
}
