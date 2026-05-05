@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Edit Leave Request') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('leave.update', $leave->id) }}">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-select" required>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $leave->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Leave Type') }} <span class="text-danger">*</span></label>
                                    <select name="leave_type" class="form-select" required>
                                        <option value="Annual" {{ $leave->leave_type == 'Annual' ? 'selected' : '' }}>{{ __('Annual') }}</option>
                                        <option value="Sick" {{ $leave->leave_type == 'Sick' ? 'selected' : '' }}>{{ __('Sick') }}</option>
                                        <option value="Personal" {{ $leave->leave_type == 'Personal' ? 'selected' : '' }}>{{ __('Personal') }}</option>
                                        <option value="Unpaid" {{ $leave->leave_type == 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                        <option value="Other" {{ $leave->leave_type == 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" required value="{{ $leave->start_date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" required value="{{ $leave->end_date }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Reason') }} <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required>{{ $leave->reason }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $leave->notes }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('leave.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
