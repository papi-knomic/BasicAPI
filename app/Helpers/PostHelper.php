<?php

use App\Models\Post;
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

if ( ! function_exists( 'checkPostCreator') ) {
    function checkPostCreator( Post $post ): bool {
        return auth()->id() == $post->user_id;
     }
}
