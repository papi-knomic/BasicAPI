<?php

namespace Database\Factories;

use App\Models\ProfilePicture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilePictureFactory extends Factory
{
    protected $model = ProfilePicture::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $uuid = $this->faker->uuid();
        $user_id = User::inRandomOrder()->first()->id;
        return [
            'image_id' => $uuid,
            'url' => 'https://res.cloudinary.com/example/image/upload/v123456/test.jpg',
            'user_id' => $user_id
        ];
    }
}
