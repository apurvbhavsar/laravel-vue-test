<?php

namespace App\Listeners;

use App\Events\UserRegisterd;
use App\Mail\UserRegistered;
use Illuminate\Support\Facades\Mail;

class UserRegisterdListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserRegisterd $event)
    {
        //Send Mail to User
        Mail::to($event->user->email)->send(new UserRegistered($event->user));
    }
}
