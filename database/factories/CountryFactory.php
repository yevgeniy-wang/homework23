<?php

namespace Database\Factories;

use App\Models\Continent;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $continents = ['Af', 'Na', 'OC', 'AN', 'AS', 'EU', 'SA'];

        return [
            'continent_id' => Continent::factory(),
            'code'         => $this->faker->unique()->countryCode,
            'country_name'         => $this->faker->unique()->country,
        ];
    }
}
