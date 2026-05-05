@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Financial Summary') }}</h4>
                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Transactions') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>{{ __('Total Income') }}</h5>
                                    <h3>${{ number_format($totalIncome, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>{{ __('Total Expense') }}</h5>
                                    <h3>${{ number_format($totalExpense, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-{{ $profit >= 0 ? 'primary' : 'warning' }} text-white">
                                <div class="card-body">
                                    <h5>{{ __('Net Profit') }}</h5>
                                    <h3>${{ number_format($profit, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5>{{ __('Income by Category') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($incomeByCategory->isNotEmpty())
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Category') }}</th>
                                                    <th class="text-end">{{ __('Amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($incomeByCategory as $item)
                                                <tr>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                                                    <td class="text-end">${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold">
                                                    <td>{{ __('Total') }}</td>
                                                    <td class="text-end">${{ number_format($totalIncome, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <p class="text-center">{{ __('No income data') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5>{{ __('Expense by Category') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($expenseByCategory->isNotEmpty())
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Category') }}</th>
                                                    <th class="text-end">{{ __('Amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expenseByCategory as $item)
                                                <tr>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                                                    <td class="text-end">${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold">
                                                    <td>{{ __('Total') }}</td>
                                                    <td class="text-end">${{ number_format($totalExpense, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <p class="text-center">{{ __('No expense data') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
