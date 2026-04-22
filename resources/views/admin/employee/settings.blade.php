@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Employee Settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Employee Settings') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted text-uppercase fw-semibold">{{ __('Total Employees') }}</h5>
                            <h3 class="mb-0">{{ $totalEmployees }}</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title bg-primary-subtle text-primary rounded fs-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted text-uppercase fw-semibold">{{ __('Active') }}</h5>
                            <h3 class="mb-0 text-success">{{ $activeEmployees }}</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title bg-success-subtle text-success rounded fs-2">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted text-uppercase fw-semibold">{{ __('Present Today') }}</h5>
                            <h3 class="mb-0 text-info">{{ $presentToday }}</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title bg-info-subtle text-info rounded fs-2">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-muted text-uppercase fw-semibold">{{ __('Monthly Payroll') }}</h5>
                            <h3 class="mb-0 text-warning">${{ number_format($monthlyPayroll, 2) }}</h3>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <div class="avatar-title bg-warning-subtle text-warning rounded fs-2">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Management Sections -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Employee Management System') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md bg-primary-subtle text-primary rounded">
                                        <i class="fas fa-user-plus fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Employee Registration') }}</h6>
                                    <p class="text-muted mb-2">{{ __('Register new employees with personal information') }}</p>
                                    <a href="{{ route('employee.create') }}" class="btn btn-sm btn-primary">{{ __('Add Employee') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md bg-success-subtle text-success rounded">
                                        <i class="fas fa-qrcode fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('QR Code System') }}</h6>
                                    <p class="text-muted mb-2">{{ __('Generate QR codes for employee attendance') }}</p>
                                    <a href="{{ route('employee.index') }}" class="btn btn-sm btn-success">{{ __('View Employees') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md bg-info-subtle text-info rounded">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Shift Management') }}</h6>
                                    <p class="text-muted mb-2">{{ __('Manage work shifts and schedules') }}</p>
                                    <a href="{{ route('shift.index') }}" class="btn btn-sm btn-info">{{ __('Manage Shifts') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md bg-warning-subtle text-warning rounded">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Payroll System') }}</h6>
                                    <p class="text-muted mb-2">{{ __('Calculate and manage employee salaries') }}</p>
                                    <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-warning">{{ __('Payroll') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Today\'s Attendance') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <h2 class="mb-0">{{ $presentToday + $lateToday }}</h2>
                            <p class="text-muted">{{ __('Present') }}</p>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="mb-0 text-success">{{ $presentToday }}</h4>
                                    <p class="text-muted mb-0">{{ __('On Time') }}</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="mb-0 text-warning">{{ $lateToday }}</h4>
                                    <p class="text-muted mb-0">{{ __('Late') }}</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="mb-0 text-danger">{{ $absentToday }}</h4>
                                    <p class="text-muted mb-0">{{ __('Absent') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Employee Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ __('Active') }}</span>
                            <span class="badge bg-success">{{ $activeEmployees }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalEmployees > 0 ? ($activeEmployees / $totalEmployees) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ __('Inactive') }}</span>
                            <span class="badge bg-secondary">{{ $inactiveEmployees }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $totalEmployees > 0 ? ($inactiveEmployees / $totalEmployees) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ __('Resigned') }}</span>
                            <span class="badge bg-danger">{{ $resignedEmployees }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalEmployees > 0 ? ($resignedEmployees / $totalEmployees) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('employee.index') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-list me-2"></i>{{ __('View All Employees') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('employee.export') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-download me-2"></i>{{ __('Export Reports') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('attendance.report') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-chart-bar me-2"></i>{{ __('Attendance Report') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('role.index') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-cog me-2"></i>{{ __('System Settings') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
