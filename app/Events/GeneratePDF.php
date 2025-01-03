<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GeneratePDF
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $tasks;
    public $summary;
    public $pick_sum_data;
    public $client_logo;
    public $visited_orginization;
    public $served_orginization;
    public $frozenBags;
    public $roomBags;
    public $refBags;
    public $roomSamples;
    public $refSamples;
    public $frozenSamples;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        $tasks,
        $summary,
        $pick_sum_data,
        $client_logo,
        $visited_orginization,
        $served_orginization,
        $frozenBags,
        $roomBags,
        $refBags,
        $roomSamples,
        $refSamples,
        $frozenSamples
    )
    {
       $this->tasks =  $tasks;
       $this->summary =  $summary;
       $this->pick_sum_data = $pick_sum_data;
       $this->client_logo = $client_logo;
       $this->visited_orginization = $visited_orginization;
       $this->served_orginization = $served_orginization;
       $this->frozenBags =  $frozenBags;
       $this->roomBags = $roomBags;
       $this->refBags = $refBags;
       $this->roomSamples = $roomSamples;
       $this->refSamples = $refSamples;
       $this->frozenSamples = $frozenSamples;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
