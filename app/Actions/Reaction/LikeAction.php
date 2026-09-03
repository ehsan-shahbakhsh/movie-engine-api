<?php

namespace App\Actions\Reaction;

use App\Data\Reaction\ReactionResultData;
use App\Enums\ReactionType;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use InvalidArgumentException;

final class LikeAction
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
        'comment' => Comment::class,
    ];

    public function execute(User $user, int $modelId, string $modelType): ReactionResultData
    {
        if (!array_key_exists($modelType, self::MORPH_MAP)) {
            throw new InvalidArgumentException("Invalid reactionable type: {$modelType}");
        }

        $modelClass = self::MORPH_MAP[$modelType];

        $model = $modelClass::findOrFail($modelId);

        $reaction = $model->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($reaction) {
            if ($reaction->type === ReactionType::Like) {
                $reaction->delete();
                $message = 'لایک شما حذف شد.';
                $userReaction = null;
            } else {
                $reaction->update(['type' => ReactionType::Like]);
                $message = 'رأی شما به لایک تغییر یافت.';
                $userReaction = 'like';
            }
        } else {
            $model->reactions()->create([
                'user_id' => $user->id,
                'type' => ReactionType::Like,
            ]);
            $message = 'لایک شما با موفقیت ثبت شد.';
            $userReaction = 'like';
        }

        return new ReactionResultData(
            message: $message,
            likesCount: $model->likes()->count(),
            dislikesCount: $model->dislikes()->count(),
            userReaction: $userReaction,
        );
    }
}
