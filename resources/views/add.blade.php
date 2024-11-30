@extends('job-runner::layouts.DashboardLayout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add new Job</h5>
                </div>
                    <form action="<?= @$Edit != '' ? url('mralgorithm/jobs/' . @$Edit['id']) : url('mralgorithm/jobs') ?>" method="post">
                    @csrf
                    @if (@$Edit != '')
                    @method('put')
                @endif
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Class</label>
                                <input type="text" class="form-control" name="class" id="class"
                                    value="{{ old('class') ?? @$Edit['class'] }}" placeholder="Class" />
                            </div>

                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Method</label>
                                <input type="text" class="form-control" name="method" id="method"
                                    value="{{ old('method') ?? @$Edit['method'] }}" placeholder="Method" />
                            </div>

                        </div>

                        <span class="text-muted">
                            Parameters
                        </span>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Number Of Parameters</label>
                                <input type="text" class="form-control" name="parameters" id="parameters"
                                    value="{{ old('parameters') ?? (@$Edit != '' && count(@$Edit['params'])) ?? '3' }}"
                                    placeholder="Number Of Parameters" onchange="numberOfParameters()" />
                            </div>
                        </div>
                        <div class="row" id="params_con">
                            @php
                                $params = @$Edit->params != '' ? @$Edit->params : old('params');
                            @endphp
                            @if ($params != '')
                                @foreach ($params as $index => $p)
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <label class="form-label" for="param{{ $index + 1 }}">Param
                                            {{ $index + 1 }}</label>
                                        <input type="text" class="form-control" name="params[]"
                                            id="param{{ $index + 1 }}" value="{{ $p }}"
                                            placeholder="Param {{ $index + 1 }}" />
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <span class="text-muted">
                            Other Details
                        </span>

                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Delay(In Seconds)</label>
                                <input type="text" class="form-control" name="delay" id="delay"
                                    value="{{ old('delay') ?? (@$Edit['delay'] ?? '0') }}" placeholder="Delay(In Seconds)" />
                            </div>

                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Priority</label>
                                <input type="text" class="form-control" name="priority" id="priority"
                                    value="{{ old('priority') ?? (@$Edit['priority'] ?? '0') }}" placeholder="Priority" />
                            </div>


                            <div class="col-md-6 col-sm-12 mb-3">
                                <label class="form-label" for="role-name">Max Retires</label>
                                <input type="text" class="form-control" name="max_retires" id="max_retires"
                                    value="{{ old('max_retires') ?? (@$Edit['max_retires'] ?? '0') }}"
                                    placeholder="Max Retires" />
                            </div>

                        </div>
                        <button type="submit" class="btn btn-success">Add</button>

                    </div>

                    </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function numberOfParameters() {
            var number_of_params = document.getElementById('parameters').value;
            if (number_of_params >= 0) {
                let html = '';
                for (let x = 1; x <= number_of_params; x++) {
                    html += '<div class="col-md-6 col-sm-12 mb-3"> \n' +
                        '<label class="form-label" for="role-name">Param ' + x + '</label>\n' +
                        '<input type="text" class="form-control" name="params[]" id="param' + x + '"\n' +
                        ' placeholder="Param ' + x + '" />\n' +
                        '</div>';
                }
                document.getElementById("params_con").innerHTML = html;
            }
        }
        numberOfParameters();
    </script>
@endsection
