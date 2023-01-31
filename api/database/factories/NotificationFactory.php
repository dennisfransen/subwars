<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|Notification|mixed
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
            "notifiable_id" => function () {
                return Tournament::factory()->create()->id;
            },
            "notifiable_type" => function () {
                return Tournament::class;
            },
            "user_id" => function () {
                return User::factory()->create()->id;
            },
            "description" => $this->faker->sentence,
        ];
    }
}
