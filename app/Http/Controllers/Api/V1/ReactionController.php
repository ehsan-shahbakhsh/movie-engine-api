<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DislikeReactionRequest;
use App\Http\Requests\Api\V1\LikeReactionRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Series;

class ReactionController extends Controller
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
        'comment' => Comment::class,
    ];

    public function like(LikeReactionRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $model = $modelClass::findOrFail($id);

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

        return ApiResponse::success([
            'likes_count' => $model->likes()->count(),
            'dislikes_count' => $model->dislikes()->count(),
            'user_reaction' => $userReaction,
        ], $message);
    }

    public function dislike(DislikeReactionRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $model = $modelClass::findOrFail($id);

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

        return ApiResponse::success([
            'likes_count' => $model->likes()->count(),
            'dislikes_count' => $model->dislikes()->count(),
            'user_reaction' => $userReaction,
        ], $message);
    }
}
