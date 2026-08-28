<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CommentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMovieCommentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;

class MovieCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Movie $movie)
    {
        $comments = $movie
            ->comments()
            ->with(['user', 'allApprovedReplies'])
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Approved)
            ->latest()
            ->paginate();

        return ApiResponse::success($comments->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieCommentRequest $request, Movie $movie)
    {
        $validated = $request->validated();

        $movie->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'parent_id' => $validated['reply_to'] ?? null,
            'is_spoiler' => $validated['is_spoiler'],
        ]);

        return ApiResponse::created(message: 'نظر شما با موفقیت ثبت شد و پس از تأیید مدیر نمایش داده خواهد شد.');
    }
}
