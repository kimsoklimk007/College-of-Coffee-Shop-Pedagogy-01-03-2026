@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Inventory Management') }}</h4>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add Ingredient') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category_id" class="form-select">
                                    <option value="">{{ __('All Categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="stock_status" class="form-select">
                                    <option value="">{{ __('All Stock') }}</option>
                                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                    <th>{{ __('Cost Price') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ingredients as $ingredient)
                                <tr>
                                    <td>{{ $ingredient->id }}</td>
                                    <td>{{ $ingredient->name }}</td>
                                    <td>{{ $ingredient->category->name ?? '-' }}</td>
                                    <td>
                                        <span class="{{ $ingredient->isLowStock() ? 'text-danger fw-bold' : '' }}">
                                            {{ number_format($ingredient->stock_quantity, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ $ingredient->unit }}</td>
                                    <td>${{ number_format($ingredient->cost_price, 2) }}</td>
                                    <td>
                                        @if($ingredient->stock_quantity <= 0)
                                            <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                        @elseif($ingredient->isLowStock())
                                            <span class="badge bg-warning">{{ __('Low Stock') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('In Stock') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $ingredient->id }}">
                                            <i class="fas fa-plus-minus"></i>
                                        </button>
                                        <a href="{{ route('inventory.edit', $ingredient->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('inventory.history', $ingredient->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <form action="{{ route('inventory.destroy', $ingredient->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="adjustModal{{ $ingredient->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Adjust Stock') }}: {{ $ingredient->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('inventory.adjustStock', $ingredient->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Current Stock') }}</label>
                                                        <input type="text" class="form-control" value="{{ $ingredient->stock_quantity }} {{ $ingredient->unit }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Type') }}</label>
                                                        <select name="type" class="form-select" required>
                                                            <option value="add">{{ __('Add Stock') }}</option>
                                                            <option value="subtract">{{ __('Remove Stock') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Quantity') }}</label>
                                                        <input type="number" name="adjustment" class="form-control" step="0.01" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Note') }}</label>
                                                        <input type="text" name="note" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('No ingredients found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $ingredients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
