@extends('job-runner::layouts.DashboardLayout')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="card-action-title">Background Job Details</div>
    </div>
    </h5>
    <div class="table-responsive text-nowrap pb-5">

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $job->id }}</td>
            </tr>
            <tr>
                <th>Priority</th>
                <td>{{ $job->priority }}</td>
            </tr>
            <tr>
                <th>Attempts</th>
                <td>{{ $job->attempts }}</td>
            </tr>
            <tr>
                <th>Available At</th>
                <td>{{ $job->available_at }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $job->status }}</td>
            </tr>
            <tr>
                <th>Class</th>
                <td>{{ $job->class }}</td>
            </tr>
            <tr>
                <th>Method</th>
                <td>{{ $job->method }}</td>
            </tr>
            <tr>
                <th>Max Retires</th>
                <td>{{ $job->max_retires }}</td>
            </tr>
            <tr>
                <th>Params</th>
                <td>
                    <ol>
                        @foreach ($job->params as $param)
                            <li>{{ $param }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
        
        </table>
    </div>
</div>

@endsection