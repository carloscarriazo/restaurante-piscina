<?php

namespace App\Listeners;

use App\Models\SessionLog;
use Illuminate\Auth\Events\Login;

class LogLogin
{
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
    public function handle(Login $event): void
    {
        SessionLog::logAction(
            $event->user->id,
            'login',
            true,
            'auth',
            null,
            [
                'user_name' => $event->user->name,
                'user_email' => $event->user->email,
                'guard' => $event->guard
            ]
        );
    }
}
