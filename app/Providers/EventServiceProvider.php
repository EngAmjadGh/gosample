<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\CurrentDriverLocationEvent;
use App\Events\DriverArrivedAtDeliveredLocationEvent;
use App\Events\DriverArrivedAtPickUpLocationEvent;
use App\Events\TaskCancelledEvent;
use App\Events\SamplesCollectedEvent;
use App\Events\TaskClosedEvent;
use App\Listeners\SendCurrentDriverLocationEvent;
use App\Listeners\SendDriverArrivedAtDeliveredLocationEvent;
use App\Listeners\SendDriverArrivedAtPickUpLocationEvent;
use App\Listeners\SendTaskCancelledEvent;
use App\Listeners\SendSamplesCollectedEvent;
use App\Listeners\SendTaskClosedEvent;
use App\Observers\TaskObserver;
use App\Models\Task;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        DriverArrivedAtPickUpLocationEvent::class => [
            SendDriverArrivedAtPickUpLocationEvent::class,
        ],
        SamplesCollectedEvent::class => [
            SendSamplesCollectedEvent::class,
        ],
        TaskCancelledEvent::class => [
            SendTaskCancelledEvent::class,
        ],
        TaskClosedEvent::class => [
            SendTaskClosedEvent::class,
        ],
        DriverArrivedAtDeliveredLocationEvent::class => [
            SendDriverArrivedAtDeliveredLocationEvent::class,
        ],
        CurrentDriverLocationEvent::class => [
            SendCurrentDriverLocationEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        Task::observe(TaskObserver::class);
        
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
