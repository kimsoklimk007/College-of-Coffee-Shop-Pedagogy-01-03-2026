@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('Add New Employee') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('adminDashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.settings') }}">{{ __('Employee Settings') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('All Employees') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Add New Employee') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Employee Information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Personal Information') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">{{ __('First Name') }} *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }} *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name_kh" class="form-label">{{ __('First Name (Khmer)') }}</label>
                                    <input type="text" class="form-control" id="first_name_kh" name="first_name_kh">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name_kh" class="form-label">{{ __('Last Name (Khmer)') }}</label>
                                    <input type="text" class="form-control" id="last_name_kh" name="last_name_kh">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">{{ __('Gender') }} *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="Male">{{ __('Male') }}</option>
                                        <option value="Female">{{ __('Female') }}</option>
                                        <option value="Other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }} *</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">{{ __('Nationality') }} *</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="Cambodian" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_card_number" class="form-label">{{ __('ID Card Number') }}</label>
                                    <input type="text" class="form-control" id="id_card_number" name="id_card_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passport_number" class="form-label">{{ __('Passport Number') }}</label>
                                    <input type="text" class="form-control" id="passport_number" name="passport_number">
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
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email') }} *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ __('Address') }} *</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address_kh" class="form-label">{{ __('Address (Khmer)') }}</label>
                                    <textarea class="form-control" id="address_kh" name="address_kh" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="province" class="form-label">{{ __('Province') }}</label>
                                    <input type="text" class="form-control" id="province" name="province">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">{{ __('Emergency Contact Name') }}</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">{{ __('Emergency Contact Phone') }}</label>
                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone">
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Employment Information') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hire_date" class="form-label">{{ __('Hire Date') }} *</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">{{ __('Role') }} *</label>
                                    <select class="form-select" id="role_id" name="role_id" required>
                                        <option value="">{{ __('Select Role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">{{ __('Shift') }}</label>
                                    <select class="form-select" id="shift_id" name="shift_id">
                                        <option value="">{{ __('Select Shift') }}</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="employment_type" class="form-label">{{ __('Employment Type') }} *</label>
                                    <select class="form-select" id="employment_type" name="employment_type" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="Full-time">{{ __('Full-time') }}</option>
                                        <option value="Part-time">{{ __('Part-time') }}</option>
                                        <option value="Contract">{{ __('Contract') }}</option>
                                        <option value="Intern">{{ __('Intern') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="base_salary" class="form-label">{{ __('Base Salary') }} ($) *</label>
                                    <input type="number" class="form-control" id="base_salary" name="base_salary" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">{{ __('Bank Information') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">{{ __('Bank Name') }}</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_number" class="form-label">{{ __('Account Number') }}</label>
                                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_name" class="form-label">{{ __('Account Name') }}</label>
                                    <input type="text" class="form-control" id="bank_account_name" name="bank_account_name">
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
                                    <div id="profile_preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="id_card_photo" class="form-label">{{ __('ID Card Photo') }}</label>
                                    <input type="file" class="form-control" id="id_card_photo" name="id_card_photo" 
                                           accept="image/*" onchange="previewImage(event, 'id_card_preview')">
                                    <div id="id_card_preview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="contract_document" class="form-label">{{ __('Contract Document') }}</label>
                                    <input type="file" class="form-control" id="contract_document" name="contract_document" 
                                           accept=".pdf,.doc,.docx">
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
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('employee.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> {{ __('Save Employee') }}
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

// Set default hire date to today
document.getElementById('hire_date').valueAsDate = new Date();
</script>
@endsection
