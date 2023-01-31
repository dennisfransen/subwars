<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class TeamUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamUser::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|TeamUser|mixed
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
    public function definition()
    {
        return [
            "team_id" => function () {
                return Team::factory()->create()->id;
            },
            "user_id" => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
