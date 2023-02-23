<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilePictureController;
use App\Traits\Response;
use Illuminate\Http\Request;
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
Route::group(['middleware' => ['json']], function () {
    Route::get('/', function () {
        return Response::successResponse('Welcome to Basic API');
    });

    //register
    Route::post('/register', [AuthController::class, 'register']);
    //login
    Route::post('/login', [AuthController::class, 'login']);

    //view profile
    Route::get('/profile/{username}', [AuthController::class, 'viewProfile']);

    //get all posts
    Route::get('/posts', [PostController::class, 'index']);
    //get single post
    Route::get('/post/{post}', [PostController::class, 'show']);


    //protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('account')->group(function () {
            //view your profile
            Route::get('/profile', [AuthController::class, 'profile']);
            //update
            Route::post('/profile', [AuthController::class, 'update']);
            //get profile picture
            Route::get('/profile-picture', [ ProfilePictureController::class, 'show']);
            //post profile picture
            Route::post('/profile-picture', [ ProfilePictureController::class, 'store']);
        });

        Route::prefix('post')->group( function () {
            //create post
            Route::post('/', [PostController::class, 'store']);
            //delete post
            Route::delete('/{post}', [PostController::class, 'destroy']);
            //update
            Route::put('/{post}', [PostController::class, 'update']);
        });
    });
});


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
