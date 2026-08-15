<?php

use App\Models\{Movie, AgeRating, Language, Genre, Person, Video};
use App\Enums\MovieStatus;

it('returns a paginated list of movies', function () {
    Movie::factory()
        ->count(20)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create(['status' => MovieStatus::Published]);

    $response = $this->getJson('/api/v1/movies');

    $response
        ->assertOk()
        ->assertJsonCount(15, 'data.items')
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'title',
                        'original_title',
                        'slug',
                        'poster',
                        'release_year',
                        'release_date',
                        'duration_minutes',
                        'status',
                        'original_language',
                        'age_rating',
                        'countries',
                        'genres' => [
                            '*' => ['id', 'name', 'slug'],
                        ],
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

it('excludes draft and archived movies from the list', function () {
    Movie::factory()
        ->count(20)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['status' => MovieStatus::Draft],
            ['status' => MovieStatus::Published],
            ['status' => MovieStatus::ComingSoon],
            ['status' => MovieStatus::Archived],
        )
        ->create();

    $response = $this->getJson('/api/v1/movies');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data.items')
        ->assertJsonMissing(['status' => MovieStatus::Draft])
        ->assertJsonMissing(['status' => MovieStatus::Archived])
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'title',
                        'original_title',
                        'slug',
                        'poster',
                        'release_year',
                        'release_date',
                        'duration_minutes',
                        'status',
                        'original_language',
                        'age_rating',
                        'countries',
                        'genres' => [
                            '*' => ['id', 'name', 'slug'],
                        ],
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

it('can search movies by title and original title', function () {
    Movie::factory()
        ->count(4)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['title' => 'Test Movie', 'original_title' => 'Not Related 1'],
            ['title' => 'Not Related 2', 'original_title' => 'Test 2'],
            ['title' => 'Inception', 'original_title' => 'Inception Orig'],
            ['title' => 'Batman', 'original_title' => 'Batman Orig'],
        )
        ->create(['status' => MovieStatus::Published]);

    $response = $this->getJson('/api/v1/movies?search=Test');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.items.0.title', 'Test Movie')
        ->assertJsonPath('data.items.1.original_title', 'Test 2')
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'title',
                        'original_title',
                        'slug',
                        'poster',
                        'release_year',
                        'release_date',
                        'duration_minutes',
                        'status',
                        'original_language',
                        'age_rating',
                        'countries',
                        'genres' => [
                            '*' => ['id', 'name', 'slug'],
                        ],
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

it('returns movie details correctly by slug', function () {
    Movie::factory()
        ->count(2)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->hasAttached(Genre::factory()->count(3), relationship: 'genres')
        ->hasAttached(Person::factory()->count(3), relationship: 'persons')
        ->has(Video::factory()->count(3), 'videos')
        ->sequence(
            ['title' => 'Test Movie', 'original_title' => 'Not Related 1', 'slug' => 'test-movie'],
            ['title' => 'Not Related 2', 'original_title' => 'Test 2', 'slug' => 'test-2'],
        )
        ->create(['status' => MovieStatus::Published]);

    $response = $this->getJson('/api/v1/movies/test-movie');

    $response
        ->assertOk()
        ->assertJsonPath('data.title', 'Test Movie')
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'id',
                'title',
                'original_title',
                'slug',
                'poster',
                'synopsis',
                'release_year',
                'release_date',
                'duration_minutes',
                'status',
                'original_language',
                'languages',
                'age_rating',
                'countries',
                'genres' => [
                    '*' => ['id', 'name', 'slug'],
                ],
                'persons' => [
                    '*' => ['id', 'name', 'original_name', 'slug', 'profile', 'department', 'job', 'character_name'],
                ],
                'videos' => [
                    '*' => ['id', 'name', 'type', 'is_official', 'thumbnail', 'video'],
                ],
                'backdrop',
                'logo',
                'gallery',
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 404 if movie does not exist', function () {
    Movie::factory()
        ->count(2)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['title' => 'Test Movie', 'slug' => 'test-movie-2'],
            ['title' => 'Test 2', 'slug' => 'test-2'],
        )
        ->create(['status' => MovieStatus::Published]);

    $response = $this->getJson('/api/v1/movies/test-movie');

    $response
        ->assertNotFound()
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 404 for draft and archived movies', function (MovieStatus $status, string $slug) {
    Movie::factory()
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create(['status' => $status, 'slug' => $slug]);

    $response = $this->getJson("/api/v1/movies/{$slug}");

    $response
        ->assertNotFound()
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
})->with([
    'Draft movie' => [MovieStatus::Draft, 'test-movie'],
    'Archived movie' => [MovieStatus::Archived, 'test-2'],
]);
