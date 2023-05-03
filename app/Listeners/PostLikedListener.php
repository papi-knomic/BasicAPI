<?php

namespace App\Listeners;

use App\Events\PostLiked;
use App\Notifications\LikedPostNotification;
use App\Notifications\PostLikedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PostLikedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param PostLiked $event
     * @return void
     */
    public function handle(PostLiked $event)
    {
        $post = $event->post;

        // Check if the user who liked the post is not the same as the user who created the post
        if ($post->user_id !== auth()->id()) {
            // Send the notification to the user who owns the post
            $post->user->notify(new LikedPostNotification($post, $event->user));
        }
    }
}
