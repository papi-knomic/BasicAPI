<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function creating( Post $post ) {
        $post->slug = generatePostSlug($post->title);
        $post->excerpt = generatePostExcerpt($post->body);
    }

    /**
     * Handle the Job "updating" event.
     *
     * @param Post $post
     * @return void
     */
    public function updating(Post $post)
    {
        $post->slug = generatePostSlug($post->title);
        $post->excerpt = generatePostExcerpt($post->body);
    }
}
