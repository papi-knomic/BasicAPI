<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\PostCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class PostCreatedListener
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
     * @param PostCreated $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        $post = $event->post;
        $user = $event->user;

        $notifyUsers = User::whereIn('id', function ($query) use ($user) {
            $query->select('subscriber_id')
                ->from('user_subscriptions')
                ->where('subscribe_id', $user->id);
        })->get();

        Notification::send($notifyUsers, new PostCreatedNotification( $post, $user));
    }
}
