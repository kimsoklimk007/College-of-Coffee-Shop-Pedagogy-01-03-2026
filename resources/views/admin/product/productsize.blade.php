@extends('admin.layouts.master')

@section('content')
<section class="container-fluid py-4">
    <h4 class="mb-3">Add Sizes & Prices for <strong>{{ $product->name }}</strong></h4>

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
                    <small class="text-muted">
                        {{ __('Auto-convert between KHR (៛) and USD ($)') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('prodsizestore', $product->id) }}" method="POST">
        @csrf
        <div id="sizePriceContainer">
            <div class="row mb-3 size-price-row align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Size</label>
                    <select name="sizes[]" class="form-select" required>
                        <option value="">Select Size</option>
                        <option value="S">S (Small)</option>
                        <option value="M">M (Medium)</option>
                        <option value="L">L (Large)</option>
                        <option value="XXL">XXL</option>
                        <option value="XXX">XXX</option>
                        <option value="LLX">LLX</option>
                        <option value="XLL">XLL</option>
                        <option value="ALL">ALL</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Price (KHR) ៛</label>
                    <input type="number" name="prices_khr[]" class="form-control price-khr" step="100" placeholder="Price in Riel" data-type="khr">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Price (USD) $</label>
                    <input type="number" name="prices_usd[]" class="form-control price-usd" step="0.01" placeholder="Price in Dollar" data-type="usd">
                </div>
                <div class="col-md-2 d-flex align-items-center gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm convert-btn" title="Convert KHR to USD">KHR→$</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm convert-btn-reverse" title="Convert USD to KHR">$→KHR</button>
                </div>
                <div class="col-md-2 mt-4">
                    <button type="button" class="btn btn-danger w-100 remove-size-price">Remove</button>
                </div>
            </div>
        </div>

        <button type="button" id="addSizePrice" class="btn btn-primary btn-sm mt-2">+ Add Size & Price</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Save Sizes</button>
            <a href="{{ route('product.prodlist') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </form>
</section>

@endsection

@section('scripts')
<script>
    const sizeOptions = `
        <option value="">Select Size</option>
        <option value="S">S (Small)</option>
        <option value="M">M (Medium)</option>
        <option value="L">L (Large)</option>
        <option value="XXL">XXL</option>
        <option value="XXX">XXX</option>
        <option value="LLX">LLX</option>
        <option value="XLL">XLL</option>
        <option value="ALL">ALL</option>
    `;

    document.getElementById('addSizePrice').addEventListener('click', function () {
        const container = document.getElementById('sizePriceContainer');
        const row = document.createElement('div');
        row.classList.add('row', 'mb-3', 'size-price-row', 'align-items-end');

        row.innerHTML = `
            <div class="col-md-2">
                <label class="form-label fw-bold">Size</label>
                <select name="sizes[]" class="form-select" required>
                    ${sizeOptions}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Price (KHR) ៛</label>
                <input type="number" name="prices_khr[]" class="form-control price-khr" step="100" placeholder="Price in Riel" data-type="khr">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Price (USD) $</label>
                <input type="number" name="prices_usd[]" class="form-control price-usd" step="0.01" placeholder="Price in Dollar" data-type="usd">
            </div>
            <div class="col-md-2 d-flex align-items-center gap-2 mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm convert-btn" title="Convert KHR to USD">KHR→$</button>
                <button type="button" class="btn btn-outline-secondary btn-sm convert-btn-reverse" title="Convert USD to KHR">$→KHR</button>
            </div>
            <div class="col-md-2 mt-4">
                <button type="button" class="btn btn-danger w-100 remove-size-price">Remove</button>
            </div>
        `;
        container.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-size-price')) {
            e.target.closest('.size-price-row').remove();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-khr')) {
            const row = e.target.closest('.size-price-row');
            const khrInput = row.querySelector('.price-khr');
            const usdInput = row.querySelector('.price-usd');
            const rate = parseFloat(document.getElementById('exchangeRate').value) || 4100;
            
            if (e.target.value && !isNaN(e.target.value)) {
                usdInput.value = (parseFloat(e.target.value) / rate).toFixed(2);
            }
        }
        
        if (e.target.classList.contains('price-usd')) {
            const row = e.target.closest('.size-price-row');
            const khrInput = row.querySelector('.price-khr');
            const usdInput = row.querySelector('.price-usd');
            const rate = parseFloat(document.getElementById('exchangeRate').value) || 4100;
            
            if (e.target.value && !isNaN(e.target.value)) {
                khrInput.value = Math.round(parseFloat(e.target.value) * rate);
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('convert-btn')) {
            const row = e.target.closest('.size-price-row');
            const khrInput = row.querySelector('.price-khr');
            const usdInput = row.querySelector('.price-usd');
            const rate = parseFloat(document.getElementById('exchangeRate').value) || 4100;
            
            if (khrInput.value && !isNaN(khrInput.value)) {
                usdInput.value = (parseFloat(khrInput.value) / rate).toFixed(2);
            }
        }
        
        if (e.target.classList.contains('convert-btn-reverse')) {
            const row = e.target.closest('.size-price-row');
            const khrInput = row.querySelector('.price-khr');
            const usdInput = row.querySelector('.price-usd');
            const rate = parseFloat(document.getElementById('exchangeRate').value) || 4100;
            
            if (usdInput.value && !isNaN(usdInput.value)) {
                khrInput.value = Math.round(parseFloat(usdInput.value) * rate);
            }
        }
    });
</script>
@endsection

