<?php

namespace Database\Factories;

use App\Trip;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Trip::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'miles' => $this->faker->numberBetween(100, 1000),
            'car_id' => function () {
                return Car::factory()->create()->id;
            },
        ];
    }
}
