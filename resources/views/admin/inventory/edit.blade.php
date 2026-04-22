@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Ingredient') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.update', $ingredient->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} *</label>
                            <input type="text" name="name" class="form-control" value="{{ $ingredient->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Category') }}</label>
                            <select name="category_id" class="form-select">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $ingredient->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Cost Price') }} *</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="{{ $ingredient->cost_price }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Current Stock') }} *</label>
                            <input type="number" name="stock_quantity" class="form-control" step="0.01" min="0" value="{{ $ingredient->stock_quantity }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Low Stock Threshold') }} *</label>
                            <input type="number" name="low_stock_threshold" class="form-control" step="0.01" min="0" value="{{ $ingredient->low_stock_threshold }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Unit') }} *</label>
                            <input type="text" name="unit" class="form-control" value="{{ $ingredient->unit }}" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
