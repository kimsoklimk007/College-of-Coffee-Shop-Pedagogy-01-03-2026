@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Edit Employee') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('adminDashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.settings') }}">{{ __('Employee Settings') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('All Employees') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Edit Employee') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Edit Employee Information') }} - {{ $employee->full_name }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Personal Information') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">{{ __('First Name') }} *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="{{ $employee->first_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }} *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="{{ $employee->last_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name_kh" class="form-label">{{ __('First Name (Khmer)') }}</label>
                                    <input type="text" class="form-control" id="first_name_kh" name="first_name_kh" 
                                           value="{{ $employee->first_name_kh }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name_kh" class="form-label">{{ __('Last Name (Khmer)') }}</label>
                                    <input type="text" class="form-control" id="last_name_kh" name="last_name_kh" 
                                           value="{{ $employee->last_name_kh }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">{{ __('Gender') }} *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="Male" {{ $employee->gender === 'Male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                        <option value="Female" {{ $employee->gender === 'Female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="Other" {{ $employee->gender === 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }} *</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="{{ $employee->date_of_birth->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">{{ __('Nationality') }} *</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" 
                                           value="{{ $employee->nationality }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_card_number" class="form-label">{{ __('ID Card Number') }}</label>
                                    <input type="text" class="form-control" id="id_card_number" name="id_card_number" 
                                           value="{{ $employee->id_card_number }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passport_number" class="form-label">{{ __('Passport Number') }}</label>
                                    <input type="text" class="form-control" id="passport_number" name="passport_number" 
                                           value="{{ $employee->passport_number }}">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Contact Information') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('Phone Number') }} *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="{{ $employee->phone }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email') }} *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ $employee->email }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ __('Address') }} *</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required>{{ $employee->address }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address_kh" class="form-label">{{ __('Address (Khmer)') }}</label>
                                    <textarea class="form-control" id="address_kh" name="address_kh" rows="2">{{ $employee->address_kh }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ $employee->city }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="province" class="form-label">{{ __('Province') }}</label>
                                    <input type="text" class="form-control" id="province" name="province" value="{{ $employee->province }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">{{ __('Emergency Contact Name') }}</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                           value="{{ $employee->emergency_contact_name }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">{{ __('Emergency Contact Phone') }}</label>
                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" 
                                           value="{{ $employee->emergency_contact_phone }}">
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Employment Information') }}</h6>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="hire_date" class="form-label">{{ __('Hire Date') }} *</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                           value="{{ $employee->hire_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }} *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        <option value="Active" {{ $employee->status === 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="Inactive" {{ $employee->status === 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        <option value="On Leave" {{ $employee->status === 'On Leave' ? 'selected' : '' }}>{{ __('On Leave') }}</option>
                                        <option value="Resigned" {{ $employee->status === 'Resigned' ? 'selected' : '' }}>{{ __('Resigned') }}</option>
                                        <option value="Terminated" {{ $employee->status === 'Terminated' ? 'selected' : '' }}>{{ __('Terminated') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">{{ __('Role') }} *</label>
                                    <select class="form-select" id="role_id" name="role_id" required>
                                        <option value="">{{ __('Select Role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $employee->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">{{ __('Shift') }}</label>
                                    <select class="form-select" id="shift_id" name="shift_id">
                                        <option value="">{{ __('Select Shift') }}</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $employee->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="employment_type" class="form-label">{{ __('Employment Type') }} *</label>
                                    <select class="form-select" id="employment_type" name="employment_type" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="Full-time" {{ $employee->employment_type === 'Full-time' ? 'selected' : '' }}>{{ __('Full-time') }}</option>
                                        <option value="Part-time" {{ $employee->employment_type === 'Part-time' ? 'selected' : '' }}>{{ __('Part-time') }}</option>
                                        <option value="Contract" {{ $employee->employment_type === 'Contract' ? 'selected' : '' }}>{{ __('Contract') }}</option>
                                        <option value="Intern" {{ $employee->employment_type === 'Intern' ? 'selected' : '' }}>{{ __('Intern') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="base_salary" class="form-label">{{ __('Base Salary') }} ($) *</label>
                                    <input type="number" class="form-control" id="base_salary" name="base_salary" 
                                           step="0.01" min="0" value="{{ $employee->base_salary }}" required>
                                </div>
                            </div>
                            @if(in_array($employee->status, ['Resigned', 'Terminated']))
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ $employee->end_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Bank Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Bank Information') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">{{ __('Bank Name') }}</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                           value="{{ $employee->bank_name }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_number" class="form-label">{{ __('Account Number') }}</label>
                                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" 
                                           value="{{ $employee->bank_account_number }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_name" class="form-label">{{ __('Account Name') }}</label>
                                    <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" 
                                           value="{{ $employee->bank_account_name }}">
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Documents') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">{{ __('Profile Photo') }}</label>
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" 
                                           accept="image/*" onchange="previewImage(event, 'profile_preview')">
                                    @if($employee->profile_photo)
                                        <div class="mt-2">
                                            <small class="text-muted">{{ __('Current:') }}</small><br>
                                            <img src="{{ asset('storage/' . $employee->profile_photo) }}" 
                                                 class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                        </div>
                                    @endif
                                    <div id="profile_preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_card_photo" class="form-label">{{ __('ID Card Photo') }}</label>
                                    <input type="file" class="form-control" id="id_card_photo" name="id_card_photo" 
                                           accept="image/*" onchange="previewImage(event, 'id_card_preview')">
                                    @if($employee->id_card_photo)
                                        <div class="mt-2">
                                            <small class="text-muted">{{ __('Current:') }}</small><br>
                                            <img src="{{ asset('storage/' . $employee->id_card_photo) }}" 
                                                 class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                        </div>
                                    @endif
                                    <div id="id_card_preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="contract_document" class="form-label">{{ __('Contract Document') }}</label>
                                    <input type="file" class="form-control" id="contract_document" name="contract_document" 
                                           accept=".pdf,.doc,.docx">
                                    @if($employee->contract_document)
                                        <div class="mt-2">
                                            <small class="text-muted">{{ __('Current:') }}</small><br>
                                            <a href="{{ asset('storage/' . $employee->contract_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> {{ __('View Document') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Additional Information') }}</h6>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $employee->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> {{ __('Update Employee') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event, previewId) {
    const file = event.target.files[0];
    const preview = document.getElementById(previewId);
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">';
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

// Show/hide end date based on status
document.getElementById('status').addEventListener('change', function() {
    const endDateField = document.getElementById('end_date');
    const endDateContainer = endDateField.closest('.col-md-3');
    
    if (this.value === 'Resigned' || this.value === 'Terminated') {
        endDateContainer.style.display = 'block';
        if (!endDateField.value) {
            endDateField.value = new Date().toISOString().split('T')[0];
        }
    } else {
        endDateContainer.style.display = 'none';
        endDateField.value = '';
    }
});

// Trigger change event on page load
document.getElementById('status').dispatchEvent(new Event('change'));
</script>
@endsection
