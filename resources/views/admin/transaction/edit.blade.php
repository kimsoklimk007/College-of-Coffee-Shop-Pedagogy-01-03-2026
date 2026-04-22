@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Transaction') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaction.update', $transaction->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Type') }} *</label>
                            <select name="type" class="form-select" id="typeSelect" required>
                                <option value="income" {{ $transaction->type === 'income' ? 'selected' : '' }}>{{ __('Income') }}</option>
                                <option value="expense" {{ $transaction->type === 'expense' ? 'selected' : '' }}>{{ __('Expense') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Category') }} *</label>
                            <select name="category" class="form-select" id="categorySelect" required>
                                <option value="{{ $transaction->category }}">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Amount (USD)') }} *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $transaction->amount }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Currency') }} *</label>
                            <select name="currency" class="form-select" id="currencySelect">
                                <option value="USD">USD</option>
                                <option value="KHR">KHR</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Date') }} *</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ $transaction->transaction_date }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ $transaction->description }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('transaction.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
