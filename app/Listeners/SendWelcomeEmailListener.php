<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;
    

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // send welcome email to the user
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
