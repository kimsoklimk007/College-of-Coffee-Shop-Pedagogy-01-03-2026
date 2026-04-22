@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Payroll') }}</h4>
                <div>
                    <a href="{{ route('payroll.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Calculate Payroll') }}
                    </a>
                    <a href="{{ route('payroll.report') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> {{ __('Report') }}
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
                            <div class="col-md-3">
                                <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
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
                                    <th>{{ __('Payment Date') }}</th>
                                    <th>{{ __('Basic Salary') }}</th>
                                    <th>{{ __('Gross Salary') }}</th>
                                    <th>{{ __('Deductions') }}</th>
                                    <th>{{ __('Net Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->employee->full_name ?? '-' }}</td>
                                    <td>{{ $payroll->payment_date }}</td>
                                    <td>${{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td>${{ number_format($payroll->gross_salary, 2) }}</td>
                                    <td>${{ number_format($payroll->total_deduction, 2) }}</td>
                                    <td>${{ number_format($payroll->net_salary, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payroll->status == 'Paid' ? 'success' : 'warning' }}">
                                            {{ __($payroll->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('payroll.show', $payroll->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('No payroll records found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $payrolls->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
