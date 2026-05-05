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

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please fix the following errors:') }}</h6>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">{{ __('Last Name') }} *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name_kh" class="form-label">{{ __('First Name (Khmer)') }}</label>
                                    <input type="text" class="form-control @error('first_name_kh') is-invalid @enderror" id="first_name_kh" name="first_name_kh" value="{{ old('first_name_kh') }}">
                                    @error('first_name_kh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name_kh" class="form-label">{{ __('Last Name (Khmer)') }}</label>
                                    <input type="text" class="form-control @error('last_name_kh') is-invalid @enderror" id="last_name_kh" name="last_name_kh" value="{{ old('last_name_kh') }}">
                                    @error('last_name_kh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">{{ __('Gender') }} *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }} *</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">{{ __('Nationality') }} *</label>
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', 'Cambodian') }}" required>
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_card_number" class="form-label">{{ __('ID Card Number') }}</label>
                                    <input type="text" class="form-control @error('id_card_number') is-invalid @enderror" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}">
                                    @error('id_card_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passport_number" class="form-label">{{ __('Passport Number') }}</label>
                                    <input type="text" class="form-control @error('passport_number') is-invalid @enderror" id="passport_number" name="passport_number" value="{{ old('passport_number') }}">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email') }} *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ __('Address') }} *</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address_kh" class="form-label">{{ __('Address (Khmer)') }}</label>
                                    <textarea class="form-control @error('address_kh') is-invalid @enderror" id="address_kh" name="address_kh" rows="2">{{ old('address_kh') }}</textarea>
                                    @error('address_kh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="province" class="form-label">{{ __('Province') }}</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">{{ __('Emergency Contact Name') }}</label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">{{ __('Emergency Contact Phone') }}</label>
                                    <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" id="hire_date" name="hire_date" value="{{ old('hire_date') }}" required>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">{{ __('Role') }} *</label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                        <option value="">{{ __('Select Role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">{{ __('Shift') }}</label>
                                    <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id">
                                        <option value="">{{ __('Select Shift') }}</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})</option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="employment_type" class="form-label">{{ __('Employment Type') }} *</label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="Full-time" {{ old('employment_type') == 'Full-time' ? 'selected' : '' }}>{{ __('Full-time') }}</option>
                                        <option value="Part-time" {{ old('employment_type') == 'Part-time' ? 'selected' : '' }}>{{ __('Part-time') }}</option>
                                        <option value="Contract" {{ old('employment_type') == 'Contract' ? 'selected' : '' }}>{{ __('Contract') }}</option>
                                        <option value="Intern" {{ old('employment_type') == 'Intern' ? 'selected' : '' }}>{{ __('Intern') }}</option>
                                    </select>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="base_salary" class="form-label">{{ __('Base Salary') }} ($) *</label>
                                    <input type="number" class="form-control @error('base_salary') is-invalid @enderror" id="base_salary" name="base_salary" 
                                           value="{{ old('base_salary') }}" step="0.01" min="0" required>
                                    @error('base_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_number" class="form-label">{{ __('Account Number') }}</label>
                                    <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}">
                                    @error('bank_account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bank_account_name" class="form-label">{{ __('Account Name') }}</label>
                                    <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}">
                                    @error('bank_account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
