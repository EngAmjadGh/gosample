<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use Log;

class FirebaseNotification extends Notification
{

    protected $title;
    protected $message;
    protected $type;

    // public function __construct($title, $message)
    // {
    //     $this->title = $title;
    //     $this->message = $message;
    // }
    public function __construct($title, $message,$type = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        Log::info('toFcm');
        return FcmMessage::create()
            ->setData([
                'sound' => 'default',
                'title' => $this->title,
                'message' => $this->message,
                'body' => $this->message,
                'type' => $this->type
            ])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->title)
                ->setBody($this->message)
                // ->setSound('default')

                // ->setImage('http://example.com/url-to-image-here.png')
                )
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )
            ->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
                    ->setPayload([
                        'aps' => [
                            'sound' => 'hang.caf',
                            'title' => $this->title,
                            'message' => $this->message,
                            'body' => $this->message,
                        ],
                        'type' => $this->type,
                        'title' => $this->title,
                        'message' => $this->message,
                        'body' => $this->message,
                        ])
                )
                ;
    }
}
