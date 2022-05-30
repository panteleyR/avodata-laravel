<?php

namespace App\Listeners;

use App\Events\ConfirmCodeSet;
use App\Events\VerifyUserDone;
use App\Mail\UserVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class CleanTemporaryUser implements ShouldQueue
{
    public string $queue = 'cleaner';

    public int $delay = 3;

    public function __construct()
    {
        //
    }

    public function handle(VerifyUserDone $event)
    {
        $event->tmpUser->delete();
    }
}
