<?php

namespace Database\Factories;

use App\Models\LinkedSocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class LinkedSocialAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LinkedSocialAccount::class;

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection|LinkedSocialAccount|mixed
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
            "provider_id" => "12345678901234567",
            "user_id" => function () {
                return User::factory()->create()->id;
            },
            "avatar" => "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
        ];
    }
}
