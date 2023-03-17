<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikePostRequest;
use App\Models\Post;
use App\Models\PostLike;
use App\Traits\Response;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function like( LikePostRequest $request ) {
        // Get the authenticated user
        $user = auth()->user();

        // Get the post being liked
        $post = Post::findOrFail( $request->post_id );

        // Check if the user has already liked the post
        if ( $post->likes()->where( 'user_id', $user->id )->exists() ) {
            // If the user has already liked the post, unlike it
            $post->likes()->where( 'user_id', $user->id )->delete();
            $responseMessage = 'Post unliked';
            $code = 200;
        } else {
            // If the user has not liked the post, like it
            $post->likes()->create([
                'user_id' => $user->id,
            ]);
            $responseMessage = 'Post liked';
            $code = 201;
        }

        // Return a response indicating success
        return Response::successResponse($responseMessage, $code);
    }
}
