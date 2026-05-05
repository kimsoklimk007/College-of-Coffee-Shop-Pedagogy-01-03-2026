@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Stock History') }}: {{ $ingredient->name }}</h4>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Inventory') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>{{ __('Current Stock') }}</h6>
                                    <h4>{{ number_format($ingredient->stock_quantity, 2) }} {{ $ingredient->unit }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>{{ __('Cost Price') }}</h6>
                                    <h4>${{ number_format($ingredient->cost_price, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>{{ __('Low Stock Threshold') }}</h6>
                                    <h4>{{ number_format($ingredient->low_stock_threshold, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>{{ __('Total Value') }}</h6>
                                    <h4>${{ number_format($ingredient->stock_quantity * $ingredient->cost_price, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>{{ __('Purchase History') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Cost Price') }}</th>
                                    <th>{{ __('Total Price') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseItems as $item)
                                <tr>
                                    <td>{{ $item->purchase->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format($item->quantity, 2) }} {{ $ingredient->unit }}</td>
                                    <td>${{ number_format($item->cost_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ $item->purchase->supplier->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No purchase history') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $purchaseItems->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
