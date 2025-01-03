@extends('layouts.master')
@section('content')
    <h1>Scheduled Task Logs</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Level</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log['date'] }}</td>
                    <td>{{ $log['level'] }}</td>
                    <td>{{ $log['message'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
