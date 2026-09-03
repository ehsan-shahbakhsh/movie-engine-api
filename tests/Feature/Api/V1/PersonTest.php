<?php

use App\Models\Person;

it('returns a paginated list of persons', function () {
    Person::factory()
        ->count(20)
        ->create();

    $response = $this->getJson('/api/v1/persons');

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
                        'name',
                        'original_name',
                        'slug',
                        'profile',
                        'birth_date',
                        'death_date',
                        'biography',
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

it('can search persons by name and original name', function () {
    Person::factory()
        ->forEachSequence(
            ['name' => 'Christopher Nolan', 'original_name' => 'Not Related 1'],
            ['name' => 'Not Related 2', 'original_name' => 'Quentin Nolan'],
            ['name' => 'Steven Spielberg', 'original_name' => 'Not Related 3'],
            ['name' => 'Not Related 4', 'original_name' => 'Martin Scorsese'],
        )
        ->create();

    $response = $this->getJson('/api/v1/persons?search=Nolan');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data.items')
        ->assertJsonFragments([
            ['name' => 'Christopher Nolan'],
            ['original_name' => 'Quentin Nolan'],
        ])
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'original_name',
                        'slug',
                        'profile',
                        'birth_date',
                        'death_date',
                        'biography',
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
