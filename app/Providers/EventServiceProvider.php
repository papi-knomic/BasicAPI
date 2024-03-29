<?php

namespace App\Providers;

use App\Events\PostCommented;
use App\Events\PostCreated;
use App\Events\PostLiked;
use App\Listeners\PostCommentedListener;
use App\Listeners\PostCreatedListener;
use App\Listeners\PostLikedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PostLiked::class => [
            PostLikedListener::class,
        ],
        PostCommented::class => [
            PostCommentedListener::class
        ],
        PostCreated::class => [
            PostCreatedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
