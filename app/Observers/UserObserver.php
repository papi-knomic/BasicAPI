<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\WelcomeMailNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function created(User $user)
    {
        $this->generateUserVerificationCode($user->email);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        $emailWasVerified = $user->wasChanged('email_verified_at');

        if ($emailWasVerified) {
            $firstname = $user->firstname;

            $data = [
                'subject' => "Welcome to OpenSocial, {$firstname}",
                'firstname' => $firstname
            ];

            Notification::route('mail', $user->email)->notify((new WelcomeMailNotification($data)));
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }

    /**
     * @throws Exception
     */
    private function generateUserVerificationCode(string $email ) {
        $code = generateVerificationCodeForUser($email);
        $firstname = getUserFirstNameFromEmail($email);

        $details = [
            'subject' => 'Verify Email Address',
            'message' => 'Your verification code: :code',
            'code' => $code,
            'firstname' => $firstname
        ];

        Notification::route('mail', $email)
            ->notify(new EmailVerificationNotification($details));

    }
}
