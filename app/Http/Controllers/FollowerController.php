<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;


class FollowerController extends Controller
{


    /**
     * Get specific user followers.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function followers(User $user) : JsonResponse
    {
        $followers = $user->followers;
        $followers = ProfileResource::collection($followers)->response()->getData(true);

        return Response::successResponseWithData( $followers );
    }

    /**
     * Get specific user following
     *
     * @param User $user
     * @return JsonResponse
     */
    public function following(User $user) : JsonResponse
    {
        $following = $user->following;
        $following = ProfileResource::collection($following)->response()->getData(true);

        return Response::successResponseWithData( $following );
    }

    /**
     * Follow specific user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function follow(User $user) : JsonResponse
    {
        $currentUser = auth()->id();
        if ( $user->id === $currentUser ) {
            return Response::errorResponse('You can not follow yourself', 400);
        }

        if ( $user->isFollowedByUser( $currentUser ) ) {
            return Response::errorResponse('You already follow this user', 400);
        }

        $user->followers()->attach($currentUser);
        return Response::successResponse('You have successfully followed the user', 201);
    }

    /**
     * Unfollow specific user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function unfollow(User $user) : JsonResponse
    {
        $currentUser = auth()->id();
        if ( $user->id === $currentUser ) {
            return Response::errorResponse('You can not unfollow yourself', 400);
        }

        if ( ! $user->isFollowedByUser( $currentUser ) ) {
            return Response::errorResponse('You do not follow this user', 400);
        }

        $user->followers()->detach($currentUser);
        return Response::successResponse('You have successfully followed the user', 201);
    }

    /**
     * Get authenticated user followers.
     * @return JsonResponse
     */
    public function getFollowers() : JsonResponse
    {
        $user = auth()->id();
        $user = User::find( $user );
        $followers = $user->followers;
        $followers = ProfileResource::collection($followers)->response()->getData(true);

        return Response::successResponseWithData( $followers );
    }

    /**
     * Get authenticated user following.
     * @return JsonResponse
     */
    public function getFollowing() : JsonResponse
    {
        $user = auth()->user();
        $following = $user->following;

        $following = ProfileResource::collection($following)->response()->getData(true);

        return Response::successResponseWithData( $following );
    }
}
