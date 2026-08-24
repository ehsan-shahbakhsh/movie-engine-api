<?php

use App\Models\{Favorite, User, AgeRating, Language, Movie, Series};
use Laravel\Sanctum\Sanctum;

it('returns a paginated list of favorites for authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $ageRating = AgeRating::factory()->create(['name' => 'PG-13']);
    $originalLanguage = Language::factory()->create(['name' => 'Persian', 'code' => 'fa']);

    $movies = Movie::factory()
        ->count(10)
        ->create(['age_rating_id' => $ageRating->id, 'original_language_id' => $originalLanguage->id]);
    $series = Series::factory()
        ->count(10)
        ->create(['age_rating_id' => $ageRating->id, 'original_language_id' => $originalLanguage->id]);

    $items = $movies->concat($series);

    Favorite::factory()
        ->count(20)
        ->for($user)
        ->sequence(static fn($sequence) => [
            'favoritable_id' => $items->get($sequence->index)->id,
            'favoritable_type' => $items->get($sequence->index)::class,
        ])
        ->create();

    $response = $this->getJson('/api/v1/favorites');

    $response
        ->assertOk()
        ->assertJsonCount(15, 'data.items')
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'items' => [
                    '*' => ['id', 'type', 'item', 'created_at'],
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

it('returns an unauthorized error when fetching favorites without authentication', function () {
    $response = $this->getJson('/api/v1/favorites');

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

it('adds an item to the favorites list successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $movie = Movie::factory()
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create();

    $payload = [
        'id' => $movie->id,
        'type' => 'movie',
    ];

    $response = $this->postJson('/api/v1/favorites', $payload);

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

    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'favoritable_id' => $movie->id,
        'favoritable_type' => Movie::class,
    ]);
});

it('returns an unauthorized error when adding a favorite without authentication', function () {
    $response = $this->postJson('/api/v1/favorites');

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

it('returns validation errors when the store payload is invalid', function (int|string|null $id, ?string $type, string $errorField) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'id' => $id,
        'type' => $type,
    ];

    $response = $this->postJson('/api/v1/favorites', $payload);

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
    'missing id' => [null, 'movie', 'id'],
    'missing type' => [1, null, 'type'],
    'invalid id' => ['not-a-number', 'movie', 'id'],
    'invalid type' => [1, 'book', 'type'],
]);

it('returns a conflict error if the item is already in the favorites list', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $movie = Movie::factory()
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create();

    Favorite::factory()
        ->for($user)
        ->for($movie, 'favoritable')
        ->create();

    $payload = [
        'id' => $movie->id,
        'type' => 'movie',
    ];

    $response = $this->postJson('/api/v1/favorites', $payload);

    $response
        ->assertConflict()
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

it('returns a not found error when trying to favorite a non-existent item', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'id' => 1,
        'type' => 'movie',
    ];

    $response = $this->postJson('/api/v1/favorites', $payload);

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

it('removes an item from the favorites list successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $favorite = Favorite::factory()
        ->for($user)
        ->create();

    $response = $this->deleteJson("/api/v1/favorites/{$favorite->id}");

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

    $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
});

it('returns an unauthorized error when deleting a favorite without authentication', function () {
    $response = $this->deleteJson('/api/v1/favorites/1');

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

it('returns a forbidden error when attempting to delete another user\'s favorite', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $anotherUser = User::factory()->create();
    $favorite = Favorite::factory()
        ->for($anotherUser)
        ->create();

    $response = $this->deleteJson("/api/v1/favorites/{$favorite->id}");

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

it('returns a not found error when trying to delete a non-existent favorite record', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/v1/favorites/1');

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
