<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    public function creating( Post $post ) {
        $post->slug = generatePostSlug($post->title);
    }

    /**
     * Handle the Job "updating" event.
     *
     * @param Post $post
     * @return void
     */
    public function updating(Post $post)
    {
        $post->slug = generatePostSlug($post->title);;
    }
}
