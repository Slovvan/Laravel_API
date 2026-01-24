<?php

namespace App\Listeners;

use App\Events\NotificationReceived;
use Illuminate\Notifications\Events\DatabaseNotificationReceived;

class BroadcastNotificationReceived
{
    public function handle(DatabaseNotificationReceived $event)
    {
        // Only broadcast if we have a valid broadcaster configured
        // For now, skip broadcasting since we don't have Pusher/Reverb set up
        // Uncomment below when ready to enable real-time notifications
        
        // event(new NotificationReceived($event->notification, $event->notifiable));
    }
}
