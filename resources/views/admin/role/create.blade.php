@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Add New Role') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('role.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>{{ __('Permissions') }}</label>
                            <div class="row">
                                @php
                                    $permissions = [
                                        'employee_view' => 'View Employees',
                                        'employee_create' => 'Create Employees',
                                        'employee_edit' => 'Edit Employees',
                                        'employee_delete' => 'Delete Employees',
                                        'attendance_view' => 'View Attendance',
                                        'attendance_manage' => 'Manage Attendance',
                                        'payroll_view' => 'View Payroll',
                                        'payroll_manage' => 'Manage Payroll',
                                        'leave_approve' => 'Approve Leave',
                                        'report_view' => 'View Reports',
                                    ];
                                @endphp
                                @foreach($permissions as $key => $label)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="form-check-input" id="perm_{{ $key }}">
                                        <label class="form-check-label" for="perm_{{ $key }}">{{ __($label) }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('role.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
