<?php

namespace Database\Factories;

use App\Enums\MoviePersonDepartment;
use App\Models\Movie;
use App\Models\MoviePerson;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoviePerson>
 */
class MoviePersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $department = fake()->randomElement(MoviePersonDepartment::cases());

        $jobs = [
            MoviePersonDepartment::Acting->value => ['Actor', 'Voice Actor', 'Cameo'],
            MoviePersonDepartment::Directing->value => ['Director', 'Assistant Director'],
            MoviePersonDepartment::Writing->value => ['Writer', 'Screenplay', 'Story'],
            MoviePersonDepartment::Production->value => ['Producer', 'Executive Producer', 'Co-Producer'],
            MoviePersonDepartment::Camera->value => ['Cinematographer', 'Camera Operator'],
            MoviePersonDepartment::Editing->value => ['Editor', 'Assistant Editor'],
            MoviePersonDepartment::Sound->value => ['Sound Designer', 'Sound Editor'],
            MoviePersonDepartment::Art->value => ['Production Designer', 'Art Director'],
            MoviePersonDepartment::VisualEffects->value => ['Visual Effects Supervisor', 'Visual Effects Artist'],
            MoviePersonDepartment::Music->value => ['Composer', 'Music Supervisor'],
        ];
        $job = fake()->randomElement($jobs[$department->value]);

        return [
            'movie_id' => Movie::factory(),
            'person_id' => Person::factory(),

            'department' => $department,
            'job' => $job,

            'character_name' => $department === MoviePersonDepartment::Acting ? fake()->optional()->name() : null,
        ];
    }
}
