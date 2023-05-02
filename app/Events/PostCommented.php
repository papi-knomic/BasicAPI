<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCommented implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $user;
    public $comment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Post $post, User $user, Comment $comment)
    {
        $this->post = $post;
        $this->user = $user;
        $this->comment = $comment;
    }
}
