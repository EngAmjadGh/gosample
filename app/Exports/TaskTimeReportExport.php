<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class TaskTimeReportExport implements FromQuery, WithHeadings, WithMapping,ShouldAutoSize , WithEvents
{
    use Exportable;

    private $status;
    private $date_from;
    private $date_to;
    private $billingClient;
    private $fromLocation;
    private $toLocation;
    private $driverId;
    private $cnt;

    public function __construct($status, $date_from, $date_to, $billingClient, $fromLocation, $toLocation, $driverId)
    {
        $this->status = $status;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->billingClient = $billingClient;
        $this->fromLocation = $fromLocation;
        $this->toLocation = $toLocation;
        $this->driverId = $driverId;
    }

    public function query()
    {

        // Define your query to retrieve the tasks based on the provided filter parameters
        $query = Task::with('driver', 'from', 'to', 'client');

        // if ($this->status) {
        //     $query->where('status', '=', $this->status);
        // }

        // if ($this->date_from) {
        //     $query->where('created_at', '>=', $this->date_from);
        // }

        // if ($this->date_to) {
        //     $query->where('created_at', '<=', $this->date_to);
        // }

        if ($this->date_from && $this->date_to) {
            $query->whereBetween('collection_date', [$this->date_from, $this->date_to]);
        } elseif ($this->date_from) {
            $query->where('collection_date', '>=', $this->date_from);
        } elseif ($this->date_to) {
            $query->where('collection_date', '<=', $this->date_to);
        }



        if ($this->billingClient) {
            $query->where('billing_client', '=', $this->billingClient);
        }

        if ($this->fromLocation) {
            $query->where('from_location', '=', $this->fromLocation);
        }

        if ($this->toLocation) {
            $query->where('to_location', '=', $this->toLocation);
        }

        if ($this->driverId) {
            $query->where('driver_id', '=', $this->driverId);
        }
        $this->cnt = $query->count();
        return $query;
    }



    // public function query()
    // {
    //     // Define your query to retrieve the tasks you want to include in the report
    //     return Task::with('driver','from','to','client')->select('*');
    // }

    public function headings(): array
    {
        // Define the column headings for the Excel report
        return [
            'Task ID',
            'Created Date',
            'Accepted Time',
            'Reach Time',
            'Started Time',
            'Expected Pickup Time',
            'Collected Date',
            'Collected Time',
            'Expected Dropoff Time',
            'Close Date',
            'Close Time',
            'Driver',
            'Client',
            'From Location',
            'To Location',
            'Time in Minutes (Reach to Expected Pickup)',
            'Time in Minutes (Collection Date to Pickup Time)',
            'Time in Minutes (Close Date to Dropoff Time)',
        ];
    }


    public function map($task): array
    {
        $from_location_arrival_time = $task->from_location_arrival_time ? Carbon::parse($task->from_location_arrival_time) : null;
        $acceptedTime = $task->task_confirmation_timestamp ? Carbon::parse($task->task_confirmation_timestamp) : null;
        $startedTime = $task->driver_start_date ;
        // $startedTime = $task->driver_start_date ? Carbon::parse($task->driver_start_date) : null;
        $pickupTime = $task->pickup_time ? Carbon::parse($task->pickup_time) : null;
        $dropoffTime = $task->dropoff_time ? Carbon::parse($task->dropoff_time) : null;
        $confirmationTime = $task->task_confirmation_timestamp ? Carbon::parse($task->task_confirmation_timestamp) : null;
        $fromLocationConfirmationTime = $task->from_location_confirmation_timestamp ? Carbon::parse($task->from_location_confirmation_timestamp) : null;
        $toLocationConfirmationTime = $task->to_location_confirmation_timestamp ? Carbon::parse($task->to_location_confirmation_timestamp) : null;
        $driverStartDate = $task->driver_start_date ? Carbon::parse($task->driver_start_date) : null;
        $collectionDateTime = $task->collection_date ? Carbon::parse($task->collection_date) : null;
        $collectionDate = $collectionDateTime ? $collectionDateTime->format('Y-m-d') : null;
        $collectionTime = $collectionDateTime ? $collectionDateTime->format('H:i:s') : null;

        $freezerDate = $task->freezer_date ? Carbon::parse($task->freezer_date) : null;
        $freezerOutDate = $task->freezer_out_date ? Carbon::parse($task->freezer_out_date) : null;
        $toLocationArrivalTime = $task->to_location_arrival_time ? Carbon::parse($task->to_location_arrival_time) : null;
        $dropoffTime = $task->dropoff_time ? Carbon::parse($task->dropoff_time) : null;
        $closeDateTime = $task->close_date ? Carbon::parse($task->close_date) : null;
        $closeDate = $closeDateTime ? $closeDateTime->format('Y-m-d') : null;
        $closeTime = $closeDateTime ? $closeDateTime->format('H:i:s') : null;

        // Calculate the time differences in minutes with validation for null values
        $reachToPickup = $task->from_location_confirmation_timestamp && $pickupTime ? $task->from_location_confirmation_timestamp->diffInMinutes($pickupTime) : null;

        $collectionToPickupTime = $collectionDateTime && $pickupTime ? $collectionDateTime->diffInMinutes($pickupTime) : null;
        $closeToDropoff = $closeDateTime && $dropoffTime ? $closeDateTime->diffInMinutes($dropoffTime) : null;

        // Map the task data and calculated times to the columns
        return [
            $task->id,
            $task->created_at,
            $acceptedTime,
            $task->from_location_confirmation_timestamp,
            $task->driver_start_date,
            $pickupTime,
            $collectionDate,
            $collectionTime,
            $dropoffTime,
            $closeDate,
            $closeTime,
            $task->driver ? $task->driver->name : 'N/A',
            $task->client ? $task->client->english_name : 'N/A',
            $task->from ? $task->from->name : 'N/A',
            $task->to ? $task->to->name : 'N/A',
            $reachToPickup ? $reachToPickup : 'N/A',
            $collectionToPickupTime ? $collectionToPickupTime : 'N/A',
            $closeToDropoff ? $closeToDropoff : 'N/A',
        ];
    }

    public function registerEvents(): array
    {
        //need get count tasks
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->append(["Number of tasks : ".$this->cnt ?? 0]);
            },
        ];
    }


}
