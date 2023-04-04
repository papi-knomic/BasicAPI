<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestResetPasswordRequest;
use App\Http\Requests\ResendVerificationCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Jobs\ResendVerificationCodeJob;
use App\Jobs\ResetPasswordJob;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class VerificationCodeController extends Controller
{

    public function verifyEmail( VerifyEmailRequest $request ) {
        $data = $request->validated();

        $user = User::whereEmail($data['email'])->first();
        $code = VerificationCode::where('verifiable', $data['email'])->first();
        $invalidResponse = Response::errorResponse('Invalid code!');

        if (!$user) {
            return Response::errorResponse('Invalid details');
        }

        auth()->loginUsingId($user->id);

        if ($request->user()->hasVerifiedEmail()) {
            return Response::successResponse('Already verified');
        }

        if (!$code) {
            return $invalidResponse;
        }

        if ($code->expires_at < now()) {
            return $invalidResponse;
        }

        $verify = $this::verify( $data['code'], $data['email'] );

        if (!$verify ) {
            return $invalidResponse;
        }

        $request->user()->markEmailAsVerified();

        return Response::successResponseWithData($user,'Verification successful');
    }

    /**
     * @throws \Exception
     */
    public function resendVerificationCode(ResendVerificationCodeRequest $request ): JsonResponse
    {
        $data = $request->validated();
        $user = User::whereEmail($data['email'])->first();

        if (!$user) {
            return Response::errorResponse('Invalid details');
        }

        auth()->loginUsingId($user->id);

        if ($request->user()->hasVerifiedEmail()) {
            return Response::successResponse('Already verified');
        }

        $code = generateVerificationCodeForUser($user->email);
        $firstname = getUserFirstNameFromEmail($user->email);
        $details = [
            'code' => $code,
            'firstname' => $firstname,
            'subject' => "Verify Email",
            'email' => $user->email
        ];

        ResendVerificationCodeJob::dispatchAfterResponse( $details );

        return Response::successResponse('Verification code resent');

    }

    /**
     * @throws \Exception
     */
    public function requestPasswordResetCode(RequestResetPasswordRequest $request ) : JsonResponse
    {
        $data = $request->validated();
        $user = User::whereEmail($data['email'])->first();
        $code = generateVerificationCodeForUser($user->email);

        if (!$user) {
            return Response::errorResponse('Invalid details');
        }
        $details = [
            'code' => $code,
            'subject' => 'Reset Password',
            'body' => 'This is the code to reset your password',
            'email' => $user->email
        ];

        ResetPasswordJob::dispatchAfterResponse($details);

        return Response::successResponse('Reset code has been sent to email');
    }

    public function resetPassword(ResetPasswordRequest $request ) : JsonResponse
    {
        $data = $request->validated();
        $user = User::whereEmail($data['email'])->first();

        if (!$user) {
            return Response::errorResponse('Invalid details');
        }

        $verify = $this::verify( $data['code'], $data['email'] );

        if ( ! $verify ){
            return Response::errorResponse('Invalid details');
        }
        $details['password'] = bcrypt($data['password']);

        User::whereId($user->id)->update($details);

        return Response::successResponse('Password successfully reset');
    }

    public static function verify($code, $email): bool
    {
        $getCode = VerificationCode::where('verifiable', $email)->first();
        if ($getCode) {
            $existingCode = $getCode->code;
            $correctCode = Hash::check($code, $existingCode);
            if ($correctCode) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
