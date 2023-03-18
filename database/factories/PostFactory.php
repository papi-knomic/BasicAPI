<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{

    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_id = User::inRandomOrder()->first()->id;

        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraphs(3, true),
            'tags' => $this->faker->words(3, true),
            'user_id' => $user_id
        ];
    }
}
