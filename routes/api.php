<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
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

        Route::prefix('post')->group( function () {
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
            //Add comment
            Route::post('/{post}/comment', [ CommentController::class, 'store' ])->name('comment.store');
        });

        Route::prefix('comment')->group( function () {
            //get comment
//            Route::get('/{comment}', [CommentController::class, 'show'])->name('comment.show');
            //update comment
            Route::patch('/{comment}', [ CommentController::class, 'update' ])->name('comment.update');
            //delete comment
            Route::delete('/{comment}', [ CommentController::class, 'destroy' ])->name('comment.destroy');
        });
    });
});



