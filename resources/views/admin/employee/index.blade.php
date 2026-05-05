@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    {{-- Session Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Employee Management') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('adminDashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.settings') }}">{{ __('Employee Settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('All Employees') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('employee.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">{{ __('Search') }}</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="{{ __('Search by name, ID, email, phone') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status">{{ __('Status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    <option value="On Leave" {{ request('status') == 'On Leave' ? 'selected' : '' }}>{{ __('On Leave') }}</option>
                                    <option value="Resigned" {{ request('status') == 'Resigned' ? 'selected' : '' }}>{{ __('Resigned') }}</option>
                                    <option value="Terminated" {{ request('status') == 'Terminated' ? 'selected' : '' }}>{{ __('Terminated') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="role_id">{{ __('Role') }}</label>
                                <select class="form-select" id="role_id" name="role_id">
                                    <option value="">{{ __('All Roles') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="shift_id">{{ __('Shift') }}</label>
                                <select class="form-select" id="shift_id" name="shift_id">
                                    <option value="">{{ __('All Shifts') }}</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> {{ __('Search') }}
                                    </button>
                                    <a href="{{ route('employee.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo me-1"></i> {{ __('Reset') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Employee List') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employee.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Employee') }}
                        </a>
                        <button class="btn btn-outline-success" onclick="window.location.href='{{ route('employee.export') }}'">
                            <i class="fas fa-download me-1"></i> {{ __('Export') }}
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-upload me-1"></i> {{ __('Import') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Photo') }}</th>
                                    <th>{{ __('Employee ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Shift') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Hire Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            @if($employee->profile_photo)
                                                <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $employee->employee_id }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $employee->full_name }}</strong>
                                                @if($employee->full_name_kh)
                                                    <br><small class="text-muted">{{ $employee->full_name_kh }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($employee->role)
                                                <span class="badge bg-primary">{{ $employee->role->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('Not Assigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->shift)
                                                <span class="badge bg-info">{{ $employee->shift->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('Not Assigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <i class="fas fa-phone text-muted me-1"></i> {{ $employee->phone }}
                                                <br>
                                                <i class="fas fa-envelope text-muted me-1"></i> 
                                                <small>{{ $employee->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Active' => 'success',
                                                    'Inactive' => 'secondary',
                                                    'On Leave' => 'warning',
                                                    'Resigned' => 'danger',
                                                    'Terminated' => 'dark'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$employee->status] ?? 'secondary' }}">
                                                {{ $employee->status }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->hire_date->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('employee.show', $employee->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employee.edit', $employee->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($employee->has_qr_code)
                                                    <a href="{{ asset('storage/' . $employee->qr_code_path) }}" 
                                                       target="_blank" class="btn btn-sm btn-outline-success" title="{{ __('View QR') }}">
                                                        <i class="fas fa-qrcode"></i>
                                                    </a>
                                                @else
                                                    <form method="POST" action="{{ route('employee.generateQr', $employee->id) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                title="{{ __('Generate QR') }}">
                                                            <i class="fas fa-qrcode"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('employee.toggleStatus', $employee->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('{{ __('Are you sure you want to toggle employee status?') }}')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                            title="{{ __('Toggle Status') }}">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('employee.delete', $employee->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this employee?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('No employees found.') }}</p>
                                            <a href="{{ route('employee.create') }}" class="btn btn-primary">
                                                {{ __('Add First Employee') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            {{ __('Showing') }} {{ $employees->firstItem() }} {{ __('to') }} {{ $employees->lastItem() }} 
                            {{ __('of') }} {{ $employees->total() }} {{ __('employees') }}
                        </div>
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Import Employees') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Import employee data from Excel or CSV file.') }}</p>
                <form method="POST" action="{{ route('employee.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="import_file" class="form-label">{{ __('Select File') }}</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" 
                               accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="mb-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> {{ __('Download Template') }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
