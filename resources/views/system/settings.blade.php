@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-cogs"></i> {{ __('System Settings') }}</h3>
            <a href="{{ route('system.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- General Settings -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-sliders-h"></i> {{ __('General Settings') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('system.settings.update') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Default Currency') }} <span class="text-danger">*</span></label>
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="KHR" {{ $settings['currency'] == 'KHR' ? 'selected' : '' }}>KHR - Cambodian Riel</option>
                                <option value="THB" {{ $settings['currency'] == 'THB' ? 'selected' : '' }}>THB - Thai Baht</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Default Tax Rate (%)') }}</label>
                            <input type="number" name="default_tax_rate" class="form-control @error('default_tax_rate') is-invalid @enderror"
                                   value="{{ $settings['default_tax_rate'] }}" min="0" max="100" step="0.01">
                            @error('default_tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="tax_enabled" class="form-check-input" id="taxEnabled" value="1"
                                       {{ $settings['tax_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="taxEnabled">{{ __('Enable Tax Calculation') }}</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="multi_shop" class="form-check-input" id="multiShop" value="1"
                                       {{ $settings['multi_shop'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="multiShop">{{ __('Enable Multi-Shop Mode') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="registration_enabled" class="form-check-input" id="registrationEnabled" value="1"
                                   {{ $settings['registration_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="registrationEnabled">{{ __('Enable Public Registration') }}</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Save Settings') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-toggle-on"></i> {{ __('Feature Toggles') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ __('POS System') }}</h6>
                                <small class="text-muted">{{ __('Point of Sale functionality') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input feature-toggle" data-feature="pos"
                                       {{ $settings['features']['pos'] ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ __('Inventory Management') }}</h6>
                                <small class="text-muted">{{ __('Stock and inventory tracking') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input feature-toggle" data-feature="inventory"
                                       {{ $settings['features']['inventory'] ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ __('Employee Management') }}</h6>
                                <small class="text-muted">{{ __('Staff, payroll, attendance') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input feature-toggle" data-feature="employee"
                                       {{ $settings['features']['employee_management'] ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ __('Delivery System') }}</h6>
                                <small class="text-muted">{{ __('Delivery fees and locations') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input feature-toggle" data-feature="delivery"
                                       {{ $settings['features']['delivery'] ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h6 class="mb-1">{{ __('Reports & Analytics') }}</h6>
                                <small class="text-muted">{{ __('Sales, inventory reports') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input feature-toggle" data-feature="reports"
                                       {{ $settings['features']['reports'] ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ __('System Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold">{{ __('Laravel Version:') }}</td>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('PHP Version:') }}</td>
                                <td>{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('Environment:') }}</td>
                                <td>{{ config('app.env') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold">{{ __('Timezone:') }}</td>
                                <td>{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('Locale:') }}</td>
                                <td>{{ app()->getLocale() }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('Debug Mode:') }}</td>
                                <td>
                                    <span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">
                                        {{ config('app.debug') ? 'ON' : 'OFF' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.feature-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const feature = this.dataset.feature;
            const enabled = this.checked;

            fetch('{{ route('system.settings.toggleFeature') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ feature, enabled })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success toast or alert
                    console.log(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !enabled; // Revert toggle
            });
        });
    });
</script>
@endpush
