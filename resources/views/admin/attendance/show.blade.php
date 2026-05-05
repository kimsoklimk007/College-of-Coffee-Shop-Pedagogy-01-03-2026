@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Attendance Details') }}</h4>
                <div>
                    <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
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
                                    <th>{{ __('Date') }}:</th>
                                    <td>{{ $attendance->attendance_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Employee') }}:</th>
                                    <td>{{ $attendance->employee->full_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Employee ID') }}:</th>
                                    <td>{{ $attendance->employee->employee_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Shift') }}:</th>
                                    <td>{{ $attendance->shift->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Check In') }}:</th>
                                    <td>{{ $attendance->check_in_time }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Check Out') }}:</th>
                                    <td>{{ $attendance->check_out_time ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status == 'Present' ? 'success' : ($attendance->status == 'Late' ? 'warning' : ($attendance->status == 'Absent' ? 'danger' : 'info')) }}">
                                            {{ __($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Notes') }}:</th>
                                    <td>{{ $attendance->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
