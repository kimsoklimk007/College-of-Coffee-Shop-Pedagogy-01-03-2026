@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Leaves') }}</h4>
                <div>
                    <a href="{{ route('leave.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add Leave') }}
                    </a>
                    <a href="{{ route('leave.balances') }}" class="btn btn-info">
                        <i class="fas fa-calendar-check"></i> {{ __('Leave Balances') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
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
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="leave_type" class="form-select">
                                    <option value="">{{ __('All Types') }}</option>
                                    <option value="Annual" {{ request('leave_type') == 'Annual' ? 'selected' : '' }}>{{ __('Annual') }}</option>
                                    <option value="Sick" {{ request('leave_type') == 'Sick' ? 'selected' : '' }}>{{ __('Sick') }}</option>
                                    <option value="Personal" {{ request('leave_type') == 'Personal' ? 'selected' : '' }}>{{ __('Personal') }}</option>
                                    <option value="Unpaid" {{ request('leave_type') == 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
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
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                <tr>
                                    <td>{{ $leave->employee->full_name ?? '-' }}</td>
                                    <td>{{ __($leave->leave_type) }}</td>
                                    <td>{{ $leave->start_date }}</td>
                                    <td>{{ $leave->end_date }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td>
                                        <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Pending' ? 'warning' : 'danger') }}">
                                            {{ __($leave->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('leave.show', $leave->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($leave->status == 'Pending')
                                        <form action="{{ route('leave.approve', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Approve this leave?') }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('leave.reject', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Reject this leave?') }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No leave records found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
