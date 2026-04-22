@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Payroll Details') }}</h4>
                <div>
                    <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
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
                                    <th>{{ __('Employee') }}:</th>
                                    <td>{{ $payroll->employee->full_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Employee ID') }}:</th>
                                    <td>{{ $payroll->employee->employee_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Payment Date') }}:</th>
                                    <td>{{ $payroll->payment_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        <span class="badge bg-{{ $payroll->status == 'Paid' ? 'success' : 'warning' }}">
                                            {{ __($payroll->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('Basic Salary') }}</td>
                                    <td class="text-end">${{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Working Days') }}</td>
                                    <td class="text-end">{{ $payroll->working_days }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Overtime Pay') }}</td>
                                    <td class="text-end">${{ number_format($payroll->overtime_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Bonus') }}</td>
                                    <td class="text-end">${{ number_format($payroll->bonus, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Allowance') }}</td>
                                    <td class="text-end">${{ number_format($payroll->allowance, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>{{ __('Gross Salary') }}</strong></td>
                                    <td class="text-end"><strong>${{ number_format($payroll->gross_salary, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Deductions') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('Late Deduction') }}</td>
                                    <td class="text-end">-${{ number_format($payroll->late_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Absent Deduction') }}</td>
                                    <td class="text-end">-${{ number_format($payroll->absent_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Other Deduction') }}</td>
                                    <td class="text-end">-${{ number_format($payroll->other_deduction, 2) }}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>{{ __('Total Deductions') }}</strong></td>
                                    <td class="text-end"><strong>-${{ number_format($payroll->total_deduction, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2>${{ number_format($payroll->net_salary, 2) }}</h2>
                                    <p>{{ __('Net Salary') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payroll->notes)
                    <hr>
                    <h5>{{ __('Notes') }}</h5>
                    <p>{{ $payroll->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
