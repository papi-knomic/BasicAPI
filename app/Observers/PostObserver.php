<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function creating( Post $post ) {
        $post->slug = generatePostSlug($post->title);
        $post->user_id = auth()->id();
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

    public function retrieved(Post $post)
    {
        // Increase the view_count by 1
        $post->increment('view_count');
    }
}
