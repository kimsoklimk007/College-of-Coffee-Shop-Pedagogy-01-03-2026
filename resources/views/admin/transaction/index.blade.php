@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Income & Expense Management') }}</h4>
                    <div>
                        <a href="{{ route('transaction.summary') }}" class="btn btn-info">
                            <i class="fas fa-chart-pie"></i> {{ __('Summary') }}
                        </a>
                        <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Add Transaction') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>{{ __('Net Profit') }}</h5>
                                    <h3>${{ number_format($totalIncome - $totalExpense, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="type" class="form-select">
                                    <option value="">{{ __('All Types') }}</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>{{ __('Income') }}</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>{{ __('Expense') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="{{ __('Start Date') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="{{ __('End Date') }}">
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
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Amount (USD)') }}</th>
                                    <th>{{ __('Amount (KHR)') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date }}</td>
                                    <td>
                                        @if($transaction->type === 'income')
                                            <span class="badge bg-success">{{ __('Income') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Expense') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                                    <td>${{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ number_format($transaction->amount_khr, 0) }} {{ __('KHR') }}</td>
                                    <td>{{ $transaction->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('transaction.edit', $transaction->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('transaction.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No transactions found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
