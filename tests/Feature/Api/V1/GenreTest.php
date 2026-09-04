<?php

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('returns the entire list of genres', function () {
    Genre::factory()
        ->count(20)
        ->sequence(
            static fn(Sequence $sequence) => [
                'name' => "Genre {$sequence->index}",
                'slug' => "genre-{$sequence->index}",
            ],
        )
        ->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/genres');

    $response
        ->assertOk()
        ->assertJsonCount(20, 'data')
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'movies_count',
                    'series_count',
                ],
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns only active genres', function () {
    Genre::factory()
        ->count(20)
        ->sequence(
            static fn(Sequence $sequence) => [
                'name' => "Genre {$sequence->index}",
                'slug' => "genre-{$sequence->index}",
                'is_active' => $sequence->index % 2 === 0,
            ],
        )
        ->create();

    $response = $this->getJson('/api/v1/genres');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'movies_count',
                    'series_count',
                ],
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns active genres in the correct order', function () {
    Genre::factory()
        ->count(10)
        ->sequence(
            static fn(Sequence $sequence) => [
                'name' => "Genre {$sequence->index}",
                'slug' => "genre-{$sequence->index}",
                'sort_order' => $sequence->index,
            ],
        )
        ->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/genres');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPaths([
            'data.0.name' => 'Genre 0',
            'data.1.name' => 'Genre 1',
            'data.2.name' => 'Genre 2',
            'data.3.name' => 'Genre 3',
            'data.4.name' => 'Genre 4',
            'data.5.name' => 'Genre 5',
            'data.6.name' => 'Genre 6',
            'data.7.name' => 'Genre 7',
            'data.8.name' => 'Genre 8',
            'data.9.name' => 'Genre 9',
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'movies_count',
                    'series_count',
                ],
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});
