<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class CreateCommentAction
{
    public function execute(User $user, Model $commentable, string $body, ?int $replyTo, bool $isSpoiler): Comment
    {
        if (!method_exists($commentable, 'comments')) {
            throw new InvalidArgumentException(
                sprintf('The model [%s] does not support comments. Missing comments() method.', $commentable::class)
            );
        }

        return $commentable->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
            'parent_id' => $replyTo,
            'is_spoiler' => $isSpoiler,
        ]);
    }
}
