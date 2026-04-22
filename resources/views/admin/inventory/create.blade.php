@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Add Ingredient') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Category') }}</label>
                            <select name="category_id" class="form-select">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Cost Price') }} *</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Initial Stock') }} *</label>
                            <input type="number" name="stock_quantity" class="form-control" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Low Stock Threshold') }} *</label>
                            <input type="number" name="low_stock_threshold" class="form-control" step="0.01" min="0" value="10" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Unit') }} *</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg, liter, pcs" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
