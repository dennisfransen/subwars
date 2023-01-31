<?php

namespace Database\Factories;

use App\Models\Fight;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class FightFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fight::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|Fight|mixed
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
            "tournament_id" => function () {
                return Tournament::factory()->create()->id;
            },
        ];
    }
}
