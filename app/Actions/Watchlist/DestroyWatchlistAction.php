<?php

namespace App\Actions\Watchlist;

use App\Models\Watchlist;

final class DestroyWatchlistAction
{
    public function execute(Watchlist $watchlist): void
    {
        $watchlist->delete();
    }
}
