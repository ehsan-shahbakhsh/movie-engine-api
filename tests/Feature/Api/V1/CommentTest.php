<?php

use App\Models\{Movie, Comment, User, Series};
use App\Enums\{CommentStatus, MovieStatus, SeriesPublishStatus};
use Laravel\Sanctum\Sanctum;

describe('movies', function () {
    it('returns a paginated list of comments', function () {
        $movie = Movie::factory()->published()->create();

        Comment::factory()
            ->count(20)
            ->for($movie, 'commentable')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(15, 'data.items')
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 404 when fetching comments for a non-existent item', function () {
        $response = $this->getJson('/api/v1/movies/invalid-slug/comments');

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns only approved comments', function () {
        $movie = Movie::factory()->published()->create();

        $approvedComment = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create();
        $pendingComment = Comment::factory()
            ->for($movie, 'commentable')
            ->create(['status' => CommentStatus::Pending]);

        $response = $this->getJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonFragment(['id' => $approvedComment->id])
            ->assertJsonMissing(['id' => $pendingComment->id])
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns only root comments at the top level', function () {
        $movie = Movie::factory()->published()->create();

        $firstComment = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create([
                'parent_id' => null,
                'created_at' => now()->subMinutes(5),
            ]);

        $parentComment = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create(['parent_id' => null]);

        Comment::factory()
            ->for($movie, 'commentable')
            ->for($parentComment, 'parent')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPaths([
                'data.items.0.id' => $parentComment->id,
                'data.items.1.id' => $firstComment->id,
            ])
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns comments with correctly loaded replies', function () {
        $movie = Movie::factory()->published()->create();

        $parentComment1 = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create([
                'parent_id' => null,
                'created_at' => now()->subMinutes(),
            ]);
        $replyComment1 = Comment::factory()
            ->for($movie, 'commentable')
            ->for($parentComment1, 'parent')
            ->approved()
            ->create();

        $parentComment2 = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create(['parent_id' => null]);
        $replyComment2 = Comment::factory()
            ->for($movie, 'commentable')
            ->for($parentComment2, 'parent')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPaths([
                'data.items.0.id' => $parentComment2->id,
                'data.items.0.replies.0.id' => $replyComment2->id,
                'data.items.1.id' => $parentComment1->id,
                'data.items.1.replies.0.id' => $replyComment1->id,
            ])
            ->assertJsonCount(1, 'data.items.0.replies')
            ->assertJsonCount(1, 'data.items.1.replies')
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('successfully stores a root comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => null,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertCreated()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
            'parent_id' => null,
        ]);
    });

    it('successfully stores a reply comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $parentComment = Comment::factory()
            ->for($movie, 'commentable')
            ->approved()
            ->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => $parentComment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertCreated()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
            'parent_id' => $parentComment->id,
        ]);
    });

    it('returns 404 when creating a comment for a non-existent item', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/movies/invalid-slug/comments');

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 401 when creating a comment without authentication', function () {
        $response = $this->postJson('/api/v1/movies/invalid-slug/comments');

        $response
            ->assertUnauthorized()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 403 when creating a comment with unverified email', function () {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertForbidden()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 422 when comment payload fails validation', function (?string $body, int|string|null $replyTo, ?bool $isSpoiler, string $errorField) {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $payload = [
            'body' => $body,
            'reply_to' => $replyTo,
            'is_spoiler' => $isSpoiler,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor($errorField);
    })->with([
        'missing body' => [null, null, false, 'body'],
        'short body' => ['ab', null, false, 'body'],
        'long body' => [str_repeat('a', 1001), null, false, 'body'],
        'invalid reply'   => ['valid text', 'invalid-slug', false, 'reply_to'],
        'invalid spoiler' => ['valid text', null, null, 'is_spoiler'],
    ]);

    it('returns 404 when replying to a non-existent comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => 9999,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 422 when replying to a comment belonging to another item', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $anotherMovie = Movie::factory()->published()->create();
        $comment = Comment::factory()
            ->for($anotherMovie, 'commentable')
            ->approved()
            ->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => $comment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 404 when replying to an unapproved comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->published()->create();

        $unapprovedComment = Comment::factory()
            ->for($movie, 'commentable')
            ->create(['status' => CommentStatus::Pending]);

        $payload = [
            'body' => 'valid text',
            'reply_to' => $unapprovedComment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 404 when creating a comment for a movie that is not visible', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->create(['status' => MovieStatus::Draft]);

        $response = $this->postJson("/api/v1/movies/{$movie->slug}/comments");

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });
});

describe('series', function () {
    it('returns a paginated list of comments', function () {
        $series = Series::factory()->published()->create();

        Comment::factory()
            ->count(20)
            ->for($series, 'commentable')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(15, 'data.items')
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 404 when fetching comments for a non-existent item', function () {
        $response = $this->getJson('/api/v1/series/invalid-slug/comments');

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns only approved comments', function () {
        $series = Series::factory()->published()->create();

        $approvedComment = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create();
        $pendingComment = Comment::factory()
            ->for($series, 'commentable')
            ->create(['status' => CommentStatus::Pending]);

        $response = $this->getJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonFragment(['id' => $approvedComment->id])
            ->assertJsonMissing(['id' => $pendingComment->id])
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns only root comments at the top level', function () {
        $series = Series::factory()->published()->create();

        $firstComment = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create([
                'parent_id' => null,
                'created_at' => now()->subMinutes(5),
            ]);

        $parentComment = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create(['parent_id' => null]);

        Comment::factory()
            ->for($series, 'commentable')
            ->for($parentComment, 'parent')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPaths([
                'data.items.0.id' => $parentComment->id,
                'data.items.1.id' => $firstComment->id,
            ])
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns comments with correctly loaded replies', function () {
        $series = Series::factory()->published()->create();

        $parentComment1 = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create([
                'parent_id' => null,
                'created_at' => now()->subMinutes(),
            ]);
        $replyComment1 = Comment::factory()
            ->for($series, 'commentable')
            ->for($parentComment1, 'parent')
            ->approved()
            ->create();

        $parentComment2 = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create(['parent_id' => null]);
        $replyComment2 = Comment::factory()
            ->for($series, 'commentable')
            ->for($parentComment2, 'parent')
            ->approved()
            ->create();

        $response = $this->getJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPaths([
                'data.items.0.id' => $parentComment2->id,
                'data.items.0.replies.0.id' => $replyComment2->id,
                'data.items.1.id' => $parentComment1->id,
                'data.items.1.replies.0.id' => $replyComment1->id,
            ])
            ->assertJsonCount(1, 'data.items.0.replies')
            ->assertJsonCount(1, 'data.items.1.replies')
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'user_name',
                            'body',
                            'is_official',
                            'is_spoiler',
                            'replies',
                            'created_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'from',
                        'last_page',
                        'per_page',
                        'to',
                        'total',
                        'has_more',
                    ],
                ],
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('successfully stores a root comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => null,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertCreated()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $series->id,
            'commentable_type' => Series::class,
            'parent_id' => null,
        ]);
    });

    it('successfully stores a reply comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $parentComment = Comment::factory()
            ->for($series, 'commentable')
            ->approved()
            ->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => $parentComment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertCreated()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $series->id,
            'commentable_type' => Series::class,
            'parent_id' => $parentComment->id,
        ]);
    });

    it('returns 404 when creating a comment for a non-existent item', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/series/invalid-slug/comments');

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 401 when creating a comment without authentication', function () {
        $response = $this->postJson('/api/v1/series/invalid-slug/comments');

        $response
            ->assertUnauthorized()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 403 when creating a comment with unverified email', function () {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertForbidden()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });

    it('returns 422 when comment payload fails validation', function (?string $body, int|string|null $replyTo, ?bool $isSpoiler, string $errorField) {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $payload = [
            'body' => $body,
            'reply_to' => $replyTo,
            'is_spoiler' => $isSpoiler,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor($errorField);
    })->with([
        'missing body' => [null, null, false, 'body'],
        'short body' => ['ab', null, false, 'body'],
        'long body' => [str_repeat('a', 1001), null, false, 'body'],
        'invalid reply'   => ['valid text', 'invalid-slug', false, 'reply_to'],
        'invalid spoiler' => ['valid text', null, null, 'is_spoiler'],
    ]);

    it('returns 404 when replying to a non-existent comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => 9999,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 422 when replying to a comment belonging to another item', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $anotherSeries = Series::factory()->published()->create();
        $comment = Comment::factory()
            ->for($anotherSeries, 'commentable')
            ->approved()
            ->create();

        $payload = [
            'body' => 'valid text',
            'reply_to' => $comment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 404 when replying to an unapproved comment', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->published()->create();

        $unapprovedComment = Comment::factory()
            ->for($series, 'commentable')
            ->create(['status' => CommentStatus::Pending]);

        $payload = [
            'body' => 'valid text',
            'reply_to' => $unapprovedComment->id,
            'is_spoiler' => false,
        ];

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments", $payload);

        $response
            ->assertUnprocessable()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ])
            ->assertJsonValidationErrorFor('reply_to');
    });

    it('returns 404 when creating a comment for a series that is not visible', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $series = Series::factory()->create(['publish_status' => SeriesPublishStatus::Archived]);

        $response = $this->postJson("/api/v1/series/{$series->slug}/comments");

        $response
            ->assertNotFound()
            ->assertExactJsonStructure([
                'success',
                'code',
                'message',
                'data',
                'meta',
                'errors',
                'error_code',
            ]);
    });
});

it('successfully deletes a comment', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $comment = Comment::factory()->for($user)->create();

    $response = $this->deleteJson("/api/v1/comments/{$comment->id}");

    $response
        ->assertOk()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('returns 403 when deleting another user\'s comment', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $anotherUser = User::factory()->create();
    $anotherUserComment = Comment::factory()->for($anotherUser)->create();

    $response = $this->deleteJson("/api/v1/comments/{$anotherUserComment->id}");

    $response
        ->assertForbidden()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 404 when deleting a non-existent comment', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/v1/comments/1');

    $response
        ->assertNotFound()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 403 when deleting a comment with unverified email', function () {
    $user = User::factory()->unverified()->create();
    Sanctum::actingAs($user);

    $comment = Comment::factory()->for($user)->create();

    $response = $this->deleteJson("/api/v1/comments/{$comment->id}");

    $response
        ->assertForbidden()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 401 when deleting a comment without authentication', function () {
    $response = $this->deleteJson('/api/v1/comments/1');

    $response
        ->assertUnauthorized()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});
