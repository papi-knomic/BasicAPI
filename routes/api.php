<?php

use App\Http\Controllers\AuthController;
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


    //protected routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('account')->group(function () {
            //view your profile
            Route::get('/profile', [AuthController::class, 'profile']);
            //update
            Route::post('/profile', [AuthController::class, 'update']);
        });
    });
});


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
