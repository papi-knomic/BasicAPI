<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProfilePictureRequest;
use App\Jobs\ProfilePictureJob;
use App\Models\ProfilePicture;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfilePictureController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param UploadProfilePictureRequest $request
     * @return JsonResponse
     */
    public function store(UploadProfilePictureRequest $request): JsonResponse
    {
        $request->validated();
        $file = $request->file('image');
        $cloudName = config('cloudinary.cloud_name');

        $url = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";
        $filePath = $file->getRealPath();
        $base64 = base64_encode( file_get_contents( $filePath ) );

        $response = Http::withoutVerifying()->post( $url, [
            'api_key' => config('cloudinary.api_key'),
            'file' => "data:{$file->getClientMimeType()};base64,{$base64}",
            'multiple' => true,
            'upload_preset' => config('cloudinary.upload_preset')
        ]);
        if ( $response->failed() ){
            return Response::errorResponse('Something bad happened', 400 );
        }

        $body = json_decode($response->body(), true);

        ProfilePictureJob::dispatchAfterResponse( $body );

        $data = [
            'public_id' => $body['public_id'],
            'url' => $body['secure_url']
        ];

        return Response::successResponseWithData($data,'Profile Picture updated successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ( ! $user->profilePicture ) {
            return Response::errorResponse('No profile picture found', 404);
        }
        $profilePicture = $user->profilePicture;
        $url = $profilePicture->url;

        return Response::successResponseWithData($url);
    }
}
