@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <section class="container-fluid py-4">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-lg-11">
                <div class="card border-0 shadow-sm p-4">
                    <h2 class="fw-bold mb-4">{{ __('Update Product') }}</h2>

                    @php
                        $product = $products->first();
                        $existingSizes = $products->pluck('size', 'size')->toArray();
                        $sizeData = [];
                        foreach ($products as $item) {
                            $sizeData[$item->size] = [
                                'price_khr' => $item->price_khr ?? $item->price,
                                'price_usd' => $item->price_usd ?? 0,
                                'currency' => $item->currency ?? 'KHR'
                            ];
                        }
                    @endphp

                    <form action="{{ route('product.produpdate') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <!-- Product Information Section -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <input type="hidden" name="oldImage" value="{{ $product->image }}">
                                <input type="hidden" name="productId" value="{{ $product->id }}">
                                <img id="output" src="{{ asset('productImages/' . $product->image) }}" class="img-thumbnail rounded mb-3" style="width: 100%; max-height: 250px; object-fit: cover;">
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" onchange="loadFile(event)">
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">{{ __('Product Name') }}</label>
                                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                            class="form-control @error('name') is-invalid @enderror" placeholder="Enter product name">
                                        @error('name')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">{{ __('Khmer Name') }}</label>
                                        <input type="text" name="name_kh" value="{{ old('name_kh', $product->name_kh) }}"
                                            class="form-control @error('name_kh') is-invalid @enderror" placeholder="បញ្ចូលឈ្មោះខ្មែរ">
                                        @error('name_kh')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">{{ __('Category') }}</label>
                                        <select name="category_name" class="form-select @error('category_name') is-invalid @enderror">
                                            @foreach ($categories as $item)
                                                <option value="{{ $item->id }}" @selected(old('category_name', $product->category_id) == $item->id)>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_name')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">{{ __('Stock Quantity') }}</label>
                                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ old('stock', $product->qty) }}" placeholder="Enter stock quantity" min="0">
                                        @error('stock')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">{{ __('Description') }}</label>
                                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exchange Rate Setting -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold mb-0">
                                            <i class="bi bi-currency-exchange"></i> {{ __('Exchange Rate') }}
                                        </label>
                                        <small class="text-muted d-block">1 USD = ? KHR</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="number" id="exchangeRate" class="form-control" value="4100" min="1" step="1">
                                            <span class="input-group-text">KHR</span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <small class="text-muted" id="exchangeRateSource">
                                            {{ __('Auto-convert between KHR (៛) and USD ($)') }}
                                        </small>
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="refreshRateBtn" onclick="fetchLatestExchangeRate()">
                                            <i class="bi bi-arrow-clockwise"></i> {{ __('Refresh Rate') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sizes & Prices Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-tags"></i> {{ __('Sizes & Prices') }}
                                </h5>
                                <span class="text-muted small" id="categorySizesInfo">
                                    <i class="bi bi-info-circle"></i> {{ __('Category sizes loaded') }}
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="sizesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 12%;">{{ __('Size') }}</th>
                                            <th style="width: 28%;">{{ __('Price (KHR)') }} ៛</th>
                                            <th style="width: 28%;">{{ __('Price (USD)') }} $</th>
                                            <th style="width: 20%;">{{ __('Currency Display') }}</th>
                                            <th style="width: 12%;">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sizesTableBody">
                                        <!-- Sizes will be loaded dynamically based on category -->
                                    </tbody>
                                </table>
                            </div>

                            @error('sizes')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save"></i> {{ __('Update Product') }}
                                </button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('product.prodlist') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-left"></i> {{ __('Back') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    // Image preview
    function loadFile(event) {
        document.getElementById('output').src = URL.createObjectURL(event.target.files[0]);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const exchangeRateInput = document.getElementById('exchangeRate');
        const exchangeRateSource = document.getElementById('exchangeRateSource');
        const categorySelect = document.getElementById('category_name');
        const sizesTableBody = document.getElementById('sizesTableBody');
        const categorySizesInfo = document.getElementById('categorySizesInfo');

        // Pre-existing product data from PHP
        const productCategoryId = {{ $product->category_id }};
        const existingSizesData = @json($sizeData);

        // Fetch latest exchange rate from API
        function fetchLatestExchangeRate() {
            const refreshBtn = document.getElementById('refreshRateBtn');
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ __("Loading...") }}';

            fetch('{{ route("currency.exchangeRate") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        exchangeRateInput.value = data.rate;
                        const sourceText = data.source === 'default' || data.source === 'default_fallback' 
                            ? '{{ __("Using default Cambodia market rate") }}' 
                            : `{{ __("Rate from") }}: ${data.source} (${data.cached ? '{{ __("cached") }}' : '{{ __("live") }}'})`;
                        exchangeRateSource.innerHTML = sourceText;

                        // Show success notification
                        showNotification('{{ __("Exchange rate updated") }}: 1 USD = ' + data.rate + ' KHR', 'success');
                    } else {
                        showNotification('{{ __("Failed to fetch exchange rate") }}', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching exchange rate:', error);
                    showNotification('{{ __("Error fetching exchange rate. Using default rate.") }}', 'warning');
                })
                .finally(() => {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> {{ __("Refresh Rate") }}';
                });
        }

        // Make fetchLatestExchangeRate globally accessible
        window.fetchLatestExchangeRate = fetchLatestExchangeRate;

        // Show notification helper
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Size labels mapping
        const sizeLabels = {
            'S': 'Small',
            'M': 'Medium',
            'L': 'Large',
            'XXL': 'Extra Extra Large',
            'XXX': 'Triple Extra Large',
            'LLX': 'Double Large XL',
            'XLL': 'Extra Large Large',
            'ALL': 'All Sizes'
        };

        // Load category sizes via AJAX
        function loadCategorySizes(categoryId, preSelectedSizes = {}) {
            if (!categoryId) {
                sizesTableBody.innerHTML = '';
                categorySizesInfo.innerHTML = '<i class="bi bi-info-circle"></i> {{ __("Select a category to load its sizes") }}';
                return;
            }

            categorySizesInfo.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ __("Loading sizes...") }}';

            fetch(`{{ url('admin/product/getCategorySizes') }}/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.sizes.length > 0) {
                            renderSizes(data.sizes, preSelectedSizes);
                            categorySizesInfo.innerHTML = `<i class="bi bi-check-circle text-success"></i> {{ __("Loaded") }} ${data.sizes.length} {{ __("size(s) from category") }}`;
                        } else {
                            // Fallback to default sizes if category has no sizes defined
                            renderDefaultSizes(preSelectedSizes);
                            categorySizesInfo.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i> {{ __("No sizes defined for this category. Using default sizes.") }}';
                        }
                    } else {
                        renderDefaultSizes(preSelectedSizes);
                        categorySizesInfo.innerHTML = '<i class="bi bi-exclamation-circle text-danger"></i> {{ __("Error loading sizes. Using defaults.") }}';
                    }
                })
                .catch(error => {
                    console.error('Error loading category sizes:', error);
                    renderDefaultSizes(preSelectedSizes);
                    categorySizesInfo.innerHTML = '<i class="bi bi-exclamation-circle text-danger"></i> {{ __("Error loading sizes. Using defaults.") }}';
                });
        }

        // Render sizes from category with pre-selected values
        function renderSizes(sizes, preSelectedSizes = {}) {
            let html = '';
            sizes.forEach((size, index) => {
                const label = size.label || sizeLabels[size.size] || size.size;

                // Check if this size exists for the product
                const hasExisting = preSelectedSizes[size.size] !== undefined;
                const existingKhr = hasExisting ? preSelectedSizes[size.size].price_khr : '';
                const existingUsd = hasExisting ? preSelectedSizes[size.size].price_usd : '';
                const existingCurrency = hasExisting ? preSelectedSizes[size.size].currency : 'KHR';

                // Use existing values if available, otherwise use category defaults
                const khrValue = existingKhr || size.price_khr || '';
                const usdValue = existingUsd || size.price_usd || '';
                const currencyValue = existingCurrency || 'KHR';
                const isChecked = hasExisting ? 'checked' : '';
                const statusClass = hasExisting ? 'bg-success' : 'bg-secondary';
                const statusText = hasExisting ? '{{ __("Has Price") }}' : '{{ __("No Price") }}';

                html += `
                    <tr class="size-row" data-size="${size.size}">
                        <td class="align-middle">
                            <div class="form-check">
                                <input class="form-check-input size-checkbox" type="checkbox"
                                       name="sizes[]" value="${size.size}"
                                       id="size_${size.size}" ${isChecked}>
                                <label class="form-check-label fw-bold" for="size_${size.size}">
                                    ${size.size}
                                </label>
                                <small class="text-muted d-block">${label}</small>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text bg-white">៛</span>
                                <input type="number"
                                       name="prices_khr[]"
                                       class="form-control price-khr"
                                       placeholder="0"
                                       step="100"
                                       min="0"
                                       value="${khrValue}"
                                       data-size="${size.size}"
                                       ${!hasExisting ? 'disabled' : ''}>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text bg-white">$</span>
                                <input type="number"
                                       name="prices_usd[]"
                                       class="form-control price-usd"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       value="${usdValue}"
                                       data-size="${size.size}"
                                       ${!hasExisting ? 'disabled' : ''}>
                            </div>
                        </td>
                        <td class="align-middle">
                            <select name="currencies[]" class="form-select form-select-sm currency-select" data-size="${size.size}" ${!hasExisting ? 'disabled' : ''}>
                                <option value="KHR" ${currencyValue === 'KHR' ? 'selected' : ''}>៛ {{ __('Khmer Riel') }}</option>
                                <option value="USD" ${currencyValue === 'USD' ? 'selected' : ''}>$ {{ __('US Dollar') }}</option>
                            </select>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge ${statusClass} status-badge" id="status_${size.size}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>
                `;
            });
            sizesTableBody.innerHTML = html;
            attachEventListeners();
        }

        // Render default sizes when category has no sizes defined
        function renderDefaultSizes(preSelectedSizes = {}) {
            const defaultSizes = ['S', 'M', 'L', 'XXL', 'XXX', 'LLX', 'XLL', 'ALL'];
            let html = '';
            defaultSizes.forEach((size, index) => {
                const label = sizeLabels[size] || size;

                const hasExisting = preSelectedSizes[size] !== undefined;
                const khrValue = hasExisting ? preSelectedSizes[size].price_khr : '';
                const usdValue = hasExisting ? preSelectedSizes[size].price_usd : '';
                const currencyValue = hasExisting ? preSelectedSizes[size].currency : 'KHR';
                const isChecked = hasExisting ? 'checked' : '';
                const statusClass = hasExisting ? 'bg-success' : 'bg-secondary';
                const statusText = hasExisting ? '{{ __("Has Price") }}' : '{{ __("No Price") }}';

                html += `
                    <tr class="size-row" data-size="${size}">
                        <td class="align-middle">
                            <div class="form-check">
                                <input class="form-check-input size-checkbox" type="checkbox"
                                       name="sizes[]" value="${size}"
                                       id="size_${size}" ${isChecked}>
                                <label class="form-check-label fw-bold" for="size_${size}">
                                    ${size}
                                </label>
                                <small class="text-muted d-block">${label}</small>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text bg-white">៛</span>
                                <input type="number"
                                       name="prices_khr[]"
                                       class="form-control price-khr"
                                       placeholder="0"
                                       step="100"
                                       min="0"
                                       value="${khrValue}"
                                       data-size="${size}"
                                       ${!hasExisting ? 'disabled' : ''}>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text bg-white">$</span>
                                <input type="number"
                                       name="prices_usd[]"
                                       class="form-control price-usd"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       value="${usdValue}"
                                       data-size="${size}"
                                       ${!hasExisting ? 'disabled' : ''}>
                            </div>
                        </td>
                        <td class="align-middle">
                            <select name="currencies[]" class="form-select form-select-sm currency-select" data-size="${size}" ${!hasExisting ? 'disabled' : ''}>
                                <option value="KHR" ${currencyValue === 'KHR' ? 'selected' : ''}>៛ {{ __('Khmer Riel') }}</option>
                                <option value="USD" ${currencyValue === 'USD' ? 'selected' : ''}>$ {{ __('US Dollar') }}</option>
                            </select>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge ${statusClass} status-badge" id="status_${size}">
                                ${statusText}
                            </span>
                        </td>
                    </tr>
                `;
            });
            sizesTableBody.innerHTML = html;
            attachEventListeners();
        }

        // Attach event listeners to dynamic elements
        function attachEventListeners() {
            // KHR to USD conversion
            document.querySelectorAll('.price-khr').forEach(input => {
                input.addEventListener('input', function() {
                    const size = this.dataset.size;
                    const usdInput = document.querySelector(`.price-usd[data-size="${size}"]`);
                    const rate = parseFloat(exchangeRateInput.value) || 4100;
                    const khrValue = parseFloat(this.value);

                    if (khrValue && !isNaN(khrValue)) {
                        usdInput.value = (khrValue / rate).toFixed(2);
                    } else {
                        usdInput.value = '';
                    }

                    updateStatusBadge(size);
                });
            });

            // USD to KHR conversion
            document.querySelectorAll('.price-usd').forEach(input => {
                input.addEventListener('input', function() {
                    const size = this.dataset.size;
                    const khrInput = document.querySelector(`.price-khr[data-size="${size}"]`);
                    const rate = parseFloat(exchangeRateInput.value) || 4100;
                    const usdValue = parseFloat(this.value);

                    if (usdValue && !isNaN(usdValue)) {
                        khrInput.value = Math.round(usdValue * rate);
                    } else {
                        khrInput.value = '';
                    }

                    updateStatusBadge(size);
                });
            });

            // Enable/disable price inputs based on checkbox
            document.querySelectorAll('.size-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const size = this.value;
                    const khrInput = document.querySelector(`.price-khr[data-size="${size}"]`);
                    const usdInput = document.querySelector(`.price-usd[data-size="${size}"]`);
                    const currencySelect = document.querySelector(`.currency-select[data-size="${size}"]`);

                    if (this.checked) {
                        khrInput.removeAttribute('disabled');
                        usdInput.removeAttribute('disabled');
                        currencySelect.removeAttribute('disabled');
                        khrInput.focus();
                    } else {
                        khrInput.setAttribute('disabled', 'disabled');
                        usdInput.setAttribute('disabled', 'disabled');
                        currencySelect.setAttribute('disabled', 'disabled');
                        khrInput.value = '';
                        usdInput.value = '';
                        currencySelect.value = 'KHR';
                    }

                    updateStatusBadge(size);
                });
            });
        }

        // Update status badge based on price input
        function updateStatusBadge(size) {
            const khrInput = document.querySelector(`.price-khr[data-size="${size}"]`);
            const usdInput = document.querySelector(`.price-usd[data-size="${size}"]`);
            const statusBadge = document.getElementById(`status_${size}`);
            const checkbox = document.getElementById(`size_${size}`);

            if (!khrInput || !usdInput || !statusBadge || !checkbox) return;

            const khrValue = parseFloat(khrInput.value);
            const usdValue = parseFloat(usdInput.value);

            if (checkbox.checked && ((khrValue && khrValue > 0) || (usdValue && usdValue > 0))) {
                statusBadge.textContent = '{{ __("Has Price") }}';
                statusBadge.classList.remove('bg-secondary', 'bg-warning');
                statusBadge.classList.add('bg-success');
            } else if (checkbox.checked) {
                statusBadge.textContent = '{{ __("Selected") }}';
                statusBadge.classList.remove('bg-secondary', 'bg-success');
                statusBadge.classList.add('bg-warning');
            } else {
                statusBadge.textContent = '{{ __("No Price") }}';
                statusBadge.classList.remove('bg-success', 'bg-warning');
                statusBadge.classList.add('bg-secondary');
            }
        }

        // Category change handler
        categorySelect.addEventListener('change', function() {
            // When category changes, reload sizes but don't pre-select existing ones
            loadCategorySizes(this.value, {});
        });

        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const checkedSizes = document.querySelectorAll('.size-checkbox:checked');
            let hasValidPrice = false;

            checkedSizes.forEach(checkbox => {
                const size = checkbox.value;
                const khrInput = document.querySelector(`.price-khr[data-size="${size}"]`);
                const usdInput = document.querySelector(`.price-usd[data-size="${size}"]`);

                if (khrInput && usdInput && (khrInput.value || usdInput.value)) {
                    hasValidPrice = true;
                }
            });

            if (checkedSizes.length === 0) {
                alert('{{ __("Please select at least one size.") }}');
                e.preventDefault();
                return false;
            }

            if (!hasValidPrice) {
                alert('{{ __("Please enter at least one price (KHR or USD) for the selected sizes.") }}');
                e.preventDefault();
                return false;
            }

            // Enable all inputs before submit to ensure data is sent
            document.querySelectorAll('.price-khr, .price-usd, .currency-select').forEach(input => {
                input.removeAttribute('disabled');
            });
        });

        // Initialize with product's current category and sizes, and fetch exchange rate
        loadCategorySizes(productCategoryId, existingSizesData);
        fetchLatestExchangeRate();
    });
</script>
@endsection
