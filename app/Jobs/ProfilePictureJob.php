<?php

namespace App\Jobs;

use App\Models\ProfilePicture;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProfilePictureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $details;

    private $id = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( array $details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createProfilePicture();
    }

    private function createProfilePicture() {
        $userID = auth()->id();
        $profileCheck = ProfilePicture::where('user_id', $userID)->first();
        $args = [
            'user_id' => auth()->id(),
            'image_id' => $this->details['public_id'],
            'url' => $this->details['secure_url']
        ];

        if ( $profileCheck ) {
            deleteCloudinaryImage( $profileCheck->image_id );
            $profile = ProfilePicture::whereId( $profileCheck->id )
                ->update($args);
            $this->id = $profileCheck->id;
        }
        else{
            $profile = ProfilePicture::create( $args );
            $this->id = $profile->id;
        }

        if ( $profile ) {
            $this->updateProfile();
        }
    }


    private function updateProfile() {
        User::whereId( auth()->id())
            ->update([
                'profile_picture' => $this->id
            ]);
    }
}
