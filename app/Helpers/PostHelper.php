<?php

use App\Models\Post;
use App\Traits\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

if ( ! function_exists('generatePostSlug') ) {
    function generatePostSlug( string $title ) : string {
        $slug = Str::slug($title);
        $count = Post::where('slug', 'LIKE', "%$slug%")->count();

        if ( $count ){
            $count += 1;
            return "$slug-{$count}";
        }
        return $slug;
    }
}

if ( ! function_exists('generatePostExcerpt') ) {
    function generatePostExcerpt( string $body ) : string {

        // Generate an excerpt
        return Str::limit($body);
    }
}

if (!function_exists( 'isPostCreator')) {
    function isPostCreator( Post $post ): bool {
        return auth()->id() == $post->user_id;
     }
}

if (!function_exists('addPostAttachment')) {
    /**
     * @throws Exception
     */
    function addPostAttachment($file) {
        $filePath = $file->getRealPath();
        $base64 = base64_encode( file_get_contents( $filePath ) );
        $uuid = Str::uuid();
        $cloudName = config('cloudinary.cloud_name');

        $url = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";

        $response = Http::withoutVerifying()->post( $url, [
            'api_key' => config('cloudinary.api_key'),
            'file' => "data:{$file->getClientMimeType()};base64,{$base64}",
            'multiple' => true,
            'upload_preset' => config('cloudinary.upload_preset'),
            'public_id' => 'posts' . '/' . $uuid
        ]);

        if ($response->successful()) {
            // Attachment uploaded successfully
            return json_decode($response->body(), true);
        } else {
            // Attachment upload failed
            // You can handle the error condition here
            $errorMessage = $response->json('message');
            // You can log the error or throw an exception
            // For example, you can throw an exception with the error message
            throw new \Exception("Attachment upload failed: $errorMessage");
        }

    }
}
