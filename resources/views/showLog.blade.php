@extends('job-runner::layouts.DashboardLayout')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-action-title">Job Log</div>
        </div>
        <div class="card-body">
            <div style="overflow-y: auto; height: 500px; border: 1px solid #ccc; padding: 10px; background-color: #f9f9f9;">
                <pre>{{ $Content }}</pre>
            </div>
        </div>

    </div>
@endsection
