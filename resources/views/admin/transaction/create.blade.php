@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Add Transaction') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaction.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Type') }} *</label>
                            <select name="type" class="form-select" id="typeSelect" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="income">{{ __('Income') }}</option>
                                <option value="expense">{{ __('Expense') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Category') }} *</label>
                            <select name="category" class="form-select" id="categorySelect" required>
                                <option value="">{{ __('Select Category') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Amount') }} *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Currency') }} *</label>
                            <select name="currency" class="form-select" id="currencySelect">
                                <option value="USD">USD</option>
                                <option value="KHR">KHR</option>
                            </select>
                        </div>
                        <div class="mb-3" id="exchangeRateField" style="display:none;">
                            <label class="form-label">{{ __('Exchange Rate') }}</label>
                            <input type="number" name="exchange_rate" class="form-control" step="1" value="4100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Date') }} *</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('transaction.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const incomeCategories = {
    "product_sales": "Product Sales",
    "delivery_fee": "Delivery Fee",
    "other_income": "Other Income"
};
const expenseCategories = {
    "purchase": "Purchase (Ingredients)",
    "salary": "Salary",
    "rent": "Rent",
    "utilities": "Utilities",
    "supplies": "Supplies",
    "maintenance": "Maintenance",
    "marketing": "Marketing",
    "other_expense": "Other Expense"
};

document.getElementById('typeSelect').addEventListener('change', function() {
    const categorySelect = document.getElementById('categorySelect');
    categorySelect.innerHTML = '<option value="">Select Category</option>';

    const categories = this.value === 'income' ? incomeCategories : expenseCategories;
    for (const [key, value] of Object.entries(categories)) {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = value;
        categorySelect.appendChild(option);
    }
});

document.getElementById('currencySelect').addEventListener('change', function() {
    document.getElementById('exchangeRateField').style.display = this.value === 'KHR' ? 'block' : 'none';
});
</script>
@endsection
