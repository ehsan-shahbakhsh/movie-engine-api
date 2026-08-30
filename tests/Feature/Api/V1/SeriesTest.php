<?php

use App\Models\{Series, AgeRating, Language, Genre, Person, Video, Season, Episode, DownloadGroup, DownloadLink};
use App\Enums\SeriesPublishStatus;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('returns a paginated list of series', function () {
    Series::factory()
        ->count(20)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create(['publish_status' => SeriesPublishStatus::Published]);

    $response = $this->getJson('/api/v1/series');

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
                        'title',
                        'original_title',
                        'slug',
                        'poster',
                        'release_year',
                        'release_date',
                        'end_date',
                        'publish_status',
                        'production_status',
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

it('excludes draft and archived series from the list', function () {
    Series::factory()
        ->count(20)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['publish_status' => SeriesPublishStatus::Draft],
            ['publish_status' => SeriesPublishStatus::Published],
            ['publish_status' => SeriesPublishStatus::ComingSoon],
            ['publish_status' => SeriesPublishStatus::Archived],
        )
        ->create();

    $response = $this->getJson('/api/v1/series');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data.items')
        ->assertJsonMissing(['publish_status' => SeriesPublishStatus::Draft])
        ->assertJsonMissing(['publish_status' => SeriesPublishStatus::Archived])
        ->assertExactJsonStructure([
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
                        'end_date',
                        'publish_status',
                        'production_status',
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

it('can search series by title and original title', function () {
    Series::factory()
        ->count(4)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['title' => 'Test Series', 'original_title' => 'Not Related 1'],
            ['title' => 'Not Related 2', 'original_title' => 'Test 2'],
            ['title' => 'بازی تاج‌وتخت', 'original_title' => 'Game of Thrones'],
            ['title' => 'بریکینگ بد', 'original_title' => 'Breaking Bad'],
        )
        ->create(['publish_status' => SeriesPublishStatus::Published]);

    $response = $this->getJson('/api/v1/series?search=Test');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.items.0.title', 'Test Series')
        ->assertJsonPath('data.items.1.original_title', 'Test 2')
        ->assertExactJsonStructure([
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
                        'end_date',
                        'publish_status',
                        'production_status',
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

it('returns series details correctly by slug', function () {
    Series::factory()
        ->count(2)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->hasAttached(Genre::factory()->count(3), relationship: 'genres')
        ->hasAttached(Person::factory()->count(3), relationship: 'persons')
        ->has(Video::factory()->count(3), 'videos')
        ->has(
            Season::factory()
                ->sequence(static fn(Sequence $sequence) => ['season_number' => $sequence->index + 1])
                ->has(
                    Episode::factory()
                        ->has(
                            DownloadGroup::factory()
                                ->has(DownloadLink::factory(), 'downloadLinks')
                                ->state(['is_active' => true]),
                            'downloadGroups',
                        ),
                    'episodes',
                ),
            'seasons',
        )
        ->sequence(
            ['title' => 'Test Series', 'original_title' => 'Not Related 1', 'slug' => 'test-series'],
            ['title' => 'Not Related 2', 'original_title' => 'Test 2', 'slug' => 'test-2'],
        )
        ->create(['publish_status' => SeriesPublishStatus::Published]);

    $response = $this->getJson('/api/v1/series/test-series');

    $response
        ->assertOk()
        ->assertJsonPath('data.title', 'Test Series')
        ->assertExactJsonStructure([
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
                'end_date',
                'publish_status',
                'production_status',
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
                'seasons' => [
                    '*' => [
                        'id',
                        'season_number',
                        'title',
                        'release_date',
                        'end_date',
                        'episodes' => [
                            '*' => [
                                'id',
                                'episode_number',
                                'title',
                                'synopsis',
                                'air_date',
                                'duration_minutes',
                                'download_groups' => [
                                    '*' => [
                                        'id',
                                        'title',
                                        'download_links' => [
                                            '*' => ['id', 'quality', 'encoder', 'codec', 'size_in_bytes', 'human_readable_size', 'video'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'backdrop',
                'logo',
                'gallery',
                'likes_count',
                'dislikes_count',
                'user_reaction',
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns 404 if series does not exist', function () {
    Series::factory()
        ->count(2)
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->sequence(
            ['title' => 'Test Series', 'slug' => 'test-series-2'],
            ['title' => 'Test 2', 'slug' => 'test-2'],
        )
        ->create(['publish_status' => SeriesPublishStatus::Published]);

    $response = $this->getJson('/api/v1/series/test-series');

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

it('returns 404 for draft and archived series', function (SeriesPublishStatus $status, string $slug) {
    Series::factory()
        ->for(AgeRating::factory()->state(['name' => 'PG-13']), 'ageRating')
        ->for(Language::factory()->state(['name' => 'Persian', 'code' => 'fa']), 'originalLanguage')
        ->create(['publish_status' => $status, 'slug' => $slug]);

    $response = $this->getJson("/api/v1/series/{$slug}");

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
    'Draft series' => [SeriesPublishStatus::Draft, 'test-series'],
    'Archived series' => [SeriesPublishStatus::Archived, 'test-2'],
]);
