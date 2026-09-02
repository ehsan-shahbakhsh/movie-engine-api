<?php

namespace App\Data\Reaction;

use Spatie\LaravelData\Data;

class ReactionResultData extends Data
{
    public function __construct(
        public readonly string  $message,
        public readonly int     $likesCount,
        public readonly int     $dislikesCount,
        public readonly ?string $userReaction,
    )
    {
    }
}
