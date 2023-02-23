<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{

    private $postRepository;

    public function __construct( PostRepository $postRepository )
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $posts = $this->postRepository->getAll();
        $posts = PostResource::collection($posts)->response()->getData(true);
        return Response::successResponseWithData( $posts, 'Posts gotten');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return JsonResponse
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $post = $this->postRepository->create( $fields );
        $post = new PostResource($post);
        return Response::successResponseWithData( $post, 'Post created', 201 );
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        // increment the view count by 1
        $post->increment('views_count');

        $post = new PostResource($post);

        return Response::successResponseWithData($post);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param CreatePostRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(CreatePostRequest $request, Post $post) : JsonResponse
    {
        if ( !checkPostCreator($post) ){
            return Response::errorResponse('You are not authorised to do this');
        }
        $fields = $request->validated();
        $post = $this->postRepository->update( $post->id, $fields );
        $post = new PostResource($post);
        return Response::successResponseWithData( $post, 'Post updated', 201 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {
        if ( !checkPostCreator($post) ){
            return Response::errorResponse('You are not authorised to do this');
        }

        $post->delete();

        return Response::successResponse();
    }

    public function getUserPosts(Request $request ) : JsonResponse
    {
        $username = $request->username;
        $user = User::whereUsername($username)->first();
        if (! $user ) {
            return Response::errorResponse('User not found');
        }
        $posts = $user->posts()->latest('updated_at')->paginate(10);
        $posts = PostResource::collection($posts)->response()->getData(true);
        return Response::successResponseWithData( $posts, 'Posts gotten');
    }
}
