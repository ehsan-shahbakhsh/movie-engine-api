<?php

namespace Database\Factories;

use App\Enums\PersonSeriesDepartment;
use App\Models\Person;
use App\Models\PersonSeries;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonSeries>
 */
class PersonSeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $department = fake()->randomElement(PersonSeriesDepartment::cases());

        $jobs = [
            PersonSeriesDepartment::Acting->value => ['Actor', 'Voice Actor', 'Cameo'],
            PersonSeriesDepartment::Directing->value => ['Director', 'Assistant Director'],
            PersonSeriesDepartment::Writing->value => ['Writer', 'Screenplay', 'Story'],
            PersonSeriesDepartment::Production->value => ['Producer', 'Executive Producer', 'Co-Producer'],
            PersonSeriesDepartment::Camera->value => ['Cinematographer', 'Camera Operator'],
            PersonSeriesDepartment::Editing->value => ['Editor', 'Assistant Editor'],
            PersonSeriesDepartment::Sound->value => ['Sound Designer', 'Sound Editor'],
            PersonSeriesDepartment::Art->value => ['Production Designer', 'Art Director'],
            PersonSeriesDepartment::VisualEffects->value => ['Visual Effects Supervisor', 'Visual Effects Artist'],
            PersonSeriesDepartment::Music->value => ['Composer', 'Music Supervisor'],
        ];
        $job = fake()->randomElement($jobs[$department->value]);

        return [
            'person_id' => Person::factory(),
            'series_id' => Series::factory(),

            'department' => $department,
            'job' => $job,

            'character_name' => $department === PersonSeriesDepartment::Acting ? fake()->optional()->name() : null,
        ];
    }
}
