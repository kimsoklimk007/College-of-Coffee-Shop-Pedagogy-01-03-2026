@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Role Details') }}</h4>
                <div>
                    <a href="{{ route('role.edit', $role->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('role.index') }}" class="btn btn-secondary">
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
                                    <th>{{ __('Name') }}:</th>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Description') }}:</th>
                                    <td>{{ $role->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        <span class="badge bg-{{ $role->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ __($role->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Employees') }}:</th>
                                    <td>{{ $role->employees()->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @php
                        $permissions = json_decode($role->permissions ?? '[]', true) ?? [];
                        $permissionLabels = [
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

                    @if(count($permissions) > 0)
                    <hr>
                    <h5>{{ __('Permissions') }}</h5>
                    <div class="row">
                        @foreach($permissions as $perm)
                        @if(isset($permissionLabels[$perm]))
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-primary"><i class="fas fa-check"></i> {{ __($permissionLabels[$perm]) }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($role->employees->count() > 0)
                    <hr>
                    <h5>{{ __('Assigned Employees') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->employees as $employee)
                                <tr>
                                    <td>{{ $employee->employee_id }}</td>
                                    <td>{{ $employee->full_name }}</td>
                                    <td>{{ $employee->phone }}</td>
                                    <td>
                                        <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ __($employee->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
