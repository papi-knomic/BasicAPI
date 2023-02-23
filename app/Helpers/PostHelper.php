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
