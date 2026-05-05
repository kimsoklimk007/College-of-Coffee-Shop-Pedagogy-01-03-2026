@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Payroll Calculation') }} - {{ $month }}</h4>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Employee Information') }}</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Name') }}:</th>
                                    <td>{{ $employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Employee ID') }}:</th>
                                    <td>{{ $employee->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Base Salary') }}:</th>
                                    <td>${{ number_format($base_salary, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>{{ __('Attendance Summary') }}</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $working_days }}</h3>
                                    <p>{{ __('Working Days') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $late_days }}</h3>
                                    <p>{{ __('Late Days') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $absent_days }}</h3>
                                    <p>{{ __('Absent Days') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $leave_days }}</h3>
                                    <p>{{ __('Leave Days') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>{{ __('Salary Breakdown') }}</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('Worked Salary') }} ({{ $working_days }} days)</td>
                                    <td class="text-end">${{ number_format($worked_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Overtime Pay') }}</td>
                                    <td class="text-end">${{ number_format($overtime_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Bonus') }}</td>
                                    <td class="text-end">${{ number_format($bonus, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Allowance') }}</td>
                                    <td class="text-end">${{ number_format($allowance, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>{{ __('Gross Salary') }}</strong></td>
                                    <td class="text-end"><strong>${{ number_format($gross_salary, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5>{{ __('Deductions') }}</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('Late Deduction') }} ({{ $late_days }} days)</td>
                                    <td class="text-end">-${{ number_format($late_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Absent Deduction') }} ({{ $absent_days }} days)</td>
                                    <td class="text-end">-${{ number_format($absent_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Other Deduction') }}</td>
                                    <td class="text-end">-${{ number_format($other_deduction, 2) }}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>{{ __('Total Deductions') }}</strong></td>
                                    <td class="text-end"><strong>-${{ number_format($total_deduction, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2>${{ number_format($net_salary, 2) }}</h2>
                                    <p>{{ __('Net Salary') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('payroll.store') }}">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="basic_salary" value="{{ $base_salary }}">
                        <input type="hidden" name="working_days" value="{{ $working_days }}">
                        <input type="hidden" name="late_days" value="{{ $late_days }}">
                        <input type="hidden" name="absent_days" value="{{ $absent_days }}">
                        <input type="hidden" name="overtime_hours" value="0">
                        <input type="hidden" name="overtime_rate" value="0">
                        <input type="hidden" name="overtime_pay" value="{{ $overtime_pay }}">
                        <input type="hidden" name="bonus" value="{{ $bonus }}">
                        <input type="hidden" name="allowance" value="{{ $allowance }}">
                        <input type="hidden" name="late_deduction" value="{{ $late_deduction }}">
                        <input type="hidden" name="absent_deduction" value="{{ $absent_deduction }}">
                        <input type="hidden" name="other_deduction" value="{{ $other_deduction }}">
                        <input type="hidden" name="gross_salary" value="{{ $gross_salary }}">
                        <input type="hidden" name="total_deduction" value="{{ $total_deduction }}">
                        <input type="hidden" name="net_salary" value="{{ $net_salary }}">
                        <input type="hidden" name="status" value="Pending">

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ __('Save Payroll') }}
                        </button>
                        <a href="{{ route('payroll.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
