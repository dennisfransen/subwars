<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|Client|mixed
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
            "name" => $this->faker->name,
            "secret" => "asdflhkdslaksjghalkdsjhfalksjdflaks",
            "provider" => "users",
            "redirect" => config("app.url"),
            "personal_access_client" => false,
            "password_client" => true,
            "revoked" => false,
        ];
    }
}
