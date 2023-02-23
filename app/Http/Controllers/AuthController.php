<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ProfileViewResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function register( RegisterUserRequest $request ) : JsonResponse
    {
        $fields = $request->validated();

        $user = $this->userRepository->create($fields);

        return Response::successResponseWithData($user, 'Successful!, check your mail for verification code');
    }

    public function login( LoginUserRequest $request ) : JsonResponse
    {
        $userData = $request->validated();

        if (Auth::attempt($userData)) {
            $token = config('keys.token');
            $accessToken = Auth::user()->createToken($token)->plainTextToken;
            $data = auth()->user();
            return Response::successResponseWithData($data, 'Login successful', 200, $accessToken);
        }
        return Response::errorResponse('Invalid Login credentials', 400);
    }

    public function profile( Request $request ): JsonResponse
    {
        $token = config('keys.token');
        $accessToken = Auth::user()->createToken($token)->plainTextToken;
        $data = auth()->user();
        $user =  User::whereId($data->id)
            ->with('profilePicture')
            ->first();
        $profileResource = new ProfileResource($user);
        return Response::successResponseWithData($profileResource, 'Profile data gotten', 200, $accessToken);
    }

    public function viewProfile( Request $request ): JsonResponse
    {
        $username = $request->username;
        $user = User::whereUsername($username)
            ->with('profilePicture')
            ->first();
        if ( $user ) {
            $profileResource = new ProfileViewResource($user);
            return Response::successResponseWithData($profileResource,'Success');
        }

        return Response::errorResponse('User not found', 404);
    }

    public function update( UpdateProfileRequest $request ) : JsonResponse
    {
        $fields = $request->validated();

        $update = $this->userRepository->update($fields);

        $user = User::whereId($update)->first();
        $profileResource = new ProfileResource($user);

        return Response::successResponseWithData( $profileResource,'Profile updated', 201);
    }

}
