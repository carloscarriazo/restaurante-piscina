<?php

namespace App\Listeners;

use App\Models\SessionLog;
use Illuminate\Auth\Events\Logout;

class LogLogout
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
    public function handle(Logout $event): void
    {
        if ($event->user) {
            SessionLog::logAction(
                $event->user->id,
                'logout',
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
}
