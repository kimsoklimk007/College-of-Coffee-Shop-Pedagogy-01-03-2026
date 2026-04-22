@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Attendance') }}</h4>
                <div>
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add Record') }}
                    </a>
                    <a href="{{ route('attendance.report') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> {{ __('Report') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <select name="employee_id" class="form-select">
                                    <option value="">{{ __('All Employees') }}</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>{{ __('Present') }}</option>
                                    <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }}>{{ __('Late') }}</option>
                                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>{{ __('Absent') }}</option>
                                    <option value="On Leave" {{ request('status') == 'On Leave' ? 'selected' : '' }}>{{ __('On Leave') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Shift') }}</th>
                                    <th>{{ __('Check In') }}</th>
                                    <th>{{ __('Check Out') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date }}</td>
                                    <td>{{ $attendance->employee->full_name ?? '-' }}</td>
                                    <td>{{ $attendance->shift->name ?? '-' }}</td>
                                    <td>{{ $attendance->check_in_time }}</td>
                                    <td>{{ $attendance->check_out_time ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status == 'Present' ? 'success' : ($attendance->status == 'Late' ? 'warning' : ($attendance->status == 'Absent' ? 'danger' : 'info')) }}">
                                            {{ __($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('attendance.show', $attendance->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('attendance.delete', $attendance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No attendance records found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
