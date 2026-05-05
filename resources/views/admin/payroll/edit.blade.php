@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Edit Payroll') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('payroll.update', $payroll->id) }}">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-select" required>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" class="form-control" required value="{{ $payroll->payment_date }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="basic_salary" class="form-control" required value="{{ $payroll->basic_salary }}" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Pending" {{ $payroll->status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                        <option value="Paid" {{ $payroll->status == 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $payroll->notes }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('payroll.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
