@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Calculate Payroll') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('payroll.calculate') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }} - ${{ number_format($employee->base_salary, 2) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Month') }} <span class="text-danger">*</span></label>
                                    <input type="month" name="month" class="form-control" required value="{{ date('Y-m') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Bonus') }}</label>
                                    <input type="number" name="bonus" class="form-control" value="0" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Allowance') }}</label>
                                    <input type="number" name="allowance" class="form-control" value="0" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Other Deductions') }}</label>
                            <input type="number" name="deduction" class="form-control" value="0" min="0" step="0.01">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calculator"></i> {{ __('Calculate') }}
                        </button>
                        <a href="{{ route('payroll.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
