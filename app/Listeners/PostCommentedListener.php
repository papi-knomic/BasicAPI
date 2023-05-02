<?php

namespace App\Listeners;

use App\Events\PostCommented;
use App\Notifications\LikedPostNotification;
use App\Notifications\PostCommentedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class PostCommentedListener
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
     * @param PostCommented $event
     * @return void
     */
    public function handle(PostCommented $event)
    {
        $post = $event->post;

        // Check if the user who liked the post is not the same as the user who created the post
        if ($post->user_id !== auth()->id()) {
            $user = $post->user;
            // Send the notification to the user who owns the post
            Notification::send($user, new PostCommentedNotification($post, $event->user, $event->comment));
        }
    }
}
