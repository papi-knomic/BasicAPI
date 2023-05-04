<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Post $post, User $user)
    {
        $this->user = $user;
        $this->post = $post;
    }
}
