<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilePictureController;
use App\Traits\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['json', 'throttle:60,1']], function () {
    Route::get('/', function () {
        return Response::successResponse('Welcome to Basic API');
    });

    //register
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    //login
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    //view profile
    Route::get('/profile/{user}', [AuthController::class, 'viewProfile']);
    //get user posts by username
    Route::get('/{user}/posts' , [PostController::class, 'getUserPosts']);

    //get all posts
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    //get single post
    Route::get('/post/{post}', [PostController::class, 'show'])->name('posts.show');


    //protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('account')->group(function () {
            //get user followers
            Route::get('/followers', [FollowerController::class, 'getFollowers'])->name('profile.followers');
            //get user followings
            Route::get('/following', [FollowerController::class, 'getFollowing'])->name('profile.following');
            //view your profile
            Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
            //update
            Route::post('/profile', [AuthController::class, 'update'])->name('profile.update');
            //get profile picture
            Route::get('/profile-picture', [ ProfilePictureController::class, 'show']);
            //post profile picture
            Route::post('/profile-picture', [ ProfilePictureController::class, 'store']);
            //logout
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });

        Route::prefix('user')->group(function () {
            //get user followers
            Route::get('/{user}/followers', [FollowerController::class, 'followers'])->name('user.followers');
            //get user followings
            Route::get('/{user}/following', [FollowerController::class, 'following'])->name('user.following');
           //follow user
            Route::post('/{user}/follow', [FollowerController::class, 'follow'])->name('user.follow');
            //follow user
            Route::post('/{user}/unfollow', [FollowerController::class, 'unfollow'])->name('user.unfollow');
        });

        Route::prefix('post')->group(function () {
            //create post
            Route::post('/', [PostController::class, 'store'])->name('post.store');
            //update
            Route::post('/{post}', [PostController::class, 'update'])->name('post.update');
            //like post
            Route::post('{post}/like', [PostController::class, 'like'])->name('post.like');
            //dislike
            Route::post('{post}/dislike', [PostController::class, 'dislike'])->name('post.dislike');
            //delete post
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('post.destroy');
            //Get comments
            Route::get('/{post}/comments', [ CommentController::class, 'index' ])->name('comment.index');
            //Add comment
            Route::post('/{post}/comment', [ CommentController::class, 'store' ])->name('comment.store');
        });

        Route::prefix('comment')->group(function () {
            //get comment
            Route::get('/{comment}', [CommentController::class, 'show'])->name('comment.show');
            //update comment
            Route::patch('/{comment}', [ CommentController::class, 'update' ])->name('comment.update');
            //delete comment
            Route::delete('/{comment}', [ CommentController::class, 'destroy' ])->name('comment.destroy');
        });
    });
});



