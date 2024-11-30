@extends('job-runner::layouts.DashboardLayout')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-action-title">Background job list</div>
        </div>
        </h5>
        <div class="table-responsive text-nowrap pb-5">
            <table class="table" style="text-align:center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Class</th>
                        <th>Method</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($Content as $C)
                        <tr>
                            <td>
                                {{ $C['id'] }}
                            </td>
                            <td>
                                {{ $C['class'] }}
                            </td>
                            <td>
                                {{ $C['method'] }}
                            </td>
                            <td>
                                {{ $C['priority'] }}
                            </td>
                            <td>
                                @switch($C['status'])
                                    @case('queued')
                                        <span class="badge bg-info">Queued</span>
                                    @break

                                    @case('processing')
                                        <span class="badge bg-primary">Processing</span>
                                    @break

                                    @case('completed')
                                        <span class="badge bg-success">Completed</span>
                                    @break

                                    @case('failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @break

                                    @case('paused')
                                        <span class="badge bg-warning">Paused</span>
                                    @break

                                    @case('retrying')
                                        <span class="badge bg-secondary">Retrying</span>
                                    @break

                                    @default
                                @endswitch
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ url('mralgorithm/jobs/' . $C['id'] . '/edit') }}"><i
                                                class="ti ti-pencil me-1"></i> Edit</a>

                                        @if ($C->status == 'queued' || $C->status == 'retrying')
                                            <a class="dropdown-item"
                                                href="{{ url('mralgorithm/jobs/' . $C['id'] . '/status/paused') }}"><i
                                                    class="ti ti-player-pause me-1"></i> Pause</a>
                                        @endif

                                        @if($C->status == 'paused')
                                        <a class="dropdown-item"
                                            href="{{ url('mralgorithm/jobs/' . $C['id'] . '/status/queued') }}"><i
                                                class="ti ti-player-play me-1"></i> Run</a>
                                        @endif

                                        <a class="dropdown-item"
                                            href="{{ url('mralgorithm/jobs/' . $C['id'] . '/log') }}"><i
                                                class="ti ti-file me-1"></i> Log</a>

                                        <a class="dropdown-item" href="{{ url('mralgorithm/jobs/' . $C['id']) }}"><i
                                                class="ti ti-dots-vertical me-1"></i> More Details</a>

                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_record"
                                            onclick="confirm_delete_recored('{{ url('mralgorithm/jobs/' . $C['id']) }}','{{ $C['class'] }}')"><i
                                                class="ti ti-trash me-1"></i> Delete</a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="my-pagination">
                {{ $Content->links('job-runner::vendor.pagination.default') }}
                <h1 class="mt-lg-5" />
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->

    </div>
@endsection
