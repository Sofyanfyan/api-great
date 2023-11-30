<?php

namespace App\Jobs;

use App\Mail\SendEmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $send_mail, $user, $otp;
    public $tries = 5;

    public function __construct($send_mail, $user, $otp)
    {
        $this->send_mail = $send_mail;
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailData = [
            'user' => $this->user->name,
            'otp' => $this->otp->otp,
        ];
        $email = new SendEmailVerification($mailData);
        Mail::to($this->send_mail)->send($email);
    }
}
