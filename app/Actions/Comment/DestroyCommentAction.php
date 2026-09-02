<?php

namespace App\Actions\Comment;

use App\Models\Comment;

final class DestroyCommentAction
{
    public function execute(Comment $comment): void
    {
        $comment->delete();
    }
}
