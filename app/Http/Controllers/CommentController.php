<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Post $post
     * @param AddCommentRequest $request
     * @return JsonResponse
     */
    public function store(Post $post, AddCommentRequest $request): JsonResponse
    {
        $commentData = $request->validated();
        $commentData['post_id'] = $post->id;
        $commentData['user_id'] = auth()->id();

        if ( array_key_exists('parent_id', $commentData) ) {
            $parentComment = Comment::find($commentData['parent_id']);
            if ( $parentComment->parent_id  ) {
                return Response::errorResponse('You can not add comment to this thread', 400 );
            }

            if ( $parentComment->post_id !== $post->id ) {
                return Response::errorResponse('Wrong post passed', 400 );
            }
        }
        Comment::create($commentData);

        $postResource = new PostResource($post);

        return Response::successResponseWithData( $postResource);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
