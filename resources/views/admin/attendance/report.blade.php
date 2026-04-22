@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Attendance Report') }}</h4>
                <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Generate Report') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Present') }}</th>
                                    <th>{{ __('Late') }}</th>
                                    <th>{{ __('Absent') }}</th>
                                    <th>{{ __('On Leave') }}</th>
                                    <th>{{ __('Total Days') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary as $employeeId => $stats)
                                @php
                                    $employee = $attendances->firstWhere('employee_id', $employeeId)->employee ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $employee->full_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">{{ $stats['total_present'] }}</span></td>
                                    <td><span class="badge bg-warning">{{ $stats['total_late'] }}</span></td>
                                    <td><span class="badge bg-danger">{{ $stats['total_absent'] }}</span></td>
                                    <td><span class="badge bg-info">{{ $stats['total_leave'] }}</span></td>
                                    <td>{{ $stats['total_present'] + $stats['total_late'] + $stats['total_absent'] + $stats['total_leave'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('No attendance data found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
