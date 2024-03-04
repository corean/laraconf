<?php

namespace Database\Factories;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date'  => $this->faker->dateTime(),
            'end_date'    => $this->faker->dateTime(),
            'status'      => $this->faker->word(),
            'region'      => $this->faker->word(),
            'is_published'=> $this->faker->boolean(),
            'venue_id'    => null,
        ];
    }
}
