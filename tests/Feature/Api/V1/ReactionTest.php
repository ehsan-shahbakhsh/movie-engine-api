<?php

use App\Enums\ReactionType;
use App\Models\{User, Movie, Reaction, Series, Comment};
use Laravel\Sanctum\Sanctum;

dataset('reactionables', [
    'movie'   => [Movie::class, 'movie'],
    'series'  => [Series::class, 'series'],
    'comment' => [Comment::class, 'comment'],
]);

it('can like a model successfully', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/like', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 1,
            'data.dislikes_count' => 0,
            'data.user_reaction' => 'like',
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 1);
    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Like,
    ]);
})->with('reactionables');

it('can remove a like successfully', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    Reaction::factory()
        ->for($user)
        ->for($model, 'reactionable')
        ->create(['type' => ReactionType::Like]);

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/like', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 0,
            'data.dislikes_count' => 0,
            'data.user_reaction' => null,
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 0);
    $this->assertDatabaseMissing('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Like,
    ]);
})->with('reactionables');

it('can change a dislike to a like', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    Reaction::factory()
        ->for($user)
        ->for($model, 'reactionable')
        ->create(['type' => ReactionType::Dislike]);

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/like', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 1,
            'data.dislikes_count' => 0,
            'data.user_reaction' => 'like',
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 1);
    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Like,
    ]);
})->with('reactionables');

it('can dislike a model successfully', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/dislike', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 0,
            'data.dislikes_count' => 1,
            'data.user_reaction' => 'dislike',
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 1);
    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Dislike,
    ]);
})->with('reactionables');

it('can remove a dislike successfully', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    Reaction::factory()
        ->for($user)
        ->for($model, 'reactionable')
        ->create(['type' => ReactionType::Dislike]);

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/dislike', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 0,
            'data.dislikes_count' => 0,
            'data.user_reaction' => null,
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 0);
    $this->assertDatabaseMissing('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Dislike,
    ]);
})->with('reactionables');

it('can change a like to a dislike', function (string $modelClass, string $type) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $model = $modelClass::factory()->create();

    Reaction::factory()
        ->for($user)
        ->for($model, 'reactionable')
        ->create(['type' => ReactionType::Like]);

    $payload = [
        'id' => $model->id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/reactions/dislike', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.likes_count' => 0,
            'data.dislikes_count' => 1,
            'data.user_reaction' => 'dislike',
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('reactions', 1);
    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $model->id,
        'reactionable_type' => $modelClass,
        'type' => ReactionType::Dislike,
    ]);
})->with('reactionables');

it('returns 401 when unauthorized', function (string $endpoint) {
    $response = $this->postJson($endpoint);

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
})->with([
    'like endpoint' => '/api/v1/reactions/like',
    'dislike endpoint' => '/api/v1/reactions/dislike',
]);

it('returns 422 when data is invalid', function (string $endpoint, int|string|null $id, ?string $type, string $errorField) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'id' => $id,
        'type' => $type,
    ];

    $response = $this->postJson($endpoint, $payload);

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
    'like endpoint' => '/api/v1/reactions/like',
    'dislike endpoint' => '/api/v1/reactions/dislike',
])->with([
    'missing id' => [null, 'movie', 'id'],
    'missing type' => [1, null, 'type'],
    'invalid id' => ['not-a-number', 'movie', 'id'],
    'invalid type' => [1, 'book', 'type'],
]);

it('returns 404 when model is not found', function (string $endpoint) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'id' => 9999,
        'type' => 'movie',
    ];

    $response = $this->postJson($endpoint, $payload);

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
})->with([
    'like endpoint' => '/api/v1/reactions/like',
    'dislike endpoint' => '/api/v1/reactions/dislike',
]);
