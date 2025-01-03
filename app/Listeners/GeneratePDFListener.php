<?php

namespace App\Listeners;

use App\Events\GeneratePDF;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GeneratePDFListener implements ShouldQueue
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
     * @param  \App\Events\GeneratePDF  $event
     * @return void
     */
    public function handle(GeneratePDF $event)
    {
//        $tasks = $event->tasks;
//        $summary = $event->summary;
//        $pick_sum_data = $event->pick_sum_data;
//        $client_logo = $event->client_logo;
//        $visited_orginization = $event->visited_orginization;
//        $served_orginization = $event->served_orginization;
//        $frozenBags = $event->frozenBags;
//        $roomBags = $event->roomBags;
//        $refBags = $event->refBags;
//        $roomSamples = $event->roomSamples;
//        $refSamples = $event->refSamples;
//        $frozenSamples = $event->frozenSamples;
//        PDF::loadView('livewire.exports.tasks.tasks',
//            compact('tasks','summary','pick_sum_data','client_logo',
//                'visited_orginization','served_orginization','frozenBags','roomBags','refBags','roomSamples','refSamples','frozenSamples'))
//            ->save(public_path() . '/export/tasks.pdf');;
    }
}
