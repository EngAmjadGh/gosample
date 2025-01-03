<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CustomDbChannel
{

    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,


            'driver_id' => $data['task']['driver_id'] ,
            'task_id' => $data['task']['id'],
            'billing_client' => $data['task']['billing_client'],
            'from_location' => $data['task']['from_location'],
            'to_location' => $data['task']['to_location'],

            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
        ]);
    }

}
