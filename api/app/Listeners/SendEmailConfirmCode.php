<?php

namespace App\Listeners;

use App\Events\ConfirmCodeSet;
use App\Mail\UserVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailConfirmCode implements ShouldQueue
{
    public string $queue = 'emails';

    public int $delay = 3;

    public function __construct()
    {
        //
    }

    public function handle(ConfirmCodeSet $event)
    {
        Mail::to($event->email)->send(new UserVerification($event->code));
    }
}
