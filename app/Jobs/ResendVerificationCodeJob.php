<?php

namespace App\Jobs;

use App\Notifications\ResendVerificationCodeMailNotification;
use App\Notifications\WelcomeMailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ResendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( array $details )
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
        $this->sendMail();
    }

    private function sendMail()
    {
        Notification::route('mail', $this->details['email'])->notify((new ResendVerificationCodeMailNotification($this->details)));
    }
}
