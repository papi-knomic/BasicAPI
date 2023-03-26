<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{

    protected $model = Comment::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_id = User::inRandomOrder()->first()->id;
        $post_id = Post::inRandomOrder()->first()->id;
        return [
            'body' => $this->faker->sentence(),
            'user_id' => $user_id,
            'post_id' => $post_id
        ];
    }
}
