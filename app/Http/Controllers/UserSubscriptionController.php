<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;

class UserSubscriptionController extends Controller
{
    /**
     * Subscribe to a user post notification.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function subscribe( User $user) : JsonResponse
    {
        $currentUser = auth()->id();

        if ( $user->id === $currentUser ) {
            return Response::errorResponse('Something bad happened', 400);
        }

        if ( !$user->isFollowedByUser( $currentUser ) ){
            $user->followers()->attach($currentUser);
        }

        if ( $user->isSubscribedByUser($currentUser) ) {
            return Response::errorResponse('You are already subscribed to user', 400);
        }

        $user->postNotificationUsers()->attach($currentUser);

        return Response::successResponse('You have successfully turned on post notification for this user', 201);
    }

    /**
     * Unsubscribe to a user post notification.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function unsubscribe( User $user) : JsonResponse
    {
        $currentUser = auth()->id();

        if ( $user->id === $currentUser ) {
            return Response::errorResponse('Something bad happened', 400);
        }

        if ( ! $user->isSubscribedByUser($currentUser) ) {
            return Response::errorResponse('You are not subscribed to this user', 400);
        }

        $user->postNotificationUsers()->detach($currentUser);

        return Response::successResponse('You have successfully turned off post notification for this user', 201);
    }

}
