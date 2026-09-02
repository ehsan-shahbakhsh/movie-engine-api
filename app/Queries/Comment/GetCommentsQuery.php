<?php

namespace App\Queries\Comment;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

final class GetCommentsQuery
{
    public function execute(Model $commentable, ?User $user, int $page = 1): LengthAwarePaginator
    {
        if (!method_exists($commentable, 'comments')) {
            throw new InvalidArgumentException(
                sprintf('The model [%s] does not support comments. Missing comments() method.', $commentable::class)
            );
        }

        return $commentable
            ->comments()
            ->when($user != null, static function ($query) use ($user) {
                $query->addSelect([
                    'user_reaction' => Reaction::query()
                        ->select('type')
                        ->whereColumn('reactionable_id', 'comments.id')
                        ->where('reactionable_type', Comment::class)
                        ->where('user_id', $user->id),
                ]);
            })
            ->with(['user', 'allApprovedReplies'])
            ->withCount(['likes', 'dislikes'])
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Approved)
            ->latest()
            ->paginate(page: $page)
            ->through(static function (Comment $comment) {
                $comment->user_reaction ??= null;

                return $comment;
            });
    }
}
