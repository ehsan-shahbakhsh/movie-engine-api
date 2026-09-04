<?php

namespace App\Actions\Comment;

use App\Contracts\Commentable;
use App\Models\Comment;
use App\Models\User;

final class CreateCommentAction
{
    public function execute(User $user, Commentable $commentable, string $body, ?int $replyTo, bool $isSpoiler): Comment
    {
        return $commentable->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
            'parent_id' => $replyTo,
            'is_spoiler' => $isSpoiler,
        ]);
    }
}
