<?php

use App\Models\{Watchlist, User, AgeRating, Language, Movie, Series};
use Laravel\Sanctum\Sanctum;

it('returns a paginated list of watchlist items for authenticated user', function () {
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

    Watchlist::factory()
        ->count(20)
        ->for($user)
        ->sequence(static fn($sequence) => [
            'watchable_id' => $items->get($sequence->index)->id,
            'watchable_type' => $items->get($sequence->index)::class,
        ])
        ->create();

    $response = $this->getJson('/api/v1/watchlists');

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

it('returns an unauthorized error when fetching watchlist without authentication', function () {
    $response = $this->getJson('/api/v1/watchlists');

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

it('adds an item to the watchlist successfully', function () {
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

    $response = $this->postJson('/api/v1/watchlists', $payload);

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

    $this->assertDatabaseHas('watchlists', [
        'user_id' => $user->id,
        'watchable_id' => $movie->id,
        'watchable_type' => Movie::class,
    ]);
});

it('returns an unauthorized error when adding an item to the watchlist without authentication', function () {
    $response = $this->postJson('/api/v1/watchlists');

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

    $response = $this->postJson('/api/v1/watchlists', $payload);

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

it('returns a conflict error if the item is already in the watchlist', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $movie = Movie::factory()
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create();

    Watchlist::factory()
        ->for($user)
        ->for($movie, 'watchable')
        ->create();

    $payload = [
        'id' => $movie->id,
        'type' => 'movie',
    ];

    $response = $this->postJson('/api/v1/watchlists', $payload);

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

it('returns a not found error when trying to add a non-existent item to the watchlist', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'id' => 1,
        'type' => 'movie',
    ];

    $response = $this->postJson('/api/v1/watchlists', $payload);

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

it('removes an item from the watchlist successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $watchlist = Watchlist::factory()
        ->for($user)
        ->create();

    $response = $this->deleteJson("/api/v1/watchlists/{$watchlist->id}");

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

    $this->assertDatabaseMissing('watchlists', ['id' => $watchlist->id]);
});

it('returns an unauthorized error when deleting a watchlist item without authentication', function () {
    $response = $this->deleteJson('/api/v1/watchlists/1');

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

it('returns a forbidden error when attempting to delete another user\'s watchlist item', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $anotherUser = User::factory()->create();
    $watchlist = Watchlist::factory()
        ->for($anotherUser)
        ->create();

    $response = $this->deleteJson("/api/v1/watchlists/{$watchlist->id}");

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

it('returns a not found error when trying to delete a non-existent watchlist record', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/v1/watchlists/1');

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

