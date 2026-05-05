@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Employee Details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('adminDashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.settings') }}">{{ __('Employee Settings') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('All Employees') }}</a></li>
                        <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Profile Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($employee->profile_photo)
                                <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                                     class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h3>{{ $employee->full_name }}</h3>
                            @if($employee->full_name_kh)
                                <h5 class="text-muted">{{ $employee->full_name_kh }}</h5>
                            @endif
                            <p class="mb-1">
                                <span class="badge bg-dark">{{ $employee->employee_id }}</span>
                                @if($employee->role)
                                    <span class="badge bg-primary ms-1">{{ $employee->role->name }}</span>
                                @endif
                                <span class="badge bg-{{ $employee->status === 'Active' ? 'success' : 'secondary' }} ms-1">
                                    {{ $employee->status }}
                                </span>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-phone me-2"></i>{{ $employee->phone }}
                                <i class="fas fa-envelope ms-3 me-2"></i>{{ $employee->email }}
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-2"></i>{{ __('Hired') }}: {{ $employee->hire_date->format('d/m/Y') }}
                                <i class="fas fa-clock ms-3 me-2"></i>{{ __('Service') }}: {{ $employee->service_years }} {{ __('years') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical" role="group">
                                <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> {{ __('Edit Employee') }}
                                </a>
                                @if($employee->has_qr_code)
                                    <a href="{{ asset('storage/' . $employee->qr_code_path) }}" target="_blank" class="btn btn-success">
                                        <i class="fas fa-qrcode me-1"></i> {{ __('View QR Code') }}
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('employee.generateQr', $employee->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-qrcode me-1"></i> {{ __('Generate QR Code') }}
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('employee.toggleStatus', $employee->id) }}" 
                                      onsubmit="return confirm('{{ __('Are you sure you want to toggle employee status?') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-toggle-on me-1"></i> {{ __('Toggle Status') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="employeeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" 
                                    type="button" role="tab">{{ __('Details') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" 
                                    type="button" role="tab">{{ __('Attendance') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#leaves" 
                                    type="button" role="tab">{{ __('Leaves') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" 
                                    type="button" role="tab">{{ __('Documents') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payroll-tab" data-bs-toggle="tab" data-bs-target="#payroll" 
                                    type="button" role="tab">{{ __('Payroll') }}</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="employeeTabsContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">{{ __('Personal Information') }}</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('Gender') }}:</strong></td>
                                            <td>{{ $employee->gender }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Date of Birth') }}:</strong></td>
                                            <td>{{ $employee->date_of_birth->format('d/m/Y') }} ({{ $employee->age }} {{ __('years') }})</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Nationality') }}:</strong></td>
                                            <td>{{ $employee->nationality }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('ID Card') }}:</strong></td>
                                            <td>{{ $employee->id_card_number ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Passport') }}:</strong></td>
                                            <td>{{ $employee->passport_number ?: 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">{{ __('Employment Information') }}</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('Role') }}:</strong></td>
                                            <td>{{ $employee->role->name ?? 'Not Assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Shift') }}:</strong></td>
                                            <td>{{ $employee->shift->name ?? 'Not Assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Employment Type') }}:</strong></td>
                                            <td>{{ $employee->employment_type }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Base Salary') }}:</strong></td>
                                            <td>${{ number_format($employee->base_salary, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('End Date') }}:</strong></td>
                                            <td>{{ $employee->end_date ? $employee->end_date->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">{{ __('Contact & Address') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('Phone') }}:</strong></td>
                                                    <td>{{ $employee->phone }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('Email') }}:</strong></td>
                                                    <td>{{ $employee->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('Emergency Contact') }}:</strong></td>
                                                    <td>{{ $employee->emergency_contact_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('Emergency Phone') }}:</strong></td>
                                                    <td>{{ $employee->emergency_contact_phone ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Address') }}:</strong></p>
                                            <p>{{ $employee->address }}</p>
                                            @if($employee->city || $employee->province)
                                                <p>{{ $employee->city }}, {{ $employee->province }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($employee->bank_name || $employee->bank_account_number)
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">{{ __('Bank Information') }}</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('Bank Name') }}:</strong></td>
                                                <td>{{ $employee->bank_name }}</td>
                                                <td><strong>{{ __('Account Number') }}:</strong></td>
                                                <td>{{ $employee->bank_account_number }}</td>
                                                <td><strong>{{ __('Account Name') }}:</strong></td>
                                                <td>{{ $employee->bank_account_name }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if($employee->notes)
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">{{ __('Notes') }}</h6>
                                        <p>{{ $employee->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Attendance Tab -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-success">{{ $attendanceStats['this_month']->whereIn('status', ['Present', 'Late'])->count() }}</h4>
                                            <p class="mb-0">{{ __('Present This Month') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-warning">{{ $attendanceStats['this_month']->where('status', 'Late')->count() }}</h4>
                                            <p class="mb-0">{{ __('Late This Month') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-danger">{{ $attendanceStats['this_month']->where('status', 'Absent')->count() }}</h4>
                                            <p class="mb-0">{{ __('Absent This Month') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-info">{{ $attendanceStats['this_month']->sum('total_work_minutes') / 60 }}</h4>
                                            <p class="mb-0">{{ __('Hours This Month') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Check In') }}</th>
                                            <th>{{ __('Check Out') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Work Hours') }}</th>
                                            <th>{{ __('Overtime') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->attendance->take(30) as $attendance)
                                            <tr>
                                                <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                                <td>{{ $attendance->formatted_check_in_time ?? '-' }}</td>
                                                <td>{{ $attendance->formatted_check_out_time ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ 
                                                        $attendance->status === 'Present' ? 'success' : 
                                                        ($attendance->status === 'Late' ? 'warning' : 'danger') 
                                                    }}">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                                <td>{{ round($attendance->total_work_hours, 2) }}</td>
                                                <td>{{ round($attendance->overtime_hours, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">{{ __('No attendance records found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Leaves Tab -->
                        <div class="tab-pane fade" id="leaves" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">{{ __('Leave Balances') }}</h6>
                                    <div class="row">
                                        @foreach($leaveBalances as $balance)
                                            <div class="col-md-3">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6>{{ $balance->getDisplayName() }}</h6>
                                                        <div class="progress mb-2" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ 
                                                                $balance->remaining_days > 5 ? 'success' : 
                                                                ($balance->remaining_days > 2 ? 'warning' : 'danger') 
                                                            }}" 
                                                                 style="width: {{ $balance->usage_percentage }}%"></div>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $balance->formatted_used_days }} / {{ $balance->formatted_total_days }} {{ __('used') }}
                                                        </small>
                                                        <br>
                                                        <strong>{{ $balance->formatted_remaining_days }} {{ __('available') }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Start Date') }}</th>
                                            <th>{{ __('End Date') }}</th>
                                            <th>{{ __('Days') }}</th>
                                            <th>{{ __('Reason') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->leaves->take(10) as $leave)
                                            <tr>
                                                <td>{{ $leave->leave_type }}</td>
                                                <td>{{ $leave->formatted_start_date }}</td>
                                                <td>{{ $leave->formatted_end_date }}</td>
                                                <td>{{ $leave->total_days }}</td>
                                                <td>{{ Str::limit($leave->display_reason, 50) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ 
                                                        $leave->status === 'Approved' ? 'success' : 
                                                        ($leave->status === 'Rejected' ? 'danger' : 'warning') 
                                                    }}">
                                                        {{ $leave->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">{{ __('No leave records found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Document Name') }}</th>
                                            <th>{{ __('File Size') }}</th>
                                            <th>{{ __('Issue Date') }}</th>
                                            <th>{{ __('Expiry Date') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->documents->take(10) as $document)
                                            <tr>
                                                <td>{{ $document->display_document_type }}</td>
                                                <td>{{ $document->document_name }}</td>
                                                <td>{{ $document->formatted_file_size }}</td>
                                                <td>{{ $document->formatted_issue_date ?? '-' }}</td>
                                                <td>
                                                    {{ $document->formatted_expiry_date ?? '-' }}
                                                    @if($document->is_expiring_soon())
                                                        <span class="badge bg-warning ms-1">{{ __('Expiring Soon') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ 
                                                        $document->status === 'Active' ? 'success' : 
                                                        ($document->status === 'Expired' ? 'danger' : 'secondary') 
                                                    }}">
                                                        {{ $document->status }}
                                                    </span>
                                                    @if($document->is_verified)
                                                        <i class="fas fa-check-circle text-success ms-1" title="{{ __('Verified') }}"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">{{ __('No documents found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payroll Tab -->
                        <div class="tab-pane fade" id="payroll" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Period') }}</th>
                                            <th>{{ __('Base Salary') }}</th>
                                            <th>{{ __('Overtime') }}</th>
                                            <th>{{ __('Deductions') }}</th>
                                            <th>{{ __('Net Salary') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Payment Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->payroll->take(12) as $payroll)
                                            <tr>
                                                <td>{{ $payroll->formatted_period }}</td>
                                                <td>${{ number_format($payroll->basic_earnings, 2) }}</td>
                                                <td>${{ number_format($payroll->overtime_earnings, 2) }}</td>
                                                <td>${{ number_format($payroll->total_deductions, 2) }}</td>
                                                <td><strong>${{ number_format($payroll->net_salary, 2) }}</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ 
                                                        $payroll->status === 'Paid' ? 'success' : 
                                                        ($payroll->status === 'Approved' ? 'warning' : 'secondary') 
                                                    }}">
                                                        {{ $payroll->status }}
                                                    </span>
                                                </td>
                                                <td>{{ $payroll->payment_date?->format('d/m/Y') ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">{{ __('No payroll records found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
