<?php

namespace App\Actions\Reaction;

use App\Data\Reaction\ReactionResultData;
use App\Enums\ReactionType;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;

final class DislikeAction
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
        'comment' => Comment::class,
    ];

    public function execute(User $user, int $modelId, string $modelType): ReactionResultData
    {
        $modelClass = self::MORPH_MAP[$modelType];

        $model = $modelClass::findOrFail($modelId);

        $reaction = $model->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($reaction) {
            if ($reaction->type === ReactionType::Dislike) {
                $reaction->delete();
                $message = 'دیس‌لایک شما حذف شد.';
                $userReaction = null;
            } else {
                $reaction->update(['type' => ReactionType::Dislike]);
                $message = 'رأی شما به دیس‌لایک تغییر یافت.';
                $userReaction = 'dislike';
            }
        } else {
            $model->reactions()->create([
                'user_id' => $user->id,
                'type' => ReactionType::Dislike,
            ]);
            $message = 'دیس‌لایک شما با موفقیت ثبت شد.';
            $userReaction = 'dislike';
        }

        return new ReactionResultData(
            message: $message,
            likesCount: $model->likes()->count(),
            dislikesCount: $model->dislikes()->count(),
            userReaction: $userReaction,
        );
    }
}
