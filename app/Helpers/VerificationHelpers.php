<?php

use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

if (!function_exists('generateVerificationCodeForUser')) {
    /**
     * Generate Verification Code
     * @param string $email
     * @return int
     * @throws Exception
     */
    function generateVerificationCodeForUser( string $email ): int
    {
        VerificationCode::whereVerifiable($email)->delete();
        $code = random_int(1000, 9999);
        $hashedCode = Hash::make($code);
        $data = [
            'code' => $hashedCode,
            'verifiable' => $email,
            'expires_at' => Carbon::now()->addHour()->toDateTimeString(),
        ];

        VerificationCode::create($data);

        return $code;
    }
}

if ( !function_exists('getUserFirstNameFromEmail') ) {
    function getUserFirstNameFromEmail( string $email )
    {
        $user = User::where('email', $email)->first();

        if ( $user ) {
            return $user->first_name;
        }

        return false;
    }
}
