@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Payroll Report') }}</h4>
                <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ __('Select Month') }}</label>
                                <input type="month" name="month" class="form-control" value="{{ $month }}" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>${{ number_format($totalGross, 2) }}</h3>
                            <p>{{ __('Total Gross Salary') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>${{ number_format($totalDeduction, 2) }}</h3>
                            <p>{{ __('Total Deductions') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>${{ number_format($totalNet, 2) }}</h3>
                            <p>{{ __('Total Net Salary') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th class="text-end">{{ __('Basic Salary') }}</th>
                                    <th class="text-end">{{ __('Gross Salary') }}</th>
                                    <th class="text-end">{{ __('Deductions') }}</th>
                                    <th class="text-end">{{ __('Net Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->employee->full_name ?? '-' }}</td>
                                    <td>{{ $payroll->payment_date }}</td>
                                    <td class="text-end">${{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td class="text-end">${{ number_format($payroll->gross_salary, 2) }}</td>
                                    <td class="text-end">${{ number_format($payroll->total_deduction, 2) }}</td>
                                    <td class="text-end">${{ number_format($payroll->net_salary, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payroll->status == 'Paid' ? 'success' : 'warning' }}">
                                            {{ __($payroll->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No payroll records found for this month') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary">
                                    <th colspan="3">{{ __('Total') }}</th>
                                    <th class="text-end">${{ number_format($totalGross, 2) }}</th>
                                    <th class="text-end">${{ number_format($totalDeduction, 2) }}</th>
                                    <th class="text-end">${{ number_format($totalNet, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
