<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProfilePictureRequest;
use App\Http\Resources\ProfilePictureResource;
use App\Jobs\ProfilePictureJob;
use App\Models\ProfilePicture;
use App\Traits\Response;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfilePictureController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
//     * @return JsonResponse
     */
    public function store(UploadProfilePictureRequest $request)
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
            return Response::errorResponse();
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
     * @param ProfilePicture $profilePicture
     * @return JsonResponse
     */
    public function show(ProfilePicture $profilePicture)
    {
        $profilePictureResource = new ProfilePictureResource($profilePicture);

        return Response::successResponseWithData($profilePictureResource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProfilePicture $profilePicture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

    }
}
