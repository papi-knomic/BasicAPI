<?php

namespace Tests\Feature;

use App\Jobs\ProfilePictureJob;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProfilePictureTest extends TestCase
{
    protected $endpoint = 'api/account/profile-picture';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_endpoint()
    {
        $this->be( $this->user );
        $response = $this->post($this->endpoint);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_image_is_missing()
    {
        $this->be( $this->user );
        $response = $this->post($this->endpoint);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('image');
    }

    public function test_image_file_not_passed()
    {
        $file = UploadedFile::fake()->create('fake.txt', 2000, 'text/plain');

        $this->be( $this->user );
        $response = $this->post($this->endpoint, [
            'image' => $file
        ]);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('image');
    }

    public function test_large_image_file_passed()
    {
        $file = UploadedFile::fake()->image('test.jpg')->size(2049);

        $response = $this->actingAs($this->user)->post($this->endpoint, [
            'image' => $file,
        ]);

        $response->assertStatus(self::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('image');
    }

    public function test_cloudinary_upload()
    {
        Queue::fake();
        // Create a fake image file
        $file = UploadedFile::fake()->image('test.jpg');

        $this->be( $this->user );
        $uuid = Factory::create()->uuid();
        // Set up the mock response
        Http::fake([
            'https://api.cloudinary.com/**' => Http::response([
                'public_id' => $uuid,
                'secure_url' => 'https://res.cloudinary.com/example/image/upload/v123456/test.jpg',
            ], 200),
        ]);

        // Make the request to the endpoint
        $response = $this->post($this->endpoint, [
            'image' => $file,
        ]);

        // Assert that the response is successful and has the expected data
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'public_id' => $uuid,
                'url' => 'https://res.cloudinary.com/example/image/upload/v123456/test.jpg',
            ],
            'message' => 'Profile Picture updated successfully',
        ]);
    }

    public function test_profile_picture_upload_fails_when_cloudinary_api_returns_failed_response()
    {
        $this->be( $this->user );
        // Arrange
        $fakeFile = UploadedFile::fake()->image('profile.jpg');
        Http::fake([
            'https://api.cloudinary.com/v1_1/*/image/upload' => Http::response([], 500),
        ]);

        // Act
        $response = $this->post($this->endpoint, [
            'image' => $fakeFile,
        ]);

        // Assert
        $response->assertStatus(self::HTTP_BAD_REQUEST );
        $response->assertJson([
            'success' => false,
            'message' => 'Something bad happened'
        ]);
    }

}
