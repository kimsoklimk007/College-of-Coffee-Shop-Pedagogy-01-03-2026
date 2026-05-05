@extends('admin.layouts.master')

@section('content')

<section class="container-fluid py-5 modern-business-section">
    <div class="row justify-content-center">
        <!-- Business Settings Form -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm">
                <div class="d-flex align-items-center mb-4">
                    @if(business_logo())
                        <img src="{{ business_logo() }}" alt="Logo" class="me-3" style="max-height: 60px; border-radius: 8px;">
                    @endif
                    <div>
                        <h3 class="text-dark fw-bold mb-1">{{ __('Business Settings') }}</h3>
                        <p class="text-muted mb-0">{{ __('Manage your business identity information') }}</p>
                    </div>
                </div>

                <form action="{{ route('business.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Business Name Khmer -->
                        <div class="col-md-6 mb-3">
                            <label for="business_name_kh" class="form-label fw-bold">{{ __('Business Name (Khmer)') }}</label>
                            <input type="text" name="business_name_kh" id="business_name_kh"
                                class="form-control @error('business_name_kh') is-invalid @enderror"
                                value="{{ old('business_name_kh', $setting->business_name_kh ?? '') }}"
                                placeholder="{{ __('Enter business name in Khmer...') }}" required>
                            @error('business_name_kh')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Business Name English -->
                        <div class="col-md-6 mb-3">
                            <label for="business_name_en" class="form-label fw-bold">{{ __('Business Name (English)') }}</label>
                            <input type="text" name="business_name_en" id="business_name_en"
                                class="form-control @error('business_name_en') is-invalid @enderror"
                                value="{{ old('business_name_en', $setting->business_name_en ?? '') }}"
                                placeholder="{{ __('Enter business name in English...') }}" required>
                            @error('business_name_en')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-bold">{{ __('Phone Number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $setting->phone ?? '') }}"
                                    placeholder="{{ __('Enter phone number...') }}">
                            </div>
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $setting->email ?? '') }}"
                                    placeholder="{{ __('Enter email address...') }}">
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">{{ __('Address') }}</label>
                        <textarea name="address" id="address" rows="2"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="{{ __('Enter business address...') }}">{{ old('address', $setting->address ?? '') }}</textarea>
                        @error('address')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Website -->
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label fw-bold">{{ __('Website') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                <input type="url" name="website" id="website"
                                    class="form-control @error('website') is-invalid @enderror"
                                    value="{{ old('website', $setting->website ?? '') }}"
                                    placeholder="https://example.com">
                            </div>
                            @error('website')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Tax Number -->
                        <div class="col-md-6 mb-3">
                            <label for="tax_number" class="form-label fw-bold">{{ __('Tax Number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                <input type="text" name="tax_number" id="tax_number"
                                    class="form-control @error('tax_number') is-invalid @enderror"
                                    value="{{ old('tax_number', $setting->tax_number ?? '') }}"
                                    placeholder="{{ __('Enter tax number...') }}">
                            </div>
                            @error('tax_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Currency Code -->
                        <div class="col-md-6 mb-3">
                            <label for="currency_code" class="form-label fw-bold">{{ __('Currency Code') }}</label>
                            <select name="currency_code" id="currency_code"
                                class="form-select @error('currency_code') is-invalid @enderror" required>
                                <option value="KHR" {{ (old('currency_code', $setting->currency_code ?? 'KHR') == 'KHR') ? 'selected' : '' }}>KHR (៛)</option>
                                <option value="USD" {{ (old('currency_code', $setting->currency_code ?? 'KHR') == 'USD') ? 'selected' : '' }}>USD ($)</option>
                            </select>
                            @error('currency_code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Exchange Rate -->
                        <div class="col-md-6 mb-3">
                            <label for="exchange_rate" class="form-label fw-bold">{{ __('Exchange Rate (1 USD to KHR)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-exchange-alt"></i></span>
                                <input type="number" name="exchange_rate" id="exchange_rate" step="0.01"
                                    class="form-control @error('exchange_rate') is-invalid @enderror"
                                    value="{{ old('exchange_rate', $setting->exchange_rate ?? 4100) }}"
                                    placeholder="4100" required>
                            </div>
                            @error('exchange_rate')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Receipt Footer Text -->
                    <div class="mb-3">
                        <label for="receipt_footer_text" class="form-label fw-bold">{{ __('Receipt Footer Text') }}</label>
                        <textarea name="receipt_footer_text" id="receipt_footer_text" rows="2"
                            class="form-control @error('receipt_footer_text') is-invalid @enderror"
                            placeholder="{{ __('Thank you message for receipts...') }}">{{ old('receipt_footer_text', $setting->receipt_footer_text ?? 'Thank you for your business!') }}</textarea>
                        @error('receipt_footer_text')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div class="mb-4">
                        <label for="logo" class="form-label fw-bold">{{ __('Business Logo') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                            <input type="file" name="logo" id="logo"
                                class="form-control @error('logo') is-invalid @enderror"
                                accept="image/*">
                        </div>
                        <small class="form-text text-muted">{{ __('Upload logo (JPG, PNG, GIF, max 2MB)') }}</small>
                        @error('logo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        @if($setting->logo)
                            <div class="mt-3">
                                <p class="mb-2">{{ __('Current Logo:') }}</p>
                                <img src="{{ business_logo() }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                <form action="{{ route('business.deleteLogo') }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Delete logo?') }}')">
                                        <i class="fas fa-trash"></i> {{ __('Remove') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary shadow-sm rounded-pill">
                            <i class="fas fa-save me-2"></i> {{ __('Save Business Settings') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="card p-4 mt-4 shadow-sm bg-light">
                <h5 class="fw-bold text-dark mb-3">{{ __('Preview - How it will appear on Receipts') }}</h5>
                <div class="receipt-preview p-3 bg-white rounded border">
                    <div class="text-center mb-3">
                        @if(business_logo())
                            <img src="{{ business_logo() }}" alt="Logo" class="mb-2" style="max-height: 50px;">
                        @endif
                        <h4 class="mb-0 fw-bold">{{ $setting->business_name_kh ?? 'កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត' }}</h4>
                        <p class="mb-0">{{ $setting->business_name_en ?? 'BTEC Cafe & Mini Mart' }}</p>
                        @if($setting->address)
                            <p class="mb-0 text-muted small">{{ $setting->address }}</p>
                        @endif
                        @if($setting->phone)
                            <p class="mb-0 text-muted small">{{ __('Tel:') }} {{ $setting->phone }}</p>
                        @endif
                    </div>
                    <hr>
                    <div class="text-center">
                        <p class="mb-0 small text-muted">{{ $setting->receipt_footer_text ?? 'Thank you for your business!' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
