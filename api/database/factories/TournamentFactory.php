<?php

namespace Database\Factories;

use App\Models\Bracket;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class TournamentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tournament::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|Tournament|mixed
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        return parent::create($attributes, $parent);
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->name(),
            "description" => $this->faker->sentence(),
            "rules" => $this->faker->sentence(),
            "max_teams" => -1,
            "min_elo" => -1,
            "max_elo" => -1,
            "bracket_id" => function () {
                return Bracket::factory()->create()->id;
            },
            "user_id" => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
