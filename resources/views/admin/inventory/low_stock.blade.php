@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ __('Low Stock Alert') }}</h4>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Inventory') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($ingredients->isNotEmpty())
                        <div class="alert alert-warning">
                            <strong>{{ $ingredients->count() }}</strong> {{ __('items are running low on stock') }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Current Stock') }}</th>
                                        <th>{{ __('Threshold') }}</th>
                                        <th>{{ __('Unit') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ingredients as $ingredient)
                                    <tr class="{{ $ingredient->stock_quantity <= 0 ? 'table-danger' : 'table-warning' }}">
                                        <td>{{ $ingredient->name }}</td>
                                        <td>{{ $ingredient->category->name ?? '-' }}</td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($ingredient->stock_quantity, 2) }}</span>
                                        </td>
                                        <td>{{ number_format($ingredient->low_stock_threshold, 2) }}</td>
                                        <td>{{ $ingredient->unit }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $ingredient->id }}">
                                                <i class="fas fa-plus"></i> {{ __('Add Stock') }}
                                            </button>
                                            <a href="{{ route('purchasePage') }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-shopping-cart"></i> {{ __('Purchase') }}
                                            </a>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="adjustModal{{ $ingredient->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Add Stock') }}: {{ $ingredient->name }}</h5>
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
                                                            <label class="form-label">{{ __('Quantity to Add') }}</label>
                                                            <input type="number" name="adjustment" class="form-control" step="0.01" min="0.01" required>
                                                        </div>
                                                        <input type="hidden" name="type" value="add">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ __('All items are well stocked!') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
