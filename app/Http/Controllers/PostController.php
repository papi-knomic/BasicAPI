<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Events\PostLiked;
use App\Http\Requests\CreatePostRequest;
use App\Http\Resources\PostResource;
use App\Jobs\IncrementPostViewJob;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;

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
        $sort = request('sort_by', 'latest');
        if ( !in_array( $sort, ['latest', 'popular'] ) ){
            $sort = 'latest';
        }
        $filters = [
            'tag' => request('tag'),
            'search' => request('search'),
            'sort' => $sort
        ];
        $posts = $this->postRepository->getAll( $filters );
        $posts = PostResource::collection($posts)->response()->getData(true);
        return Response::successResponseWithData($posts, 'Posts gotten');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        $fields = $request->validated();
        $fields['user_id'] = auth()->id();
        $post = $this->postRepository->create($fields);
        if ($request->hasFile('files')) {
            $files = $request->file('files');;
            foreach ($files as $file) {
                $attachment = addPostAttachment($file);
               PostAttachment::create( [
                    'post_id' => $post->id,
                    'media_id' => $attachment['public_id'],
                    'url' => $attachment['secure_url'],
                    'media_type' => $file->getMimeType()
                ]);
            }
        }
        event(new PostCreated($post, $post->user));
        $post = new PostResource($post);

        return Response::successResponseWithData($post, 'Post created', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        IncrementPostViewJob::dispatchAfterResponse($post);
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
        if ( !isPostCreator($post) ){
            return Response::errorResponse('You are not authorised to do this');
        }
        $fields = $request->validated();
        $post = $this->postRepository->update( $post->id, $fields );
        $post = new PostResource($post);
        return Response::successResponseWithData($post, 'Post updated', 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {
        if ( !isPostCreator($post) ){
            return Response::errorResponse('You are not authorised to do this');
        }

        $post->delete();

        return Response::successResponse();
    }

    public function getUserPosts(User $user ) : JsonResponse
    {
        $sort = request('sort_by', 'latest');
        if ( !in_array( $sort, ['latest', 'popular'] ) ){
            $sort = 'latest';
        }
        $filters = [
            'tag' => request('tag'),
            'search' => request('search')
        ];
        $posts = $user->posts()->filter($filters, $sort)->paginate(10);
        $posts = PostResource::collection($posts)->response()->getData(true);
        return Response::successResponseWithData($posts, 'Posts gotten');
    }

    /** Like a post.
    *
    * @param  Post  $post
    * @return JsonResponse
    */
    public function like(Post $post): JsonResponse
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);

        if ($user->hasLiked($post)) {
            return Response::errorResponse( 'You have already liked this post', 409 );
        }

        $user->like($post);
        event(new PostLiked($post, $user));

        return Response::successResponse('Post liked successfully' );
    }

    /**
     * Dislike a post.
     *
     * @param  Post  $post
     * @return JsonResponse
     */
    public function dislike(Post $post): JsonResponse
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);

        if ($user->hasDisliked($post)) {
            return Response::errorResponse( 'You have already disliked this post', 409 );
        }

        $user->dislike($post);

        return Response::successResponse('Post disliked successfully', 200);
    }

    /** Get posts for users from other users they follow
     * @return void
     */
    public function following() : JsonResponse
    {
        $user = auth()->user();

        $following = $user->following()->pluck('following_id');
        $sort = request('sort_by', 'latest');

        $filters = [
            'sort' => $sort
        ];

        $posts = Post::whereIn('user_id', $following)
            ->filter($filters, $sort)->paginate(10)
            ->paginate(10);

        $posts = PostResource::collection($posts)->response()->getData(true);
        return Response::successResponseWithData($posts, 'Posts gotten');
    }

    public function recommended()
    {
        //TODO decide what way to recommend posts for user

    }


}
