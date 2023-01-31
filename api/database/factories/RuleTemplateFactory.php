<?php

namespace Database\Factories;

use App\Models\RuleTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class RuleTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RuleTemplate::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|RuleTemplate|mixed
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
            "rules" => $this->faker->sentence(),
        ];
    }
}
