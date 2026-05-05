@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Shift Details') }}</h4>
                <div>
                    <a href="{{ route('shift.edit', $shift->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('shift.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Name') }}:</th>
                                    <td>{{ $shift->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Start Time') }}:</th>
                                    <td>{{ $shift->start_time }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('End Time') }}:</th>
                                    <td>{{ $shift->end_time }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Late Threshold') }}:</th>
                                    <td>{{ $shift->late_threshold ?? 15 }} {{ __('minutes') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        <span class="badge bg-{{ $shift->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ __($shift->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Description') }}:</th>
                                    <td>{{ $shift->description ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($shift->employees->count() > 0)
                    <hr>
                    <h5>{{ __('Assigned Employees') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shift->employees as $employee)
                                <tr>
                                    <td>{{ $employee->employee_id }}</td>
                                    <td>{{ $employee->full_name }}</td>
                                    <td>{{ $employee->phone }}</td>
                                    <td>
                                        <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ __($employee->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
