<style>
    th {
        text-align: center !important;
    }
</style>
<div class="container">
    <div class="row " style="float: none;">
        <table border="1" cellspacing="0" cellpadding="0" style='margin-bottom:30px'>
            <tr>

                <td rowspan='6' style="text-align:center">
                    <img src="assets/img/logo_excel.jpg" style="height:70px">
                    <h4>LAB SAMPLES TRANSPORTATION </h4>
                    <h4>DAILY REPORT </h4>
                    <h4>{{ Carbon\Carbon::now() }}</h4>
                </td>
                <td colspan="2" style="text-align:center">
                    PICK UP SUMMARY
                </td>

            </tr>
            <tr>
                <td>
                    TYPE
                </td>
                <td>
                    QTY.
                </td>

            </tr>
            <tr>
                <td>
                    Served Organizataions
                </td>
                <td>{{ count($tasks) }}</td>
            </tr>
            <tr>
                <td>
                    Pickuped Containers
                </td>
                <td>
                    {{ $bag_count }}
                </td>
            </tr>
            <tr>
                <td>
                    Pickuped Samples (As Per Data)
                </td>
                <td>
                    <?php $t_sample = 0; ?>
                    @foreach ($tasks as $task)
                        <?php $t_sample = $t_sample + count($task->bags); ?>
                    @endforeach
                    {{ $t_sample }}
                </td>
            </tr>
            <tr>
                <td>
                    Visted Organizations / No Samples
                </td>
                <td>{{ $no_sample_tasks }}</td>
            </tr>

        </table>


        <table border="1" style='border:1 px solid #000' cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th style="text-align:center"> Task ID</th>
                    <th style="text-align:center"> Order Date</th>
                    <th style="text-align:center"> Pick-up Location </th>
                    <th style="text-align:center"> Arrival at pick-up </th>
                    <th style="text-align:center"> Pick-up Stay Time </th>
                    <th style="text-align:center"> Sample Placement</th>
                    <th style="text-align:center"> Delivery Location</th>
                    <th style="text-align:center"> Delivery Arrival Time</th>
                    <th style="text-align:center"> Delivery Stay Time</th>
                    <th style="text-align:center"> Delivery Closing</th>
                    <th style="text-align:center"> Bill To</th>
                    <th style="text-align:center"> Driver Name</th>
                    <th style="text-align:center"> Sample Type</th>
                    <th style="text-align:center"> Box Count</th>
                    <th style="text-align:center"> Box Count</th>
                    <th style="text-align:center"> Sample count (As per driver)</th>
                    <th style="text-align:center"> Barcode</th>
                    <th style="text-align:center"> Temperature Selected:</th>
                    {{-- <th style="text-align:center"> Average Trip Temperature</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr style='text-align:center !important;'>
                        <td style="text-align:center">{{ $task->id }}</td>
                        <td style="text-align:center">{{ $task->created_at }}</td>
                        <td style="text-align:center">{{ $task->from_organization_name }}</td>
                        <td style="text-align:center">{{ $task->from_location_arrival_time }}</td>
                        <td style="text-align:center">
                            {{ $task->from_stay_time }}
                        </td>
                        <td style="text-align:center">{{ $task->freezer_date }}</td>

                        <td style="text-align:center">
                            {{ $task->to_organization_name }}
                        </td>
                        <td style="text-align:center">
                            {{ $task->to_location_arrival_time }}
                        </td>
                        <td style="text-align:center">
                            {{ $task->to_stay_time }}
                        </td>

                        <td style="text-align:center">
                            {{ $task->close_date }}
                        </td>
                        {{-- bill to --}}
                        <td style="text-align:center">
                            {{ $task->clientName }}
                            {{--                {{$task->client?$task->client->english_name:''}} --}}
                        </td>
                        {{-- drier name --}}
                        <td style="text-align:center">
                            {{ $task->driverName }}
                        </td>
                        <td style="text-align:center">
                            {{ $task->sample_types }}
                        </td>
                        <td>
                            {{ count($task->data) }}
                        </td>
                        <td>
                            {{ count($task->data) }}
                        </td>
                        {{-- sample count --}}
                        <td>
                            {{ $task->bags_count }}
                        </td>

                        {{-- barcode --}}
                        <th style="text-align:center">
                            @foreach ($task->bags as $barcode)
                                {{ $barcode }} @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </th>

                        {{-- temperature --}}
                        <td style="text-align:center">
                            @foreach ($task->data as $row)
                                {{ $row->temperature_label }} @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </td>
                        {{-- average trip temperature --}}
                        {{-- <td style="text-align:center">
                @foreach ($task->samples()->groupBy('temperature_type')->get() as $sample)
                    {{$sample->temperature_type}} @if (!$loop->last) , @endif
                @endforeach
            </td> --}}

                        {{-- <td>
                {{Carbon\Carbon::parse($task->freezer_date)->format('H:i')}}
            </td>
            <td>
                {{Carbon\Carbon::parse($task->freezer_out_date)->format('H:i')}}
            </td>
            <td>
                {{ $task->fromLocation->name }}
            </td>
            <td>
                {{ $task->toLocation->name }}
            </td>
            <td>
                {{ $task->status}}
            </td>
            <td>
                {{count($task->samples)}}
            </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
