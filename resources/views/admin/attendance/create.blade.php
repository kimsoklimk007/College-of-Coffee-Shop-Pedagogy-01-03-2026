@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Add Attendance Record') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Check In Time') }} <span class="text-danger">*</span></label>
                                    <input type="time" name="check_in_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Check Out Time') }}</label>
                                    <input type="time" name="check_out_time" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Present">{{ __('Present') }}</option>
                                        <option value="Late">{{ __('Late') }}</option>
                                        <option value="Absent">{{ __('Absent') }}</option>
                                        <option value="On Leave">{{ __('On Leave') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
