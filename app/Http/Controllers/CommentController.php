<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index( Post $post ): JsonResponse
    {
        $comments = $post->comments;
        $comments = CommentResource::collection($comments);

        return Response::successResponseWithData( $comments );
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

        $parentId = $request->parent_id;
        if ( $parentId ) {
            $parentComment =  Comment::whereId($parentId)->whereNull('parent_id')->first();
            if (!$parentComment) {
                return Response::errorResponse('You can not add comment to this thread', 400 );
            }

            $commentData['post_id'] = $parentComment->post_id;
        }
        Comment::create($commentData);

        $postResource = new PostResource($post);

        return Response::successResponseWithData( $postResource, 'Comment added sucessfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment = new CommentResource($comment);
        return Response::successResponseWithData($comment);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCommentRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment) : JsonResponse
    {
        if ( !isCommentCreator($comment) ){
            return Response::errorResponse('You are not authorised to do this');
        }
        $fields = $request->validated();
        $comment->update($fields);
        $postResource = new PostResource($comment->post);

        return Response::successResponseWithData($postResource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        if ( !isCommentCreator($comment) ){
            return Response::errorResponse('You are not authorised to do this');
        }

        $comment->delete();

        return Response::successResponse('Comment deleted successfully', );
    }
}
