<?php

namespace Database\Factories;

use App\Http\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory"s corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|User|mixed
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        return parent::create($attributes, $parent);
    }

    /**
     * Define the model"s default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "type" => UserType::MEMBER,
            "username" => $this->faker->userName(),
            "password" => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", // password
            "esportal_elo" => $this->faker->numberBetween(1000, 3000),
            "remember_token" => Str::random(10),
            "streamer" => false,
        ];
    }
}
