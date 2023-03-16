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
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraphs(3, true),
            'slug' => $this->faker->unique()->slug,
            'tags' => $this->faker->words(3, true),
            'excerpt' => $this->faker->sentence,
            'views_count' => 0,
            'user_id' => User::factory()->create()->id
        ];
    }
}
