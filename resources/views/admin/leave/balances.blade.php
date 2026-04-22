@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Leave Balances') }}</h4>
                <a href="{{ route('leave.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="mb-0">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label>{{ __('Select Employee') }}</label>
                                <select name="employee_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('All Employees') }}</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @isset($employee)
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Employee') }}: {{ $employee->full_name }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Leave Type') }}</th>
                                    <th>{{ __('Total Days') }}</th>
                                    <th>{{ __('Used Days') }}</th>
                                    <th>{{ __('Remaining Days') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($balances as $balance)
                                <tr>
                                    <td>{{ __($balance->leave_type) }}</td>
                                    <td>{{ $balance->total_days }}</td>
                                    <td>{{ $balance->used_days }}</td>
                                    <td>{{ $balance->total_days - $balance->used_days }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No leave balances found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endisset
        </div>
    </div>
</div>
@endsection
