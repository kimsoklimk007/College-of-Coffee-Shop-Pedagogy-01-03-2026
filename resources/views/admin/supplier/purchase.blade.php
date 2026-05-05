@extends('admin.layouts.master')

@section('content')
<div class="container mt-3">
    <div class="card shadow-lg border-left-warning shadow rounded-4 p-4 text-black">
        <h2 class="mb-4 fw-bold text-center">{{ __('Add Purchase Info') }}</h2>

        <form action="{{ route('addItem') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Ingredient Name') }}</label>
                    <input type="text" name="ingredient_name" class="form-control rounded-3" placeholder="e.g. Sugar" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Unit') }}</label>
                    <select name="unit" class="form-select rounded-3" required>
                        <option value="kg">{{ __('Kg') }}</option>
                        <option value="package">{{ __('Package') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Quantity') }}</label>
                    <input type="number" name="quantity" class="form-control rounded-3" placeholder="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Cost Price') }}</label>
                    <input type="number" step="0.01" name="cost_price" class="form-control rounded-3" placeholder="0.00" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-3">{{ __('Add Item') }}</button>
                </div>
            </div>
        </form>

        <hr class="border-light mt-5 mb-4">

        <h4 class="mb-3 fw-bold">{{ __('Purchase Items') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-success">
                <thead class="table-light text-dark">
                    <tr>
                        <th>{{ __('Ingredient') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Cost Price') }}</th>
                        <th>{{ __('Total Price') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(session()->has('purchase_items'))
                        @foreach(session('purchase_items') as $index => $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['unit'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ $item['cost_price'] }}</td>
                                <td>{{ $item['total_price'] }}</td>
                                <td>
                                    <form action="{{ route('removeItem', $index) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3">{{ __('Remove') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('No items added yet.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <h4 class="fw-bold">{{ __('Purchase Information') }}</h4>
            <form action="{{ route('storePurchase') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Supplier') }}</label>
                        <select name="supplier_id" class="form-select rounded-3" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Total Amount') }}</label>
                                <input type="number" step="100" name="total_amount" class="form-control rounded-3" value="{{ session('total_amount', 0) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Paid Amount') }}</label>
                                <input type="number" step="100" name="paid_amount" class="form-control rounded-3" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">{{ __('Payment Status') }}</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input border-dark" type="radio" name="payment_status" value="Paid" required>
                            <label class="form-check-label">{{ __('Paid') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input border-dark" type="radio" name="payment_status" value="Partial">
                            <label class="form-check-label">{{ __('Partial') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input border-dark" type="radio" name="payment_status" value="Due">
                            <label class="form-check-label">{{ __('Due') }}</label>
                        </div>
                    </div>

                    <div class="col-12 text-start">
                        <button type="submit" class="btn btn-success rounded-3 mt-1 px-4">{{ __('Save Purchase') }}</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
