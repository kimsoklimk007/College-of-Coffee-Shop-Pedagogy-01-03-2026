@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <section class="container-fluid">
        <div class="row justify-content-center" style="min-height: 80vh;">
            <div class="col-md-10 col-lg-9">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">{{ __('Create New Category') }}</h4>
                        <p class="text-muted small mb-4">{{ __('Define category name and optional size templates with pricing') }}</p>

                        <form action="{{ route('category.store') }}" method="POST" id="categoryForm">
                            @csrf

                            <!-- Category Name -->
                            <div class="mb-4">
                                <label for="category" class="form-label fw-semibold">
                                    {{ __('English Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="category" id="category"
                                    value="{{ old('category') }}"
                                    class="form-control @error('category') is-invalid @enderror"
                                    placeholder="Cake, Juice, Coffee...">
                                @error('category')
                                    <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">{{ __('Category name must be unique across the system') }}</small>
                            </div>

                            <!-- Khmer Name -->
                            <div class="mb-4">
                                <label for="khmer_name" class="form-label fw-semibold">
                                    {{ __('Khmer Name') }}
                                </label>
                                <input type="text" name="khmer_name" id="khmer_name"
                                    value="{{ old('khmer_name') }}"
                                    class="form-control @error('khmer_name') is-invalid @enderror"
                                    placeholder="នំខេក, ទឹកផ្លែឈើ, កាហ្វេ...">
                                @error('khmer_name')
                                    <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">{{ __('Khmer name for display in Khmer language mode') }}</small>
                            </div>

                            <!-- Size Templates & Pricing -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label class="form-label fw-semibold mb-0">{{ __('Size Templates & Pricing') }}</label>
                                        <p class="text-muted small mb-0">{{ __('Define S, M, L sizes with prices') }}</p>
                                    </div>
                                    {{-- Exchange Rate Section Commented Out
                                    <div class="text-end">
                                        <small class="text-muted d-block">{{ __('Exchange Rate') }}</small>
                                        <div class="input-group input-group-sm" style="width: 180px;">
                                            <span class="input-group-text">៛</span>
                                            <input type="number" id="exchangeRate" class="form-control form-control-sm" value="4100" min="1" step="100">
                                            <span class="input-group-text">= $1</span>
                                        </div>
                                    </div>
                                    --}}
                                </div>

                                <div class="row g-3">
                                    <!-- Small Size -->
                                    <div class="col-md-4">
                                        <div class="card border-primary h-100">
                                            <div class="card-header bg-primary text-white py-2">
                                                <small class="fw-semibold"><i class="fas fa-coffee me-1"></i> {{ __('Small') }}</small>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="mb-2">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (KHR)') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">៛</span>
                                                        <input type="number" name="sizes[Small][price_khr]"
                                                            class="form-control @error('sizes.Small.price_khr') is-invalid @enderror"
                                                            value="{{ old('sizes.Small.price_khr') }}" min="0" step="100">
                                                    </div>
                                                    @error('sizes.Small.price_khr')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (USD)') }}</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" name="sizes[Small][price_usd]"
                                                            class="form-control @error('sizes.Small.price_usd') is-invalid @enderror"
                                                            value="{{ old('sizes.Small.price_usd') }}" min="0" step="0.01">
                                                    </div>
                                                    @error('sizes.Small.price_usd')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Medium Size -->
                                    <div class="col-md-4">
                                        <div class="card border-info h-100">
                                            <div class="card-header bg-info text-white py-2">
                                                <small class="fw-semibold"><i class="fas fa-coffee me-1"></i> {{ __('Medium') }}</small>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="mb-2">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (KHR)') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">៛</span>
                                                        <input type="number" name="sizes[Medium][price_khr]"
                                                            class="form-control @error('sizes.Medium.price_khr') is-invalid @enderror"
                                                            value="{{ old('sizes.Medium.price_khr') }}" min="0" step="100">
                                                    </div>
                                                    @error('sizes.Medium.price_khr')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (USD)') }}</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" name="sizes[Medium][price_usd]"
                                                            class="form-control @error('sizes.Medium.price_usd') is-invalid @enderror"
                                                            value="{{ old('sizes.Medium.price_usd') }}" min="0" step="0.01">
                                                    </div>
                                                    @error('sizes.Medium.price_usd')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Large Size -->
                                    <div class="col-md-4">
                                        <div class="card border-success h-100">
                                            <div class="card-header bg-success text-white py-2">
                                                <small class="fw-semibold"><i class="fas fa-coffee me-1"></i> {{ __('Large') }}</small>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="mb-2">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (KHR)') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">៛</span>
                                                        <input type="number" name="sizes[Large][price_khr]"
                                                            class="form-control @error('sizes.Large.price_khr') is-invalid @enderror"
                                                            value="{{ old('sizes.Large.price_khr') }}" min="0" step="100">
                                                    </div>
                                                    @error('sizes.Large.price_khr')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small text-muted mb-1">{{ __('Price (USD)') }}</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" name="sizes[Large][price_usd]"
                                                            class="form-control @error('sizes.Large.price_usd') is-invalid @enderror"
                                                            value="{{ old('sizes.Large.price_usd') }}" min="0" step="0.01">
                                                    </div>
                                                    @error('sizes.Large.price_usd')
                                                        <small class="invalid-feedback d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Custom Sizes -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">{{ __('Additional Custom Sizes') }}</label>
                                    <button type="button" class="btn btn-outline-success btn-sm" id="addCustomSize">
                                        <i class="fas fa-plus me-1"></i> {{ __('Add Custom Size') }}
                                    </button>
                                </div>
                                <p class="text-muted small">{{ __('Optional extra sizes') }}</p>

                                <div id="customSizesContainer">
                                    <!-- Custom sizes will be added here dynamically -->
                                </div>

                                <div id="noCustomSizes" class="text-center text-muted py-3">
                                    <small>{{ __('No custom sizes added') }}</small>
                                    <p class="small mb-0">{{ __('Click "Add Custom Size" to add more sizes') }}</p>
                                </div>
                            </div>

                            <!-- Category Date Information -->
                            <div class="card border-info mb-4">
                                <div class="card-header bg-info bg-opacity-10 text-info py-2">
                                    <small class="fw-semibold">{{ __('Category Date Information') }}</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">
                                                {{ __('កាលបរិច្ឆេទបង្កើត') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="start_date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                value="{{ old('start_date', date('Y-m-d')) }}">
                                            @error('start_date')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">{{ __('Date when this category was created') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">
                                                {{ __('កាលបរិច្ឆេទបញ្ចប់') }} <span class="text-muted">{{ __('-- Optional') }}</span>
                                            </label>
                                            <input type="date" name="end_date"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">{{ __('If set, category will be inactive after this date') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="alert alert-info d-flex align-items-start mb-4">
                                <i class="fas fa-info-circle mt-1 me-2"></i>
                                <div>
                                    <strong>{{ __('Tip:') }}</strong>
                                    {{ __('Size templates help standardize pricing across products. When you create a product in this category, these sizes will be suggested as defaults. You can always customize and individual product prices later.') }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('category.list') }}" class="btn btn-outline-secondary w-100 rounded-pill">
                                        {{ __('បោះបង់') }}
                                    </a>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                        {{ __('Create Category') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.container-fluid -->
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addCustomSizeBtn = document.getElementById('addCustomSize');
        const customSizesContainer = document.getElementById('customSizesContainer');
        const noCustomSizes = document.getElementById('noCustomSizes');
        {{-- const exchangeRateInput = document.getElementById('exchangeRate'); --}}
        let customSizeCount = 0;

        {{--
        // Currency conversion function
        function getExchangeRate() {
            return parseFloat(exchangeRateInput.value) || 4100;
        }

        function khrToUsd(khr) {
            const rate = getExchangeRate();
            return khr > 0 ? (khr / rate).toFixed(2) : '';
        }

        function usdToKhr(usd) {
            const rate = getExchangeRate();
            return usd > 0 ? Math.round(usd * rate) : '';
        }
        --}}

        // Setup currency conversion for a container
        function setupCurrencyConversion(container) {
            // KHR to USD conversion
            const khrInputs = container.querySelectorAll('input[name$="[price_khr]"]');
            khrInputs.forEach(khrInput => {
                khrInput.addEventListener('input', function() {
                    const khrValue = parseFloat(this.value) || 0;
                    {{-- const usdInput = this.closest('.card-body, .custom-size-item')?.querySelector('input[name$="[price_usd]"]');
                    if (usdInput) {
                        usdInput.value = khrToUsd(khrValue);
                    } --}}
                });
            });

            // USD to KHR conversion
            const usdInputs = container.querySelectorAll('input[name$="[price_usd]"]');
            usdInputs.forEach(usdInput => {
                usdInput.addEventListener('input', function() {
                    const usdValue = parseFloat(this.value) || 0;
                    {{-- const khrInput = this.closest('.card-body, .custom-size-item')?.querySelector('input[name$="[price_khr]"]');
                    if (khrInput) {
                        khrInput.value = usdToKhr(usdValue);
                    } --}}
                });
            });
        }

        {{--
        // Exchange rate change - recalculate all
        exchangeRateInput.addEventListener('change', function() {
            // Recalculate USD values based on current KHR values
            document.querySelectorAll('input[name$="[price_khr]"]').forEach(khrInput => {
                const khrValue = parseFloat(khrInput.value) || 0;
                const usdInput = khrInput.closest('.card-body, .custom-size-item')?.querySelector('input[name$="[price_usd]"]');
                if (usdInput && khrValue > 0) {
                    usdInput.value = khrToUsd(khrValue);
                }
            });
        });
        --}}

        addCustomSizeBtn.addEventListener('click', function() {
            customSizeCount++;
            noCustomSizes.style.display = 'none';

            const sizeHtml = `
                <div class="card border-secondary mb-3 custom-size-item" data-index="${customSizeCount}">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">{{ __('Size Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="custom_sizes[${customSizeCount}][size]"
                                    class="form-control form-control-sm" placeholder="e.g. XXL" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">{{ __('Price (KHR)') }}</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">៛</span>
                                    <input type="number" name="custom_sizes[${customSizeCount}][price_khr]"
                                        class="form-control price-khr" min="0" step="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">{{ __('Price (USD)') }}</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="custom_sizes[${customSizeCount}][price_usd]"
                                        class="form-control price-usd" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-custom-size">
                                    <i class="fas fa-trash me-1"></i> {{ __('Remove') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            customSizesContainer.insertAdjacentHTML('beforeend', sizeHtml);

            // Setup currency conversion for the new custom size
            const newItem = customSizesContainer.lastElementChild;
            setupCurrencyConversion(newItem);
        });

        // Remove custom size
        customSizesContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-custom-size')) {
                e.target.closest('.custom-size-item').remove();

                // Show "no custom sizes" message if empty
                if (customSizesContainer.children.length === 0) {
                    noCustomSizes.style.display = 'block';
                }
            }
        });

        // Setup initial currency conversion for preset sizes
        setupCurrencyConversion(document);
    });
</script>
@endsection
